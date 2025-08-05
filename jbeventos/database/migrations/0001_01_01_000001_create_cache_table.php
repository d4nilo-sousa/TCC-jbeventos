<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação das tabelas 'cache' e 'cache_locks' utilizadas para armazenamento em cache.
 */
return new class extends Migration
{
    /**
     * Executa as migrations, criando as tabelas de cache.
     *
     * @return void
     */
    public function up(): void
    {
        // Tabela para armazenamento de dados em cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // Chave única da entrada de cache
            $table->mediumText('value'); // Valor armazenado em cache
            $table->integer('expiration'); // Timestamp de expiração do cache (em segundos ou unix timestamp)
        });

        // Tabela para controle de locks no cache
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // Chave única do lock
            $table->string('owner'); // Identificação do dono do lock (ex: ID da aplicação/cliente)
            $table->integer('expiration'); // Timestamp de expiração do lock
        });
    }

    /**
     * Reverte as migrations, removendo as tabelas de cache.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};