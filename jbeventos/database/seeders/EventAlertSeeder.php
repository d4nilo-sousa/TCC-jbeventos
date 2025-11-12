<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpar a tabela pivot
        DB::table('event_user_alerts')->truncate();

        // 2. Busca de dados
        $events = Event::all();
        $users = User::all();

        if ($events->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️ Pulei EventAlertSeeder: Nenhum Evento ou Usuário encontrado.');
            return;
        }

        $alertData = [];

        // 3. Simular Alertas de Eventos
        foreach ($events as $event) {
            
            // Tentamos pegar os usuários que seguem os cursos associados ao evento
            $relevantUserIds = collect();
            
            // Se o evento estiver associado a cursos
            if ($event->courses->isNotEmpty()) {
                foreach ($event->courses as $course) {
                    // Pega os IDs dos seguidores de cada curso
                    $courseFollowerIds = $course->followers()->pluck('user_id');
                    $relevantUserIds = $relevantUserIds->merge($courseFollowerIds);
                }
            } else {
                // Se for um evento geral, qualquer usuário é relevante
                $relevantUserIds = $users->pluck('id');
            }
            
            // Remove duplicatas
            $relevantUserIds = $relevantUserIds->unique();
            
            if ($relevantUserIds->isEmpty()) {
                continue;
            }

            // Seleciona aleatoriamente 60% dos usuários relevantes para ativar o alerta
            $usersToAlert = $users->whereIn('id', $relevantUserIds)
                                 ->shuffle()
                                 ->take(ceil($relevantUserIds->count() * 0.6));

            foreach ($usersToAlert as $user) {
                $alertData[] = [
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'created_at' => Carbon::now()->subMinutes(rand(1, 1440)),
                    'updated_at' => Carbon::now()->subMinutes(rand(1, 1440)),
                ];
            }
        }

        // 4. Inserção em Massa
        if (!empty($alertData)) {
            DB::table('event_user_alerts')->insert($alertData);
            $this->command->info('✅ Alertas de Eventos (event_user_alerts) cadastrados com sucesso!');
        } else {
             $this->command->warn('⚠️ Nenhuma alerta de evento gerado.');
        }
    }
}