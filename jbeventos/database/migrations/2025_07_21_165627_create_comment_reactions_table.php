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
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->OnDelete('cascade'); // Comentário que recebeu a reação
            $table->foreignId('user_id')->constrained()->OnDelete('cascade'); // Usuário que fez a reação
            $table->enum('type', ['like', 'dislike']); // Tipo de reação
            $table->timestamps();

            $table->unique(['user_id','comment_id']); // Garante que um usuário pode dar apenas uma reação por comentário
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};
