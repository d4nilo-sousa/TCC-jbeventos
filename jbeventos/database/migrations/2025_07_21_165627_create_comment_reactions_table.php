<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criar a tabela 'comment_reactions' que armazena reações dos usuários aos comentários.
 */
return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Cria a tabela 'comment_reactions' com as colunas:
     * - id: identificador único da reação.
     * - comment_id: referência ao comentário que recebeu a reação, com exclusão em cascata.
     * - user_id: referência ao usuário que fez a reação, com exclusão em cascata.
     * - type: tipo da reação, podendo ser 'like' ou 'dislike'.
     * - timestamps: para controle de criação e atualização.
     *
     * Define uma restrição única para que um usuário possa reagir apenas uma vez por comentário.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->onDelete('cascade'); // Comentário que recebeu a reação
            $table->foreignId('user_id')->constrained()->onDelete('cascade');    // Usuário que fez a reação
            $table->enum('type', ['like', 'dislike']);                            // Tipo de reação
            $table->timestamps();

            $table->unique(['user_id', 'comment_id']); // Garante uma reação por usuário por comentário
        });
    }

    /**
     * Reverte as migrações.
     *
     * Remove a tabela 'comment_reactions'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};