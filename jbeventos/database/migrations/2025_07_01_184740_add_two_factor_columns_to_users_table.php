<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para adicionar colunas relacionadas à autenticação de dois fatores na tabela 'users'.
 */
return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Adiciona as colunas 'two_factor_secret', 'two_factor_recovery_codes' e 'two_factor_confirmed_at' 
     * na tabela 'users' para armazenar dados da autenticação de dois fatores.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')
                ->after('password')
                ->nullable();

            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            $table->timestamp('two_factor_confirmed_at')
                ->after('two_factor_recovery_codes')
                ->nullable();
        });
    }

    /**
     * Reverte as migrações.
     *
     * Remove as colunas relacionadas à autenticação de dois fatores da tabela 'users'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }
};