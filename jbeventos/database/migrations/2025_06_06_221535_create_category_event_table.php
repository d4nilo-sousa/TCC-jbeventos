<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação da tabela pivot 'category_event',
 * que associa categorias a eventos (relacionamento muitos-para-muitos).
 */
return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Cria a tabela 'category_event' com os campos necessários para armazenar
     * a associação entre categorias e eventos.
     *
     * @return void
     */
    public function up(): void
    {
        // Criação da tabela pivot 'category_event' para associar categorias a eventos
        Schema::create('category_event', function (Blueprint $table) {
            $table->id(); // ID único do registro

            // Chave estrangeira para a categoria
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); 
            // Chave estrangeira para o evento
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

            $table->timestamps(); // Controle de criação e atualização do registro
        });
    }

    /**
     * Reverte as migrações.
     *
     * Remove a tabela 'category_event' caso exista.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('category_event');
    }
};