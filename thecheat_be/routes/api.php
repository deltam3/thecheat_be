<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;

Route::get('/', function (Request $request) {
    return 'hello';
});

Route::post('/auth/emailRegistration', [AuthController::class, 'emailRegistration']);
Route::post('/auth/emailLogin', [AuthController::class, 'emailLogin'])->name('login');


Route::group([''], function () {
    // 새글피드
    Route::get('/posts', [PostController::class, 'index']);
    // 게시판마다 글들 가져오기
    Route::get('/posts/{communityId}', [PostController::class, 'getPostsByCommunityId']);
    Route::get('/posts/details/{postId}', [PostController::class, 'getPostDetails'] );
    Route::post('/posts/{communityId}/{searchString}', [PostController::class, 'searchCommunity']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/emailRegistration/optional', [AuthController::class, 'emailRegistrationOptional']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::delete('/user/unregister', [UserController::class, 'unregister']);

    // 게시글 추가
    Route::post('/post/{communityId}', [PostController::class, 'storePostByCommunityId']);
        // 게시글 신고
    Route::post('/post/report/{postId}', [PostController::class, 'reportPost']);

    // 댓글 추가
    Route::post('/post/{postId}/comments', [CommentController::class, 'storedCommentByPost']);
        // 대댓글 추가
    Route::post('/comments/{commentId}', [CommentController::class, 'storedNestedComment']);

    // 유저 모든 정보 불러오기 마이페이지 전용
    Route::get('/user/profile', [UserController::class, 'getUserProfile']);
    Route::post('/user/logout', [UserController::class, 'logoutUser']);
});
