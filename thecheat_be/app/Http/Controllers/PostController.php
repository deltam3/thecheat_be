<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Services\S3Service;

class PostController extends Controller
{
    protected $postService;
    protected $S3Service;

    public function __construct(PostService $postService, S3Service $S3Service)
    {
        $this->postService = $postService;
        $this->S3Service = $S3Service;
    }

    public function index(Request $request) 
    {
        $message = $this->postService->index($request);
        return $message;
    }

    public function getPostsByCommunityId($communityId, Request $request)
    {
        $message = $this->postService->getPostsByCommunityId($communityId, $request);
        return $message;
    }

    public function getPostDetails($postId, Request $request)
    {
        $message = $this->postService->getPostDetails($postId, $request);
        return $message;
    }

    public function reportPost($postId, Request $request)
    {
        $message = $this->postService->reportPost($postId, $request);
        return $message;
    }

    public function storePostByCommunityId($communityId, Request $request)
    {
        $message = $this->postService->storePostByCommunityId($communityId, $request);
        return $message;
    }

    public function searchCommunity($communityId, $searchString)
    {
        $message = $this->postService->searchCommunity($communityId, $searchString);
        return $message;
    }

}
