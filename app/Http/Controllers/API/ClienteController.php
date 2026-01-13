<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/clientes",
     *      operationId="getClientesList",
     *      tags={"Clientes"},
     *      summary="Lista todos os clientes",
     *      description="Retorna uma lista paginada de clientes",
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
     * Lista todos os clientes com paginação.
     */
    public function index()
    {
        $clientes = Cliente::paginate(15);
        return ClienteResource::collection($clientes);
    }

    /**
     * @OA\Post(
     *      path="/api/clientes",
     *      operationId="storeCliente",
     *      tags={"Clientes"},
     *      summary="Cadastra um novo cliente",
     *      description="Cria um novo registro de cliente",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"nome","email","senha","cpf"},
     *                  @OA\Property(property="nome", type="string", example="Empresa X"),
     *                  @OA\Property(property="email", type="string", format="email", example="cliente@example.com"),
     *                  @OA\Property(property="senha", type="string", format="password", example="senha123"),
     *                  @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *                  @OA\Property(property="documento", type="string", format="binary"),
     *                  @OA\Property(property="empresa_ids[]", type="array", @OA\Items(type="integer"))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Cliente criado com sucesso"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erro de validação"
     *      )
     * )
     *
     * Cadastra um novo cliente, tratando senha e upload de documento.
     */
    public function store(StoreClienteRequest $request)
    {
        $data = $request->validated();
        $data['senha'] = Hash::make($data['senha']);

        if ($request->hasFile('documento')) {
            $path = $request->file('documento')->store('documentos', 'public');
            $data['documento_path'] = $path;
        }

        $cliente = Cliente::create($data);

        if ($request->has('empresa_ids')) {
            $cliente->empresas()->sync($request->input('empresa_ids'));
        }

        return response()->json([
            'message' => 'Cliente criado com sucesso.',
            'data' => new ClienteResource($cliente),
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *      path="/api/clientes/{id}",
     *      operationId="getClienteById",
     *      tags={"Clientes"},
     *      summary="Detalhes de um cliente",
     *      description="Retorna os dados de um cliente específico",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID do cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Operação realizada com sucesso"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Cliente não encontrado"
     *      )
     * )
     *
     * Exibe os detalhes de um cliente específico e suas empresas vinculadas.
     */
    public function show($id)
    {
        $cliente = Cliente::with('empresas')->find($id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return new ClienteResource($cliente);
    }

    /**
     * @OA\Post(
     *      path="/api/clientes/{id}",
     *      operationId="updateCliente",
     *      tags={"Clientes"},
     *      summary="Atualiza um cliente",
     *      description="Atualiza os dados de um cliente existente (Use POST com _method=PUT para upload)",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID do cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="_method", type="string", example="PUT"),
     *                  @OA\Property(property="nome", type="string", example="Empresa X Alterada"),
     *                  @OA\Property(property="email", type="string", format="email", example="cliente@example.com"),
     *                  @OA\Property(property="cpf", type="string", example="123.456.789-00"),
     *                  @OA\Property(property="documento", type="string", format="binary"),
     *                  @OA\Property(property="empresa_ids[]", type="array", @OA\Items(type="integer"))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Cliente atualizado com sucesso"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Cliente não encontrado"
     *      )
     * )
     *
     * Atualiza os dados de um cliente, incluindo senha e documento se fornecidos.
     */
    public function update(UpdateClienteRequest $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validated();

        if (isset($data['senha'])) {
            $data['senha'] = Hash::make($data['senha']);
        }

        if ($request->hasFile('documento')) {
            if ($cliente->documento_path && Storage::disk('public')->exists($cliente->documento_path)) {
                Storage::disk('public')->delete($cliente->documento_path);
            }
            $path = $request->file('documento')->store('documentos', 'public');
            $data['documento_path'] = $path;
        }

        $cliente->update($data);

        if ($request->has('empresa_ids')) {
            $cliente->empresas()->sync($request->input('empresa_ids'));
        }

        return response()->json([
            'message' => 'Cliente atualizado com sucesso.',
            'data' => new ClienteResource($cliente),
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *      path="/api/clientes/{id}",
     *      operationId="deleteCliente",
     *      tags={"Clientes"},
     *      summary="Remove um cliente",
     *      description="Deleta o registro de um cliente do sistema",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID do cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Cliente removido com sucesso"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Cliente não encontrado"
     *      )
     * )
     *
     * Remove um cliente do sistema e deleta seu documento físico.
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        if ($cliente->documento_path && Storage::disk('public')->exists($cliente->documento_path)) {
            Storage::disk('public')->delete($cliente->documento_path);
        }

        $cliente->delete();

        return response()->json(['message' => 'Cliente removido com sucesso.'], Response::HTTP_OK);
    }
}
