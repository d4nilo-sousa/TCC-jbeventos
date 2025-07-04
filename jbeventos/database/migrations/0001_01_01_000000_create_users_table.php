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
        // Criação da tabela 'users' com dados básicos, autenticação e perfil
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID único do usuário

            // Dados pessoais e contato
            $table->string('name'); // Nome completo do usuário
            $table->string('email')->unique(); // Email único para login e comunicação
            $table->timestamp('email_verified_at')->nullable(); // Data de verificação do email
            $table->string('phone_number')->nullable()->unique(); // Telefone opcional e único
            $table->timestamp('phone_number_verified_at')->nullable(); // Verificação do telefone

            // Dados de autenticação
            $table->string('password'); // Senha criptografada
            $table->rememberToken(); // Token para "lembrar" o login

            // Personalização do perfil
            $table->string('user_icon')->nullable(); // Avatar do usuário
            $table->string('user_banner')->nullable(); // Banner do perfil
            $table->string('profile_photo_path', 2048)->nullable();

            // Controle de tipo/perfil do usuário
            $table->enum('user_type', ['admin', 'coordinator', 'user'])->default('user'); // Tipo do usuário

            $table->foreignId('current_team_id')->nullable();  // Referência opcional a equipe atual do usuário (usado se tiver sistema de times)
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
