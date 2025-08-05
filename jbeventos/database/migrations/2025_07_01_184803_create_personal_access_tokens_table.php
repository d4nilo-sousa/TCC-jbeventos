<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criar a tabela 'personal_access_tokens', que armazena tokens de acesso pessoal.
 */
return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Cria a tabela 'personal_access_tokens' com campos para armazenar
     * tokens de acesso pessoal, suas características e validade.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable'); // Relacionamento polimórfico (ex: User, Admin, etc)
            $table->string('name'); // Nome do token
            $table->string('token', 64)->unique(); // Token único (hash)
            $table->text('abilities')->nullable(); // Habilidades/Permissões do token
            $table->timestamp('last_used_at')->nullable(); // Último uso do token
            $table->timestamp('expires_at')->nullable(); // Data de expiração do token
            $table->timestamps(); // Created_at e updated_at
        });
    }

    /**
     * Reverte as migrações.
     *
     * Remove a tabela 'personal_access_tokens'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};