<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa um funcionário no sistema.
 */
class Funcionario extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'login',
        'nome',
        'cpf',
        'email',
        'endereco',
        'senha',
        'documento_path',
    ];

    /**
     * Campos que não devem ser exibidos em respostas da API.
     */
    protected $hidden = [
        'senha',
    ];

    /**
     * Empresas onde este funcionário trabalha.
     */
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_funcionario');
    }
}
