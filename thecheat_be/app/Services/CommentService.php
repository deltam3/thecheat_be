<?php
namespace App\Services;


use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CommentImage;




class CommentService
{
  public function storeCommentByPost($postId, Request $request)
  {    
  $validated = $request->validate([
      'content' => 'required|string',
      'images' => 'nullable|array|max:5', 
      'images.*' => 'nullable|image|max:2048', 
  ]);

  $user = Auth::user();
    
  if (!$user) {
      return response()->json(['error' => 'Unauthorized'], 401);
  }

  $post = Post::find($postId);
  if (!$post) {
      return response()->json(['error' => '글이 없습니다.'], 404);
  }

  
  $userProfile = $user->profile; 
  
  $comment = Comment::create([
      'post_id' => $postId,
      'user_id' => $user->id, 
      'content' => $validated['content'],
      'username' => $user->username, 
      'profile_image' => $userProfile ? $userProfile->profile_image : null, 
  ]);

  if ($request->hasFile('images')) {
      $images = $request->file('images');
      $imageOrder = 0;

      foreach ($images as $image) {
          $imagePath = $image->store('comment_images', 'public');  
          $imageUrl = asset('storage/' . $imagePath);  

          
          CommentImage::create([
              'comment_id' => $comment->id,
              'post_id' => $post->id,
              'image_url' => $imageUrl,
              'image_order' => $imageOrder++,
          ]);          
      }
    }
    return response()->json($comment, 201);
  }

  public function storeNestedComment($commentId, Request $request)
  {
      $user = Auth::user();
      
      $validatedData = $request->validate([
          'content' => 'required|string|min:1|max:1000',        
      ]);
  
      $parentComment = Comment::find($commentId);
      
      if (!$parentComment) {
          return response()->json([
              'message' => '부모 댓글이 없습니다.',
          ], 404);
      }
      $userProfile = $user->profile;     
      $nestedComment = new Comment();
      $nestedComment->post_id = $parentComment->post_id; 
      $nestedComment->user_id = $user->id; 
      $nestedComment->username = $user->username;
      $nestedComment->parent_comment_id = $parentComment->id; 
      $nestedComment->content = $validatedData['content']; 
      $nestedComment->profile_image = $userProfile ? $userProfile->profile_image : null;
      $nestedComment->save(); 
       
      return response()->json([
          'message' => '대댓글 성공적 추가',
          'nested_comment' => $nestedComment,
      ], 201);
  }

}