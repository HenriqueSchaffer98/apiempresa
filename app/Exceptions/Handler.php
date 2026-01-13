<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Lista de campos que não devem ser salvos na sessão em erros de validação.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra os retornos de erro personalizados da aplicação.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Acesso negado. Token não fornecido ou inválido.',
                ], 401);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Recurso não encontrado.',
                ], 404);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Você não tem permissão para realizar esta ação.',
                ], 403);
            }
        });
    }
}
