<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


use Intervention\Image\Laravel\Facades\Image;
use App\Jobs\ResizeProfileImage;

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

      $encodedToken = urlencode($token); // URL encode the token
      return response()->json([
          'message' => '회원가입 성공',
          'token' => $token,
      ], 201)
    //   ->cookie('isAuthenticated', 'true', 5256000, '*', true, false, false, 'None');
      ->cookie('isAuthenticated', 'true', 5256000, '/', '.thecheat.vercel.app', true, false, false, 'None')
      ->cookie('access_token', $encodedToken, $fiveYears, null, null, true, true, false, 'Lax');

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


    public function emailRegistrationOptional(Request $request)
    {
        try {
            $request->validate([
                'intro_text' => 'nullable|string|max:100',
                'profile_image' => 'nullable|image|max:5120',
            ]);

            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);

            if ($request->hasFile('profile_image')) {
                // $upload = $request->file('profile_image');
                // $imageName = $user->id . '.' . $upload->getClientOriginalExtension();
                // Storage::disk('public')->putFileAs('profiles', $upload, $imageName);
                // $userProfile->profile_image = "profiles/{$imageName}";

                $uploadedImage = $request->file('profile_image');
                $image = Image::read($uploadedImage)->resize(300, 200);
                $imageName = $user->id . '.' . $uploadedImage->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('profiles', $image, $imageName);
                $userProfile->profile_image = "profiles/{$imageName}";

            }

            $userProfile->intro_text = $request->input('intro_text', null);
            $userProfile->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user_profile' => $userProfile,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // public function emailRegistrationOptional(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'intro_text' => 'nullable|string|max:100',
    //             'profile_image' => 'nullable|image|max:5120',
    //         ]);
    
    //         $user = auth()->user();
    
    //         if (!$user) {
    //             return response()->json(['error' => 'User not authenticated'], 401);
    //         }
    
    //         $userProfile = UserProfile::firstOrCreate(['user_id' => $user->id]);
    
    //         if ($request->hasFile('profile_image')) {
    //             $upload = $request->file('profile_image');
    //             $imageName = $user->id . '.' . $upload->getClientOriginalExtension();
    //             $imagePath = 'profiles/' . $imageName;
    
    //             
    //             Storage::disk('public')->putFileAs('profiles', $upload, $imageName);
    //             $userProfile->profile_image = $imagePath;
    
    //             
    //             ResizeProfileImage::dispatch($userProfile, $imagePath);
    //         }
    
    //         $userProfile->intro_text = $request->input('intro_text', null);
    //         $userProfile->save();
    
    //         return response()->json([
    //             'message' => 'Profile updated successfully',
    //             'user_profile' => $userProfile,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

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
          $encodedToken = urlencode($token);
          return response()->json([
              'message' => '로그인 성공',
              'token' => $token,
            //   'user' => $user,
          ], 200)
          ->cookie('isAuthenticated', 'true', 5256000, '/', 'thecheat.vercel.app', false, false, false, 'None')
          ->cookie('access_token', $encodedToken, 525600, null, null, true, true, false, 'Lax');

  
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
    ])->cookie('isAuthenticated', '', -1)->cookie('access_token', '', -1); 
  }


}
