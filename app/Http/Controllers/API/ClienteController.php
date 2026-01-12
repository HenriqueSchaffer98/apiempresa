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
    public function index()
    {
        $clientes = Cliente::paginate(15);
        return ClienteResource::collection($clientes);
    }

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

    public function show($id)
    {
        $cliente = Cliente::with('empresas')->find($id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return new ClienteResource($cliente);
    }

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
