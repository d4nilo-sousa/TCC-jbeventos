<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para adicionar a coluna 'bio' na tabela 'users'.
 */
return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Adiciona a coluna 'bio' do tipo texto, opcional, após a coluna 'user_banner'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('user_banner');
        });
    }

    /**
     * Reverte as migrações.
     *
     * Remove a coluna 'bio' da tabela 'users'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bio');
        });
    }
};