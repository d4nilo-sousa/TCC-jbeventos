<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Coordinator;

class CoordinatorSeeder extends Seeder
{
    /**
     * Executa o seeder para popular a tabela de coordenadores.
     */
    public function run(): void
    {
        // Dados dos coordenadores a serem criados
        $coordinators = [
            [
                'name' => 'Coordenador Geral',
                'email' => 'geral@example.com',
                'type' => 'general',
            ],
            [
                'name' => 'Coordenador de Curso',
                'email' => 'curso@example.com',
                'type' => 'course',
            ],
        ];

        // Itera sobre os dados e cria usuários e registros de coordenadores
        foreach ($coordinators as $coordinatorData) {

            // Cria um usuário com senha padrão e tipo 'coordinator'
            $user = User::create([
                'name' => $coordinatorData['name'], 
                'email' => $coordinatorData['email'], 
                'password' => Hash::make('Coordinator@123'),
                'user_type' => 'coordinator'
            ]);

            // Cria o registro de coordenador vinculado ao usuário
            Coordinator::create([
                'coordinator_type' => $coordinatorData['type'],
                'temporary_password' => false, // Para não ter que cadastrar uma nova senha
                'user_id' => $user->id,
            ]);
        }
    }
}
