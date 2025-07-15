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
        // Criação da tabela 'categories' para armazenar categorias do sistema
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // ID único da categoria
            $table->string('category_name')->unique(); // Nome único da categoria
            $table->timestamps(); // Controle automático de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
