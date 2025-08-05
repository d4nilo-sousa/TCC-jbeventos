<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação da tabela 'categories'.
 * Essa tabela armazena as categorias disponíveis no sistema.
 */
return new class extends Migration
{
    /**
     * Executa as migrations, criando a tabela 'categories'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // ID único auto-incrementado da categoria
            $table->string('category_name')->unique(); // Nome da categoria, único para evitar duplicidade
            $table->timestamps(); // Colunas created_at e updated_at automáticas
        });
    }

    /**
     * Reverte as migrations, removendo a tabela 'categories'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};