<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'login',
        'nome',
        'cpf',
        'email',
        'endereco',
        'senha',
        'documento_path',
    ];

    protected $hidden = [
        'senha',
    ];

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_cliente');
    }
}
