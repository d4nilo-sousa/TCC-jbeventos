<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criar a tabela 'event_user_alerts' que registra alertas de usuários para eventos.
 */
return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Cria a tabela 'event_user_alerts' com as colunas:
     * - id: identificador único do alerta.
     * - event_id: referência ao evento que gerou o alerta, com exclusão em cascata.
     * - user_id: referência ao usuário que recebeu o alerta, com exclusão em cascata.
     * - timestamps: para controle de criação e atualização do registro.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('event_user_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverte as migrações.
     *
     * Remove a tabela 'event_user_alerts'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('event_user_alerts');
    }
};