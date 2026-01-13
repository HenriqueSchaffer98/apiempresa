<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa um cliente no sistema.
 */
class Cliente extends Model
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
     * Empresas vinculadas a este cliente.
     */
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_cliente');
    }
}
