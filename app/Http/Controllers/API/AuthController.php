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
     * @OA\Post(
     *      path="/api/register",
     *      operationId="register",
     *      tags={"Autenticação"},
     *      summary="Registra um novo usuário",
     *      description="Cria uma nova conta de usuário e retorna um token de acesso",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", example="João Silva"),
     *              @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="senha123"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="senha123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuário registrado com sucesso"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erro de validação"
     *      )
     * )
     *
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
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Autenticação"},
     *      summary="Autentica um usuário",
     *      description="Valida as credenciais e retorna um token de acesso",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login realizado com sucesso"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Credenciais inválidas"
     *      )
     * )
     *
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
