<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFuncionarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'sometimes|string|max:255|regex:/^[a-zA-Z0-9]+$/|unique:funcionarios,login,' . $this->route('id'),
            'nome' => 'sometimes|string|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            'cpf' => 'sometimes|string|max:14|unique:funcionarios,cpf,' . $this->route('id'),
            'email' => 'sometimes|email|max:255|unique:funcionarios,email,' . $this->route('id'),
            'endereco' => 'sometimes|string|max:255',
            'senha' => 'sometimes|string|min:6',
            'documento' => 'sometimes|file|mimes:pdf,jpg,jpeg|max:2048',
        ];
    }
}
