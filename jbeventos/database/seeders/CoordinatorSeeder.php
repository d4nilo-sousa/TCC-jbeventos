<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Coordinator;

class CoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        foreach ($coordinators as $coordinatorData) {

            $user = User::create([
                'name' => $coordinatorData['name'], 
                'email' => $coordinatorData['email'], 
                'password' => Hash::make('Coordinator@123'),
                'user_type' => 'coordinator'
            ]);

            Coordinator::create([
                'coordinator_type' => $coordinatorData['type'],
                'temporary_password' => true,
                'user_id' => $user->id,
            ]);
        }
    }
}