<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/', function (Request $request) {
    return response()->json(['message' => 'hello']);
})->middleware('auth:sanctum');

Route::post('/auth/emailRegistration', [AuthController::class, 'emailRegistration']);
Route::post('/auth/emailLogin', [AuthController::class, 'emailLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::delete('/user/unregister', [UserController::class, 'unregister']);
});


