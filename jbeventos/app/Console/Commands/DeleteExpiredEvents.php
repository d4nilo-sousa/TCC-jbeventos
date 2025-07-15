<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Event;

class DeleteExpiredEvents extends Command
{
    /**
     * O nome e assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-events';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Deleta automaticamente eventos que passaram do prazo definido pelo usuário';

    /**
     * Executa o comando do console.
     */
    public function handle()
    {
        // Obtém a data e hora atual
        $now = Carbon::now();

        // Deleta os eventos cuja data de expiração está definida (não é nula)
        // e que já passaram da data atual
        $deleted = Event::whereNotNull('event_expired_at')
            ->where('event_expired_at', '<=', $now)
            ->delete();

        // Exibe no console a quantidade de eventos deletados
        $this->info("Eventos expirados deletados: $deleted");
    }
}
