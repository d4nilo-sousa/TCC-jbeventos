<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Envia lembretes para eventos que começarão em 24h e 1h';

    public function handle()
    {
        $now = Carbon::now();
        $intervals = [24, 1];

        foreach ($intervals as $hoursBefore) {
            $targetStart = $now->copy()->addHours($hoursBefore);
            $reminderField = $hoursBefore === 24 ? 'reminder_24h_sent' : 'reminder_1h_sent';

            $events = Event::whereBetween('event_scheduled_at', [
                $targetStart->copy()->subMinutes(15),
                $targetStart->copy()->addMinutes(15),
            ])
            ->where('visible_event', true)
            ->where($reminderField, false)
            ->get();

            foreach ($events as $event) {
                $users = $event->course->followers ?? collect();

                if ($users->isEmpty()) {
                    continue;
                }

                Notification::send($users, new EventReminderNotification($event, $hoursBefore));

                $event->$reminderField = true;
                $event->save();

                $this->info("Enviados lembretes para evento '{$event->event_name}' ({$hoursBefore}h antes).");
            }
        }

        return 0;
    }
}
