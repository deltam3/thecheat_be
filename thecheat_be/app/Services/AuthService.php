<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthService
{


  public function emailRegistration(Request $request) 
  {

    try {
      $request->validate([
          'name' => 'required|string|min:2|max:20',
          'email' => 'required|email|unique:users',
          'password' => 'required|min:10|max:20',
          'username' => 'required|string|min:1',
          'phone_number' => 'required|phone:KR|unique:users',
      ]);


      $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => Hash::make($request->password),  
          'username' => $request->username,
          'phone_number' => $request->phone_number,
      ]);

      $token = $user->createToken('access_token')->plainTextToken;
      $fiveYears = 60 * 24 * 365 * 5;


      return response()->json([
          'message' => '회원가입 성공',
          'token' => $token,
      ], 201)->cookie('isAuthenticated', 'true', 5256000, null, null, false, false)->cookie( 'access_token', $token, $fiveYears, null, null, true, true, false, 'Lax');
  } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
          'message' => 'Validation 실패',
          'errors' => $e->errors(),
      ], 422);

  } catch (\Exception $e) {
      return response()->json([
          'message' => '오류 발생',
          'error' => $e->getMessage(),
      ], 500);
  }
  }
  public function emailLogin(Request $request)
  {
      try {
          $request->validate([
              'email' => 'required|email',
              'password' => 'required|min:10|max:20',
          ]);
  
          $user = User::where('email', $request->email)->first();
  
          if (!$user || !Hash::check($request->password, $user->password)) {
              return response()->json([
                  'message' => '틀린 정보입니다.',
              ], 401);
          }
  
          $token = $user->createToken('access_token')->plainTextToken;

          return response()->json([
              'message' => '로그인 성공',
              'token' => $token,
            //   'user' => $user,
          ], 200)->cookie('isAuthenticated', 'true', 5256000, null, null, false, false)->cookie( 'access_token', $token, 60 * 24 * 365 * 5, null, null, true, true, false, 'Lax');;
  
      } catch (\Illuminate\Validation\ValidationException $e) {
          return response()->json([
              'message' => 'Validation 실패',
              'errors' => $e->errors(),
          ], 422);
  
      } catch (\Exception $e) {
          return response()->json([
              'message' => '로그인 에러',
              'error' => $e->getMessage(),
          ], 500);
      }
  }
  
  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => '성공적 로그아웃'
    ])->cookie('isAuthenticated', '', -1); 
  }


}