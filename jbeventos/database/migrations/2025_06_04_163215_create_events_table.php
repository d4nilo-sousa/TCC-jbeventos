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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name')->unique();
            $table->text('event_description')->nullable();
            $table->string('event_location');
            $table->dateTime('event_start');
            $table->timestamp('event_expired_at')->nullable();
            $table->string('event_image')->nullable();
            $table->boolean('visible_event')->default(true);
            $table->foreignId('coordinator_id')->nullable()->constrained('coordinators')->onDelete('set null');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
            $table->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
