<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Event;

class DeleteExpiredEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deleta automaticamente eventos que passaram do prazo definido pelo usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $deleted = Event::whereNotNull('event_expired_at')
            ->where('event_expired_at', '<=', $now)
            ->delete();

        $this->info("Eventos expirados deletados: $deleted");
    }
}
