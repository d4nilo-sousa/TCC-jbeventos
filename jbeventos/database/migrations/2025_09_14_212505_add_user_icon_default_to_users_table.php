<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a nova coluna, que pode ser nula
            $table->string('user_icon_default')->nullable()->after('user_icon');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove a coluna caso a migração seja revertida
            $table->dropColumn('user_icon_default');
        });
    }
};