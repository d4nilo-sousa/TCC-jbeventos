<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coordinators', function (Blueprint $table) {
            // Adiciona a coluna que faz a ligação com a tabela 'courses'
            $table->foreignId('course_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('coordinators', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna em caso de rollback
            $table->dropConstrainedForeignId('course_id');
        });
    }
};