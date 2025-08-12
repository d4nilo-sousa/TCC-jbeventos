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
        Schema::table('events', function (Blueprint $table) {
            // Adiciona a coluna 'event_type' do tipo enum com valores 'general' e 'course'
            // Essa coluna será criada logo após a coluna 'visible_event'
            $table->enum('event_type', ['general', 'course'])->after('visible_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Remove a coluna 'event_type' da tabela 'events'
            $table->dropColumn('event_type');
        });
    }
};
