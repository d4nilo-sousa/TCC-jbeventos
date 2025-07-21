<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona a coluna 'phone_number_verified' na tabela 'users',
     * tipo booleano, padrão false, logo após 'phone_number_verified_at'.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('phone_number_verified')->default(false)->after('phone_number_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     * Remove a coluna 'phone_number_verified' da tabela 'users'.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number_verified');
        });
    }
};
