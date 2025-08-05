<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Coordinator;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('user_type', 'admin')->first();

          $courseCoordinator = Coordinator::where('coordinator_type', 'course')
            ->whereHas('userAccount', function ($query) {
                $query->where('user_type', 'coordinator');
            })->first();

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
