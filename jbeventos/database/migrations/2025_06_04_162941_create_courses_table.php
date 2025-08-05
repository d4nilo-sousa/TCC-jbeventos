<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação da tabela 'courses'.
 * Esta tabela armazena os cursos, seus detalhes e relacionamentos com usuários e coordenadores.
 */
return new class extends Migration
{
    /**
     * Executa a migration, criando a tabela 'courses'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); // ID único do curso

            $table->string('course_name')->unique(); // Nome do curso, único
            $table->text('course_description')->nullable(); // Descrição detalhada do curso (opcional)
            $table->string('course_icon')->nullable(); // Ícone do curso (opcional)
            $table->string('course_banner')->nullable(); // Banner do curso (opcional)

            // Chave estrangeira para o usuário criador do curso
            // Pode ser nulo e, se o usuário for deletado, seta a coluna como null
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Chave estrangeira para o coordenador do curso
            // Pode ser nulo e, se o coordenador for deletado, seta a coluna como null
            $table->foreignId('coordinator_id')->nullable()->constrained('coordinators')->onDelete('set null');

            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverte a migration, removendo a tabela 'courses'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};