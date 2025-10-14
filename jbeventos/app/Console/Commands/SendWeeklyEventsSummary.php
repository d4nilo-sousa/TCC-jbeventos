<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\WeeklyEventsSummaryNotification;
use Carbon\Carbon;

class SendWeeklyEventsSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-weekly-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia resumo semanal de eventos para usuários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startOfWeek = Carbon::now()->startOfWeek(); 
        $endOfWeek = Carbon::now()->endOfWeek();     

        $users = User::with(['followedCourses.events' => function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('event_scheduled_at', [$startOfWeek, $endOfWeek])
                  ->orderBy('event_scheduled_at');
        }])->get();

        foreach ($users as $user) {
            $events = collect();

            foreach ($user->followedCourses as $course) {
                $events = $events->merge($course->events);
            }

            $events = $events->unique('id')->sortBy('event_scheduled_at')->values();

            if ($events->isNotEmpty()) {
                $user->notify(new WeeklyEventsSummaryNotification($events));
            }
        }

        $this->info('Resumo semanal enviado com sucesso!');
    }
}
