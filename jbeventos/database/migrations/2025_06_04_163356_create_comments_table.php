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
        // Criação da tabela 'comments' para armazenar comentários dos usuários sobre eventos
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // ID único do comentário

            $table->text('comment'); // Texto do comentário feito pelo usuário
            $table->boolean('visible_comment')->default(true); // Flag para indicar se o comentário está visível

            // Relacionamento com o usuário que fez o comentário
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Excluir comentário se usuário for deletado
            // Relacionamento com o evento comentado
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Excluir comentário se evento for deletado

            $table->timestamps(); // Controle de criação e atualização do registro
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
