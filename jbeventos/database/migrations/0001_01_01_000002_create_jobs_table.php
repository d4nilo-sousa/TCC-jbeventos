<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação das tabelas necessárias para o sistema de filas do Laravel:
 * - jobs: fila padrão para jobs pendentes
 * - job_batches: batches de jobs para processamento em grupo
 * - failed_jobs: registros de jobs que falharam
 */
return new class extends Migration
{
    /**
     * Executa as migrations, criando as tabelas de jobs e batches.
     *
     * @return void
     */
    public function up(): void
    {
        // Tabela para jobs pendentes na fila
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();           // Nome da fila
            $table->longText('payload');                // Dados serializados do job
            $table->unsignedTinyInteger('attempts');   // Número de tentativas feitas
            $table->unsignedInteger('reserved_at')->nullable(); // Timestamp quando reservado (em segundos)
            $table->unsignedInteger('available_at');   // Timestamp quando disponível para execução
            $table->unsignedInteger('created_at');     // Timestamp da criação do job
        });

        // Tabela para batches de jobs agrupados
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();            // ID único do batch (string)
            $table->string('name');                      // Nome do batch
            $table->integer('total_jobs');               // Total de jobs no batch
            $table->integer('pending_jobs');             // Jobs pendentes
            $table->integer('failed_jobs');              // Jobs que falharam
            $table->longText('failed_job_ids');          // IDs dos jobs que falharam
            $table->mediumText('options')->nullable();   // Opções adicionais do batch em JSON ou serializado
            $table->integer('cancelled_at')->nullable(); // Timestamp de cancelamento
            $table->integer('created_at');                // Timestamp de criação
            $table->integer('finished_at')->nullable();  // Timestamp de finalização
        });

        // Tabela para registrar jobs que falharam
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();            // UUID único do job falho
            $table->text('connection');                   // Conexão onde o job foi executado
            $table->text('queue');                        // Nome da fila
            $table->longText('payload');                  // Dados do job
            $table->longText('exception');                // Informação da exceção que causou a falha
            $table->timestamp('failed_at')->useCurrent(); // Timestamp da falha (default para current timestamp)
        });
    }

    /**
     * Reverte as migrations, removendo as tabelas de jobs.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};