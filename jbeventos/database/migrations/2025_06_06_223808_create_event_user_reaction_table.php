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
        // Criação da tabela pivot 'event_user_reaction' para armazenar reações dos usuários aos eventos
        Schema::create('event_user_reaction', function (Blueprint $table) {
            $table->id(); // ID único do registro

            // Tipo de reação (curtir, não curtir, salvar, notificar)
            $table->enum('reaction_type', ['like', 'dislike', 'save', 'notify']);

            // Chave estrangeira para o evento
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            // Chave estrangeira para o usuário
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps(); // Controle de criação e atualização do registro
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_user_reaction');
    }
};
