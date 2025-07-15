<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criação da tabela 'coordinators' para armazenar coordenadores do sistema
        Schema::create('coordinators', function (Blueprint $table) {
            $table->id(); // ID único do coordenador

            // Tipo de coordenador: 'general' (geral) ou 'course' (curso)
            $table->enum('coordinator_type', ['general', 'course']);

            // Flag para indicar se o coordenador será cadastrado com uma senha provisória
            $table->boolean('temporary_password')->default(true);

            // Chave estrangeira referenciando o usuário associado, com exclusão em cascata
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps(); // Controle automático de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinators');
    }
};
