<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CommentService;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function storedCommentByPost($postId, Request $request)
    {
        $message = $this->commentService->storeCommentByPost($postId, $request);
        return $message;
    }

    public function storedNestedComment($commentId, Request $request)
    {
        $message = $this->commentService->storeNestedComment($commentId, $request);
        return $message;
    }
}
