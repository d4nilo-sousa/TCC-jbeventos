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
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_event');
    }
};
