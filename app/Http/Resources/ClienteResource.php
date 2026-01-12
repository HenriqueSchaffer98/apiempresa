<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ClienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'endereco' => $this->endereco,
            'documento_url' => $this->documento_path ? Storage::url($this->documento_path) : null,
            'empresas' => EmpresaResource::collection($this->whenLoaded('empresas')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
