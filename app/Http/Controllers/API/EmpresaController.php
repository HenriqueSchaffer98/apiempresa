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
     *      summary="Get list of registered companies",
     *      description="Returns list of companies",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        $empresas = Empresa::paginate(15);
        return EmpresaResource::collection($empresas);
    }

    public function store(StoreEmpresaRequest $request)
    {
        $empresa = Empresa::create($request->validated());

        return response()->json([
            'message' => 'Empresa criada com sucesso.',
            'data' => new EmpresaResource($empresa),
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $empresa = Empresa::with(['funcionarios', 'clientes'])->find($id);

        if (!$empresa) {
            return response()->json(['error' => 'Empresa não encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return new EmpresaResource($empresa);
    }

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
