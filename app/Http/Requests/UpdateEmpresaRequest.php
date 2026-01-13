<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'sometimes|string|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            'cnpj' => 'sometimes|string|max:18|unique:empresas,cnpj,' . $this->route('id'),
            'endereco' => 'sometimes|string|max:255',
        ];
    }
}
