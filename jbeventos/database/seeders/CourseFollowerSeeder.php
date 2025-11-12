<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseFollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Verificações de dependência
        $courses = Course::all();
        $users = User::all();

        if ($courses->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️ Pulei CourseFollowerSeeder: Nenhum Curso ou Usuário encontrado.');
            return;
        }

        // 2. Limpar a tabela pivot
        // Como é uma tabela pivot simples, podemos usar o DB Facade para truncar
        DB::table('course_user_follow')->truncate();
        
        $followerData = [];

        // 3. Simular Inscrições (Followers)
        // Fazemos com que cada curso tenha entre 50% e 100% dos usuários seguindo.
        foreach ($courses as $course) {
            
            // Calcula o número de seguidores que o curso terá
            $minFollowers = ceil($users->count() * 0.5); 
            $maxFollowers = $users->count();
            $numberOfFollowers = rand($minFollowers, $maxFollowers);
            
            // Pega um subconjunto aleatório de usuários
            $randomFollowers = $users->shuffle()->take($numberOfFollowers);
            
            foreach ($randomFollowers as $user) {
                // Preenche o array para inserção em massa
                $followerData[] = [
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 4. Inserção em Massa
        if (!empty($followerData)) {
            // Insere todos os registros de uma vez
            DB::table('course_user_follow')->insert($followerData);
            $this->command->info('✅ Inscrições de Usuários em Cursos (course_user_follow) cadastradas com sucesso!');
        } else {
             $this->command->warn('⚠️ Nenhuma inscrição gerada.');
        }
    }
}