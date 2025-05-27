<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{

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