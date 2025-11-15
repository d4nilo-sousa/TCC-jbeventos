<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Notifications\NewEventNotification; // Importa a classe de notificação

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpar a tabela de notificações padrão do Laravel
        DB::table('notifications')->truncate();

        // 2. Busca de dados
        // Carrega os usuários que ativaram o alerta e também os cursos que seguem, 
        // pois a notificação NewEventNotification depende disso.
        $events = Event::with(['notifiableUsers.followedCourses', 'eventCoordinator.userAccount'])->get();

        if ($events->isEmpty()) {
            $this->command->warn('⚠️ Pulei NotificationSeeder: Nenhum Evento encontrado para gerar notificações.');
            return;
        }

        $notificationData = [];

        // 3. Simular Notificações de "Novo Evento"
        foreach ($events as $event) {
            
            // Pega os usuários que habilitaram a notificação para este evento específico
            $notifiableUsers = $event->notifiableUsers;

            if ($notifiableUsers->isEmpty()) {
                continue;
            }
            
            // Cria um timestamp ligeiramente posterior ao evento para simular o envio imediato
            $sentAt = $event->created_at->addSeconds(rand(1, 3600)); 

            foreach ($notifiableUsers as $user) {
                
                // 1. Cria uma instância da notificação
                $notificationInstance = new NewEventNotification($event);
                
                // 2. Chama o método toArray() da notificação para obter os dados formatados
                $notificationContent = $notificationInstance->toArray($user);

                $notificationData[] = [
                    'id' => Str::uuid(), // ID UUID é o padrão da tabela notifications
                    'type' => 'App\Notifications\NewEventNotification', // Nome da classe
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $user->id,
                    // Insere o JSON completo gerado pela classe de notificação
                    'data' => json_encode($notificationContent), 
                    'read_at' => (rand(0, 1) === 1) ? $sentAt->copy()->addMinutes(rand(1, 10)) : null, // 50% de chance de ser lida
                    'created_at' => $sentAt,
                    'updated_at' => $sentAt,
                ];
            }
        }

        // 4. Inserção em Massa
        if (!empty($notificationData)) {
            DB::table('notifications')->insert($notificationData);
            $this->command->info('✅ Notificações de "Novo Evento" cadastradas com sucesso!');
        } else {
             $this->command->warn('⚠️ Nenhuma notificação de evento gerada.');
        }
    }
}