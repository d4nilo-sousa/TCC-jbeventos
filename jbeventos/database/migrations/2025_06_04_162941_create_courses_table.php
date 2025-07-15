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
        // Criação da tabela 'courses' com informações do curso e relacionamentos
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); // ID único do curso

            $table->string('course_name')->unique(); // Nome único do curso
            $table->text('course_description')->nullable(); // Descrição detalhada (opcional)
            $table->string('course_icon')->nullable(); // Ícone do curso (opcional)
            $table->string('course_banner')->nullable(); // Banner do curso (opcional)

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Criador do curso, pode ser nulo, se usuário deletado, seta null
            $table->foreignId('coordinator_id')->nullable()->constrained('coordinators')->onDelete('set null'); // Coordenador responsável, pode ser nulo, se deletado seta null

            $table->timestamps(); // Controle de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
