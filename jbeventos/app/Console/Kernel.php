<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Agenda o comando para enviar o resumo semanal toda segunda-feira às 08:00
        $schedule->command('events:send-weekly-summary')->mondays()->at('08:00');

        $schedule->command('events:send-event-reminders')->everyFifteenMinutes(); // Envia lembretes de eventos
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
