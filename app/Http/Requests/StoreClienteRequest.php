<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'required|string|unique:clientes,login|max:255',
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|unique:clientes,cpf|max:14',
            'email' => 'required|email|unique:clientes,email|max:255',
            'endereco' => 'required|string|max:255',
            'senha' => 'required|string|min:6',
            'documento' => 'required|file|mimes:pdf,jpg,jpeg|max:2048',
        ];
    }
}
