<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventUserReaction;
use App\Models\Event;
use App\Models\User;

class EventReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpar a tabela de reações para evitar duplicatas em re-seed
        EventUserReaction::truncate();

        // 2. Busca todos os eventos e usuários
        $events = Event::all();
        $users = User::all();

        // Tipos de reações que serão semeadas
        $reactionTypes = ['like', 'save'];

        if ($events->isEmpty() || $users->isEmpty()) {
            echo "Aviso: Não há Eventos ou Usuários suficientes para criar reações.\n";
            return;
        }

        foreach ($events as $event) {
            // Embaralha e pega um subconjunto de usuários para simular interações
            $reactingUsers = $users->shuffle()->take(rand(3, $users->count()));
            
            // Simula reações
            foreach ($reactingUsers as $user) {
                
                // --- 1. Reação 'like' ---
                if (rand(0, 1) === 1) { // 50% de chance de dar like
                    EventUserReaction::create([
                        'reaction_type' => 'like',
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'created_at' => now()->subMinutes(rand(1, 1440)), // Data aleatória no último dia
                        'updated_at' => now()->subMinutes(rand(1, 1440)),
                    ]);
                }

                // --- 2. Reação 'save' (Salvar) ---
                // Verifica se o usuário ainda não salvou o evento
                if (rand(0, 2) === 1) { // 33% de chance de salvar o evento
                    EventUserReaction::create([
                        'reaction_type' => 'save',
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'created_at' => now()->subMinutes(rand(1, 1440)),
                        'updated_at' => now()->subMinutes(rand(1, 1440)),
                    ]);
                }
            }
        }
    }
}