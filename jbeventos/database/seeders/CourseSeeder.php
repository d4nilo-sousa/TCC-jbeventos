<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Coordinator;

class CourseSeeder extends Seeder
{
    /**
     * Executa o seeder para criar um curso.
     */
    public function run(): void
    {
        // Busca o primeiro usuário do tipo 'admin'
        $admin = User::where('user_type', 'admin')->first();

        // Busca o primeiro coordenador do tipo 'course' com conta de usuário válida
        $courseCoordinator = Coordinator::where('coordinator_type', 'course')
            ->whereHas('userAccount', function ($query) {
                $query->where('user_type', 'coordinator');
            })->first();

        // Se ambos existirem, cria um curso associado ao admin e ao coordenador
        if ($admin && $courseCoordinator) {
            Course::create([
                'course_name' => 'Curso Exemplo',
                'course_description' => 'Este curso foi criado apenas para testes',
                'course_icon' => null,
                'course_banner' => null,
                'user_id' => $admin->id,
                'coordinator_id' => $courseCoordinator->id,
            ]);
        }
    }
}
