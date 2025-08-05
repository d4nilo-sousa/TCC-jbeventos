<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migration para criar a tabela 'comments'.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // ID único do comentário

            $table->text('comment'); // Conteúdo textual do comentário
            $table->boolean('visible_comment')->default(true); // Indica se o comentário está visível

            // Relacionamento com o usuário que realizou o comentário
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Relacionamento com o evento ao qual o comentário pertence
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverte a migration, removendo a tabela 'comments'.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};