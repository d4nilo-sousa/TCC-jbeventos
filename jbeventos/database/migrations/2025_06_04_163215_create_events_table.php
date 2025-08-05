<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migration, criando a tabela 'events'.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // ID único do evento

            $table->string('event_name')->unique(); // Nome do evento, único no sistema
            $table->text('event_description')->nullable(); // Descrição detalhada (opcional)
            $table->string('event_location'); // Local do evento
            $table->dateTime('event_scheduled_at'); // Data e hora agendada para o evento
            $table->timestamp('event_expired_at')->nullable(); // Data e hora de expiração do evento (opcional)
            $table->string('event_image')->nullable(); // Imagem do evento (opcional)
            $table->boolean('visible_event')->default(true); // Visibilidade do evento (padrão: visível)

            // Relação com coordenador responsável
            // Pode ser nulo; se coordenador for deletado, seta o campo como null
            $table->foreignId('coordinator_id')->nullable()->constrained('coordinators')->onDelete('set null');

            // Relação com curso associado
            // Pode ser nulo; se o curso for deletado, o evento também será deletado (cascade)
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');

            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverte a migration, removendo a tabela 'events'.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};