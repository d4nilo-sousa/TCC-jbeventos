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
        // Criação da tabela 'course_user_follow' para registrar quais usuários seguem quais cursos
        Schema::create('course_user_follow', function (Blueprint $table) {
            $table->id(); // ID único do registro

            // Relacionamento com o curso seguido
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); 
            // Relacionamento com o usuário que está seguindo
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 

            $table->timestamps(); // Controle de criação e atualização do registro
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_user_follow');
    }
};
