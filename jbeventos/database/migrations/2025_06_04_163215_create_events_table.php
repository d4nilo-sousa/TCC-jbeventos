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
        // Criação da tabela 'events' com informações do evento e relacionamentos
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // ID único do evento

            $table->string('event_name')->unique(); // Nome único do evento
            $table->text('event_description')->nullable(); // Descrição detalhada do evento (opcional)
            $table->string('event_location'); // Local onde o evento será realizado
            $table->dateTime('event_scheduled_at'); // Data e hora agendada para o evento
            $table->timestamp('event_expired_at')->nullable(); // Data e hora de expiração automática do evento (opcional)
            $table->string('event_image')->nullable(); // Imagem representativa do evento (opcional)
            $table->boolean('visible_event')->default(true); // Flag para visibilidade do evento (padrão visível)

            $table->foreignId('coordinator_id')->nullable()->constrained('coordinators')->onDelete('set null'); // Coordenador responsável, se deletado seta null
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade'); // Curso associado, se deletado deleta evento

            $table->timestamps(); // Controle de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
