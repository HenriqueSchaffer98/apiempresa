<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\{Request, Response};

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registra um novo usuário.
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token');

        return response()->json([
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user
        ], Response::HTTP_CREATED);
    }

    /**
     * Autentica um usuário e gera um token
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('auth-token');

            return [
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'user' => $request->user()
            ];
        }

        return response()->json([
            'error' => 'As credenciais fornecidas estão incorretas.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
