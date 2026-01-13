<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de clientes.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('login')->unique();
            $table->string('nome');
            $table->string('cpf')->unique();
            $table->string('email')->unique();
            $table->string('endereco');
            $table->string('senha');
            $table->string('documento_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Remove a tabela de clientes.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
