<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\{AuthController, EmpresaController, FuncionarioController, ClienteController};

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar as rotas da API para sua aplicação.
| Essas rotas são carregadas pelo RouteServiceProvider e todas elas
| serão atribuídas ao grupo de middleware "api".
|
*/

/**
 * Rotas públicas
 */
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

/**
 * Rotas autenticadas
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('empresas', EmpresaController::class);
    Route::apiResource('funcionarios', FuncionarioController::class);
    Route::apiResource('clientes', ClienteController::class);
});
