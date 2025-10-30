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
        // 1. Define a janela de tempo para os lembretes (24h de antecedência)
        $reminderStart = Carbon::now()->addHours(23)->toDateTimeString(); // De 23h até
        $reminderEnd = Carbon::now()->addHours(25)->toDateTimeString(); // 25h no futuro

        // 2. Encontra eventos que estão para começar dentro dessa janela de 2 horas
        $events = Event::with('saivers') // Carrega os usuários que salvaram o evento
            ->whereBetween('event_scheduled_at', [$reminderStart, $reminderEnd])
            ->get();

        $this->info("Verificando lembretes para {$events->count()} eventos...");

        foreach ($events as $event) {
            $recipients = $event->saivers; // Pega os usuários que salvaram o evento

            if ($recipients->isNotEmpty()) {
                // 3. Envia a notificação de lembrete
                // Você deve criar a EventReminderNotification com o canal 'database' (próximo passo)
                $this->info("Enviando lembrete para {$recipients->count()} usuários para o evento '{$event->event_name}'");
                
                // NOTA: O Laravel vai automaticamente colocar isso na fila graças ao 'ShouldQueue'
                foreach ($recipients as $user) {
                     $user->notify(new EventReminderNotification($event));
                }
            }
        }
        
        $this->info('Verificação de lembretes concluída.');

        return Command::SUCCESS;
    }
}