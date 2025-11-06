<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    /**
     * O nome e assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'events:remind';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Envia lembretes para usuários que salvaram eventos próximos (24 horas antes).';

    /**
     * Execute o comando no console.
     */
    public function handle()
    {
        // Define as janelas de tempo que queremos verificar (24h e 1h)
        $reminders = [
            '24 horas' => 24, // Lembrete de 24 horas (janela de 2h de verificação)
            '1 hora' => 1,    // Lembrete de 1 hora (janela de 10 min de verificação)
        ];

        $notifiedUsers = []; // Para evitar notificação duplicada se o usuário salvar o evento duas vezes

        foreach ($reminders as $timeString => $hoursBefore) {
            
            // Define o período exato que inicia daqui a X horas (e a janela de verificação)
            // Ex: para 24h, verifica eventos entre 23:50 e 24:10 de antecedência.
            $windowStart = Carbon::now()->addHours($hoursBefore)->subMinutes(10)->toDateTimeString();
            $windowEnd = Carbon::now()->addHours($hoursBefore)->addMinutes(10)->toDateTimeString();

            // Encontra eventos que estão para começar na janela
            $events = Event::with('saivers') // Usuários que salvaram o evento
                ->whereBetween('event_scheduled_at', [$windowStart, $windowEnd])
                ->get();
            
            $this->info("Verificando lembretes de {$timeString} para {$events->count()} eventos...");

            foreach ($events as $event) {
                $recipients = $event->saivers;

                foreach ($recipients as $user) {
                    if (!isset($notifiedUsers[$user->id][$event->id][$timeString])) {
                        
                        // 1. Envia a notificação com a string de tempo correta
                        $user->notify(new EventReminderNotification($event, $timeString));
                        
                        // 2. Marca o usuário como notificado para esta janela de tempo/evento
                        $notifiedUsers[$user->id][$event->id][$timeString] = true;
                    }
                }
            }
        }
        
        $this->info('Verificação de lembretes concluída.');

        return Command::SUCCESS;
    }
}