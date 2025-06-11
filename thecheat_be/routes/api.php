<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ScamReportsController;


Route::prefix('v1')->group(function () {
    Route::get('/', function (Request $request) {
        return response()->json(['message' => 'API v1 test endpoint']);
    });
});

// Route::middleware('throttle:15,1')->group(function () {
Route::prefix('v1')->middleware('throttle:15,1')->group(function () {
    Route::post('/auth/emailRegistration', [AuthController::class, 'emailRegistration']);
    Route::post('/auth/emailLogin', [AuthController::class, 'emailLogin'])->name('login');
    
    // 새글피드
    Route::get('/posts', [PostController::class, 'index']);
    // 게시판마다 글들 가져오기
    Route::get('/posts/{communityId}', [PostController::class, 'getPostsByCommunityId']);
    Route::get('/posts/details/{postId}', [PostController::class, 'getPostDetails'] );
    Route::get('/posts/{communityId}/{searchString}', [PostController::class, 'searchCommunity']);
    
    //피해사례 등록
    Route::post('/scamreports', [ScamReportsController::class, 'postScamReport']);




});


// Route::middleware('auth:sanctum')->group(function () {
// Route::middleware(['auth:sanctum', 'throttle:15,1'])->group(function () {
// Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:15,1'])->group(function () {
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
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

    // 피해 사례 검색
    Route::get('/scamreports/', [ScamReportsController::class, 'searchScamReports']);

    // 자신의 프로필 사진, 유저네임, 인사말 가져오는 프로필 페이지
    Route::get('auth/profile', [AuthController::class, 'getProfile']);
});


use App\Jobs\DummyJob;
Route::post('/dispatch-dummy-job', function (Request $request) {
    $message = $request->input('message', '큐작동테스트성공!');
        
    DummyJob::dispatch($message);

    return response()->json(['status' => '잡 디스패치 성공']);
});
