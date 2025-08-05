<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration para criação das tabelas 'users', 'password_reset_tokens' e 'sessions'.
 */
return new class extends Migration
{
    /**
     * Executa as migrations, criando as tabelas necessárias.
     *
     * @return void
     */
    public function up(): void
    {
        // Criação da tabela 'users' com dados básicos, autenticação e perfil
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID único do usuário

            // Dados pessoais e contato
            $table->string('name'); // Nome completo do usuário
            $table->string('email')->unique(); // Email único para login e comunicação
            $table->timestamp('email_verified_at')->nullable(); // Data de verificação do email
            $table->string('phone_number')->nullable()->unique(); // Telefone opcional e único
            $table->timestamp('phone_number_verified_at')->nullable(); // Data de verificação do telefone

            // Dados de autenticação
            $table->string('password'); // Senha criptografada
            $table->rememberToken(); // Token para "lembrar" o login

            // Personalização do perfil
            $table->string('user_icon')->nullable(); // Avatar do usuário
            $table->string('user_banner')->nullable(); // Banner do perfil
            $table->string('profile_photo_path', 2048)->nullable(); // Caminho para foto de perfil

            // Controle de tipo/perfil do usuário
            $table->enum('user_type', ['admin', 'coordinator', 'user'])->default('user'); // Tipo do usuário

            $table->foreignId('current_team_id')->nullable();  // Referência opcional a equipe atual do usuário
            $table->timestamps();
        });

        // Criação da tabela para tokens de reset de senha
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email do usuário - chave primária
            $table->string('token'); // Token para reset de senha
            $table->timestamp('created_at')->nullable(); // Data de criação do token
        });

        // Criação da tabela de sessões do sistema
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID da sessão
            $table->foreignId('user_id')->nullable()->index(); // ID do usuário associado, índice para busca
            $table->string('ip_address', 45)->nullable(); // Endereço IP da sessão
            $table->text('user_agent')->nullable(); // Informação do user agent do navegador
            $table->longText('payload'); // Dados da sessão serializados
            $table->integer('last_activity')->index(); // Timestamp da última atividade na sessão
        });
    }

    /**
     * Reverte as migrations, removendo as tabelas criadas.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};