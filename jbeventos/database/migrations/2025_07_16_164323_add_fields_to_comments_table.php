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
        Schema::table('comments', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('visible_comment');

            // Para anexar imagens, vídeos ou arquivos
            $table->string('media_path')->nullable()->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['edited_at', 'media_path']);
            
            // Para remover a foreign key corretamente
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
