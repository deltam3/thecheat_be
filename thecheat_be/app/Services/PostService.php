<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;

use App\Jobs\AddNumbersJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Post;
use App\Models\Comment;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\PinnedPost;
use App\Models\PostReport;
use App\Models\PostView;
use App\Models\UserPoint;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use App\Jobs\UploadImagesJob;
use App\Services\S3Service;
class PostService
{
   protected $s3Service;


   public function __construct(S3Service $s3Service)
  {
    $this->s3Service = $s3Service;
  }
  public function index(Request $request)
  {
    $page = $request->query('page', 1);

    if ($page == 1) {
        $cacheKey = 'posts_index_page_1'; 

        $posts = Cache::remember($cacheKey, 30, function () {
            return Post::with([
                'images',
                'comments' => function ($query) {
                    $query->whereNull('parent_comment_id')
                          ->latest()
                          ->take(3);
                }
            ])
            ->withCount('comments')
            ->where('is_flagged', false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        });
    } else {
        $posts = Post::with([
            'images',
            'comments' => function ($query) {
                $query->whereNull('parent_comment_id')
                      ->latest()
                      ->take(3);
            }
        ])
        ->withCount('comments')
        ->where('is_flagged', false)
        ->orderBy('created_at', 'desc')
        ->paginate(20);
    }

    return response()->json([
        'status' => 'success',
        'posts' => $posts,
    ]);
  }

  public function getPostsByCommunityId($communityId, Request $request)
  {
    $page = $request->query('page', 1);

    $pinnedPostIds = PinnedPost::where('community_id', $communityId)
        ->pluck('post_id');


    $pinnedPosts = collect(); 

    if ((int)$page === 1) {
        $pinnedPosts = Post::with('user', 'images')
            ->whereIn('id', $pinnedPostIds)
            ->orderByDesc('created_at')
            ->get();
    }

    $posts = Post::with([
      'images', 
      'comments' => function ($query) {
          $query->whereNull('parent_comment_id')
              ->latest()
              ->take(3);
      }
    ])
    ->withCount('comments') 
    ->where('is_flagged', false) 
    ->where('community_id', $communityId) 
    ->orderBy('created_at', 'desc') 
    ->paginate(20); 

  
    return response()->json([
        'status' => '성공',
        'pinned_posts' => $pinnedPosts,
        'posts' => $posts,
    ]);
    return response()->json([
      'status' => '성공',
      'pinned_posts' => $pinnedPosts,
      'posts' => $posts->items(),  
      'pagination' => [
          'current_page' => $posts->currentPage(),
          'total_pages' => $posts->lastPage(),
          'total_items' => $posts->total(),
      ]
  ]);
  }

  public function getPostDetails($postId)
  {
    try {
        $token = request()->bearerToken();

        if (!$token) {
            $post = Post::with('comments', 'postImages')->find($postId); 
            $post->increment('view_count');

            $comments = Comment::where('post_id', $postId)
            ->whereNull('deleted_at') 
            ->orderBy('created_at', 'desc')
            ->with('commentImages')
            ->get();


            return response()->json([
                'post' => $post,
                'comments' => $comments,
                'images' => $post->postImages,
            ], 200);
        }

        $personalAccessToken = PersonalAccessToken::findToken($token);
        $user = $personalAccessToken ? $personalAccessToken->tokenable : null;

        $post = Post::with('comments', 'postImages')->find($postId); 

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $existingView = PostView::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        $post->increment('view_count');

        if (!$existingView) {
            DB::beginTransaction();

            try {
                PostView::create([
                    'user_id' => $user->id,
                    'post_id' => $postId,
                    'viewed_at' => now(),
                ]);

                UserPoint::create([
                    'user_id' => $user->id,
                    'points' => 1,
                    'activity_type' => 'read',
                    'activity_reference_id' => $postId,
                    'created_at' => now(),
                ]);

                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();

                return response()->json([
                    'error' => 'Server error while adding view or points',
                    'details' => $e->getMessage(),
                ], 500);
            }
        }
        $comments = Comment::where('post_id', $postId)
        ->whereNull('deleted_at') 
        ->orderBy('created_at', 'desc')
        ->with('commentImages')
        ->get();

        return response()->json([
            'post' => $post,
            'comments' => $comments,
            'images' => $post->postImages, 
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Server error',
            'details' => $e->getMessage(),
        ], 500);
    }
  }
  public function storePostByCommunityId($communityId, Request $request)
  {
    try {
          $user = $request->user();

          if (!$user) {
              return response()->json([
                  'error' => 'Unauthenticated',
                  'user' => $user,
              ], 401);
          }

          $validated = $request->validate([
              'title' => 'required|string|max:255',
              'content' => 'required|string',
              'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
              'police_station_name' => 'nullable|string|max:255',
              'victim_site_url' => 'nullable|url|max:255', 
          ]);


          $post = Post::create([
              'user_id' => $user->id,
              'community_id' => $communityId, 
              'title' => $validated['title'],
              'content' => $validated['content'],
              'police_station_name' => $request->input('police_station_name', null), 
              'victim_site_url' => $request->input('victim_site_url', null),
          ]);


          $thumbnailImageUrl = null; 

          if ($request->hasFile('images')) {
              $images = $request->file('images');
              $imageOrder = 0;

              foreach ($images as $image) {
                  $imagePath = $image->store('post_images', 'public'); 
                  $imageUrl = asset('storage/' . $imagePath);  

        //  $imagePath = $image->store('post_images', 's3');  
        //  $imageUrl = Storage::disk('s3')->url($imagePath);

                  PostImage::create([
                      'post_id' => $post->id,
                      'image_url' => $imageUrl,
                      'image_order' => $imageOrder++,
                  ]);
                  $filename = $image->getClientOriginalName();
                  $contents = file_get_contents($image->getRealPath());
                  $this->s3Service->storeFile($filename, $contents);
                  
                //   return redirect()->back()->with('status', 'File uploaded successfully to S3!');
      
                  if ($imageOrder === 1) {
                      $thumbnailImageUrl = $imageUrl;
                  }
              }
          }

          if ($thumbnailImageUrl) {
              $post->update([
                  'thumbnail_image' => $thumbnailImageUrl,
              ]);
          }

          return response()->json(['message' => 'Post created', 'post' => $post], 201);

    } catch (\Exception $e) {
          return response()->json([
              'error' => 'Server Error',
              'details' => $e->getMessage(),
          ], 500);
    }
  }

  public function reportPost($postId, Request $request) 
  {
      $validatedData = $request->validate([
          'comment' => 'nullable|string|max:1000',  
      ]);

      $post = Post::find($postId);
      if (!$post) {
          return response()->json(['message' => '게시글 없음'], 404);
      }

      $report = PostReport::create([
          'post_id' => $postId,
          'reported_by_user_id' => Auth::id(),  
          'comment' => $validatedData['comment'] ?? null,  
      ]);

      $post->update(['is_flagged' => true]);


      return response()->json([
          'message' => '게시글 신고 성공.',
          'report' => $report,
      ]);
  }

  public function searchCommunity($communityId, $searchString)
  { 
    if ($communityId === 'all') { 
      $posts = Post::where('title', 'like', '%' . $searchString . '%')
        ->orWhere('content', 'like', '%' . $searchString . '%')
        ->get();
      } else {  
        $posts = Post::where('community_id', $communityId)
          ->where(function($query) use ($searchString) {
            $query->where('title', 'like', '%' . $searchString . '%')
          ->orWhere('content', 'like', '%' . $searchString . '%');
        })->get();
      }
      return response()->json([
          'posts' => $posts,
      ], 200);
  }
}