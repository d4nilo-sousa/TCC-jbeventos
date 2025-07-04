<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria um novo usuário (admin) com os dados informados
        User::create([
            'name' => 'Admin Master', // Nome do usuário admin
            'email' => 'admin@example.com', // E-mail do usuário admin
            'email_verified_at' => now(), // Horário da verificação do email do admin
            'password' => Hash::make('Admin@123'), // Senha criptografada para segurança
            'user_type' => 'admin', // Tipo do usuário, aqui definido como admin
        ]);
    }
}
