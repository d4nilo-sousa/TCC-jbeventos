<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('events', function (Blueprint $table) {
        $table->boolean('reminder_24h_sent')->default(false);
        $table->boolean('reminder_1h_sent')->default(false);
    });
}

public function down()
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn(['reminder_24h_sent', 'reminder_1h_sent']);
    });
}

};
