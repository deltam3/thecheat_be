<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService) 
    {
        $this->authService = $authService;
    }


    public function emailRegistration(Request $request) 
    {
        $message = $this->authService->emailRegistration($request);
        return $message;
    }

    public function emailLogin(Request $request) 
    {
        $message = $this->authService->emailLogin($request);
        return $message;
    }



    public function logout(Request $request) 
    {
        $message = $this->authService->logout($request);
        return response()->json($message);
    }

    
}
