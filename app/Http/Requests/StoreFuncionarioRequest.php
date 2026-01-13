<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuncionarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'required|string|unique:funcionarios,login|max:255|regex:/^[a-zA-Z0-9]+$/',
            'nome' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            'cpf' => 'required|string|unique:funcionarios,cpf|max:14',
            'email' => 'required|email|unique:funcionarios,email|max:255',
            'endereco' => 'required|string|max:255',
            'senha' => 'required|string|min:6',
            'documento' => 'required|file|mimes:pdf,jpg,jpeg|max:2048', // 2MB Max
        ];
    }
}
