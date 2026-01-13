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
     * Lista todos os funcionários com paginação.
     */
    public function index()
    {
        $funcionarios = Funcionario::paginate(15);
        return FuncionarioResource::collection($funcionarios);
    }

    /**
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
