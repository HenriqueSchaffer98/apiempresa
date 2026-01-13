<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma empresa no sistema.
 */
class Empresa extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'nome',
        'cnpj',
        'endereco',
    ];

    /**
     * Funcionários vinculados a esta empresa.
     */
    public function funcionarios()
    {
        return $this->belongsToMany(Funcionario::class, 'empresa_funcionario');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'empresa_cliente');
    }
}
