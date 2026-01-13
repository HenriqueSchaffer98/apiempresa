<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Http\Requests\StoreEmpresaRequest;
use App\Http\Requests\UpdateEmpresaRequest;
use App\Http\Resources\EmpresaResource;
use Illuminate\Http\Response;

class EmpresaController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/empresas",
     *      operationId="getEmpresasList",
     *      tags={"Empresas"},
     *      summary="Obtém a lista de empresas cadastradas",
     *      description="Retorna uma lista paginada de empresas",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Operação realizada com sucesso"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Não autenticado"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Acesso proibido"
     *      )
     * )
     *
     * Lista todas as empresas com paginação.
     */
    public function index()
    {
        $empresas = Empresa::paginate(15);
        return EmpresaResource::collection($empresas);
    }

    /**
     * Cadastra uma nova empresa.
     */
    public function store(StoreEmpresaRequest $request)
    {
        $empresa = Empresa::create($request->validated());

        return response()->json([
            'message' => 'Empresa criada com sucesso.',
            'data' => new EmpresaResource($empresa),
        ], Response::HTTP_CREATED);
    }

    /**
     * Exibe os detalhes de uma empresa específica, incluindo funcionários e clientes.
     */
    public function show($id)
    {
        $empresa = Empresa::with(['funcionarios', 'clientes'])->find($id);

        if (!$empresa) {
            return response()->json(['error' => 'Empresa não encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return new EmpresaResource($empresa);
    }

    /**
     * Atualiza os dados de uma empresa existente.
     */
    public function update(UpdateEmpresaRequest $request, $id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['error' => 'Empresa não encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $empresa->update($request->validated());

        return response()->json([
            'message' => 'Empresa atualizada com sucesso.',
            'data' => new EmpresaResource($empresa),
        ], Response::HTTP_OK);
    }

    /**
     * Remove uma empresa do sistema.
     */
    public function destroy($id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['error' => 'Empresa não encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $empresa->delete();

        return response()->json(['message' => 'Empresa removida com sucesso.'], Response::HTTP_OK);
    }
}
