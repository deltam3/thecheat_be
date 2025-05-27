<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService) 
    {
        $this->userService = $userService;
    }

    public function unregister(Request $request)
    {
        $user = auth()->user();  

        try {
            $this->userService->softDeleteUser($user->id);
            auth()->user()->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => '유저 정보 삭제 성공적'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => '유저가 발견되지 않았습니다.'], 404);
        }
    }
}