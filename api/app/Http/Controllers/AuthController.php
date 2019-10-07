<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct()
    {
        auth()->setDefaultDriver('api');
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
        $user = user::where([
            ["email", "=", $credentials["email"] ?? ""], 
            ["state", "=", user::AUTHENTICATED]
        ])->first();

        if ( ! ($user and $token = auth()->login($user) ) ) {
            return response()->json(['error' => 'Unauthorized',], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
