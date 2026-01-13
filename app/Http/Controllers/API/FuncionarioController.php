<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Funcionario;
use App\Http\Requests\StoreFuncionarioRequest;
use App\Http\Requests\UpdateFuncionarioRequest;
use App\Http\Resources\FuncionarioResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FuncionarioController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/funcionarios",
     *      operationId="getFuncionariosList",
     *      tags={"Funcionários"},
     *      summary="Lista todos os funcionários",
     *      description="Retorna uma lista paginada de funcionários",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Operação realizada com sucesso"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Não autenticado"
     *      )
     * )
     *
     * Lista todos os funcionários com paginação.
     */
    public function index()
    {
        $funcionarios = Funcionario::paginate(15);
        return FuncionarioResource::collection($funcionarios);
    }

    /**
     * @OA\Post(
     *      path="/api/funcionarios",
     *      operationId="storeFuncionario",
     *      tags={"Funcionários"},
     *      summary="Cadastra um novo funcionário",
     *      description="Cria um novo registro de funcionário",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"nome","email","senha","cpf"},
     *                  @OA\Property(property="nome", type="string", example="João Funcionário"),
     *                  @OA\Property(property="email", type="string", format="email", example="func@example.com"),
     *                  @OA\Property(property="senha", type="string", format="password", example="senha123"),
     *                  @OA\Property(property="cpf", type="string", example="111.222.333-44"),
     *                  @OA\Property(property="documento", type="string", format="binary"),
     *                  @OA\Property(property="empresa_ids[]", type="array", @OA\Items(type="integer"))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Funcionário criado com sucesso"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erro de validação"
     *      )
     * )
     *
     * Cadastra um novo funcionário, tratando senha e upload de documento.
     */
    public function store(StoreFuncionarioRequest $request)
    {
        $data = $request->validated();
        $data['senha'] = Hash::make($data['senha']);

        if ($request->hasFile('documento')) {
            $path = $request->file('documento')->store('documentos', 'public');
            $data['documento_path'] = $path;
        }

        $funcionario = Funcionario::create($data);

        // Opcional: Vincular empresas na criação se enviado 'empresa_ids'
        if ($request->has('empresa_ids')) {
            $funcionario->empresas()->sync($request->input('empresa_ids'));
        }

        return response()->json([
            'message' => 'Funcionário criado com sucesso.',
            'data' => new FuncionarioResource($funcionario),
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *      path="/api/funcionarios/{id}",
     *      operationId="getFuncionarioById",
     *      tags={"Funcionários"},
     *      summary="Detalhes de um funcionário",
     *      description="Retorna os dados de um funcionário específico",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID do funcionário",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Operação realizada com sucesso"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Funcionário não encontrado"
     *      )
     * )
     *
     * Exibe os detalhes de um funcionário específico e suas empresas.
     */
    public function show($id)
    {
        $funcionario = Funcionario::with('empresas')->find($id);

        if (!$funcionario) {
            return response()->json(['error' => 'Funcionário não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return new FuncionarioResource($funcionario);
    }

    /**
     * @OA\Post(
     *      path="/api/funcionarios/{id}",
     *      operationId="updateFuncionario",
     *      tags={"Funcionários"},
     *      summary="Atualiza um funcionário",
     *      description="Atualiza os dados de um funcionário existente (Use POST com _method=PUT para upload)",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID do funcionário",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="_method", type="string", example="PUT"),
     *                  @OA\Property(property="nome", type="string", example="João Funcionário Alterado"),
     *                  @OA\Property(property="email", type="string", format="email", example="func@example.com"),
     *                  @OA\Property(property="cpf", type="string", example="111.222.333-44"),
     *                  @OA\Property(property="documento", type="string", format="binary"),
     *                  @OA\Property(property="empresa_ids[]", type="array", @OA\Items(type="integer"))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Funcionário atualizado com sucesso"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Funcionário não encontrado"
     *      )
     * )
     *
     * Atualiza os dados de um funcionário, tratando senha e documento se fornecidos.
     */
    public function update(UpdateFuncionarioRequest $request, $id)
    {
        $funcionario = Funcionario::find($id);

        if (!$funcionario) {
            return response()->json(['error' => 'Funcionário não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validated();

        if (isset($data['senha'])) {
            $data['senha'] = Hash::make($data['senha']);
        }

        if ($request->hasFile('documento')) {
            // Remove antigo se existir
            if ($funcionario->documento_path && Storage::disk('public')->exists($funcionario->documento_path)) {
                Storage::disk('public')->delete($funcionario->documento_path);
            }
            $path = $request->file('documento')->store('documentos', 'public');
            $data['documento_path'] = $path;
        }

        $funcionario->update($data);

        if ($request->has('empresa_ids')) {
            $funcionario->empresas()->sync($request->input('empresa_ids'));
        }

        return response()->json([
            'message' => 'Funcionário atualizado com sucesso.',
            'data' => new FuncionarioResource($funcionario),
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *      path="/api/funcionarios/{id}",
     *      operationId="deleteFuncionario",
     *      tags={"Funcionários"},
     *      summary="Remove um funcionário",
     *      description="Deleta o registro de um funcionário do sistema",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID do funcionário",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Funcionário removido com sucesso"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Funcionário não encontrado"
     *      )
     * )
     *
     * Remove um funcionário do sistema e deleta seu arquivo de documento.
     */
    public function destroy($id)
    {
        $funcionario = Funcionario::find($id);

        if (!$funcionario) {
            return response()->json(['error' => 'Funcionário não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        if ($funcionario->documento_path && Storage::disk('public')->exists($funcionario->documento_path)) {
            Storage::disk('public')->delete($funcionario->documento_path);
        }

        $funcionario->delete();

        return response()->json(['message' => 'Funcionário removido com sucesso.'], Response::HTTP_OK);
    }
}
