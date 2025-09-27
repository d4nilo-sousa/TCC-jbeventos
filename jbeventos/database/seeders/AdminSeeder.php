<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Executa o seeder para popular a tabela de admins.
     */
    public function run(): void
    {
        // Cria um novo usuário (admin) com os dados informados
        User::create([
            'name' => 'Admin Master', // Nome do usuário admin
            'email' => 'admin@example.com', // E-mail do usuário admin
            'password' => Hash::make('Admin@123'), // Senha criptografada para segurança
            'user_type' => 'admin', // Tipo do usuário, aqui definido como admin
        ]);
    }
}
