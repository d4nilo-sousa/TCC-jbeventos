<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação da tabela 'coordinators'.
 * Essa tabela armazena os coordenadores do sistema,
 * indicando o tipo de coordenador e a associação com o usuário.
 */
return new class extends Migration
{
    /**
     * Executa as migrations, criando a tabela 'coordinators'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('coordinators', function (Blueprint $table) {
            $table->id(); // ID único auto-incrementado do coordenador

            // Define o tipo de coordenador: geral ou de curso
            $table->enum('coordinator_type', ['general', 'course']);

            // Indica se o coordenador possui senha temporária (default: true)
            $table->boolean('temporary_password')->default(true);

            // FK para o usuário associado; ao deletar usuário, o coordenador é removido em cascata
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Campos timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverte as migrations, removendo a tabela 'coordinators'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinators');
    }
};