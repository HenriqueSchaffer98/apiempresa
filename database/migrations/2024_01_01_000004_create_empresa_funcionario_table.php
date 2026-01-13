<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de relacionamento entre empresas e funcionários.
     */
    public function up(): void
    {
        Schema::create('empresa_funcionario', function (Blueprint $table) {
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('funcionario_id')->constrained('funcionarios')->onDelete('cascade');
            $table->primary(['empresa_id', 'funcionario_id']);
            $table->timestamps();
        });
    }

    /**
     * Remove a tabela de relacionamento entre empresas e funcionários.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_funcionario');
    }
};
