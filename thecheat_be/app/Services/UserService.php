<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
class UserService
{


  public function getUserProfile(Request $request)
  {

      $user = auth()->user();  

      if (!$user) {
          return response()->json([
              'error' => 'User not found',

          ], 404);
      }

      $userWithProfile = User::with('profile')->find($user->id);

      if (!$userWithProfile->profile) {
          return response()->json([
              'user' => $userWithProfile,
              'profile' => null
          ], 200);
      }

      return response()->json([
          'user' => $userWithProfile,
          'profile' => $userWithProfile->profile,
      ], 200);
  }

  public function logoutUser(Request $request)
  {
    $user = auth()->user();  
    auth()->user()->tokens->each(function ($token) {
      $token->delete();
    });


      return response()->json([
          'message' => 'User logged out successfully',
      ], 200)
      ->cookie('isAuthenticated', 'false', -1)  
      ->cookie('access_token', '', -1)           
      ;
  }

  public function updateUser($id, array $data)
  {
    $user = User::find($id);
    if ($user) {
      $user->update($data);
      return $user;
    }
    return null;
  }

  public function softDeleteUser(int $userId): bool
  {
      $user = User::find($userId);

      if (!$user) {
          throw new ModelNotFoundException('해당 유저가 없습니다.');
      }

      return $user->delete();
  }

}