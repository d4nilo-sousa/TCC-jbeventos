<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para adicionar campos de edição, respostas e mídia na tabela 'comments'.
 */
return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Adiciona as colunas:
     * - 'edited_at' para registrar quando o comentário foi editado.
     * - 'parent_id' para permitir respostas vinculadas a outro comentário.
     * - 'media_path' para anexar arquivos de mídia ao comentário.
     *
     * Define chave estrangeira 'parent_id' referenciando 'comments.id' com exclusão em cascata.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('visible_comment');

            // Para respostas (relacionamento com outro comentário)
            $table->foreignId('parent_id')
                ->nullable()
                ->after('event_id')
                ->constrained('comments')
                ->onDelete('cascade');

            // Para anexar imagens, vídeos ou arquivos
            $table->string('media_path')->nullable()->after('parent_id');
        });
    }

    /**
     * Reverte as migrações.
     *
     * Remove as colunas 'edited_at', 'media_path' e a foreign key 'parent_id' da tabela 'comments'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['edited_at', 'media_path']);
            
            // Remove a foreign key e depois a coluna 'parent_id'
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};