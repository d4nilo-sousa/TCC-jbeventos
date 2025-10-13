<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Course;
use App\Models\Coordinator;

class FillMissingEventCoordinators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fill-missing-event-coordinators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preenche o coordinator_id dos eventos que estão nulos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::whereNull('coordinator_id')->get();
        $this->info("Encontrados " . $events->count() . " eventos sem coordenador");

        foreach ($events as $event) {
            if ($event->event_type === 'course' && $event->course_id) {
                $course = Course::find($event->course_id);
                if ($course && $course->coordinator_id) {
                    $event->updateQuietly(['coordinator_id' => $course->coordinator_id]);
                    $this->info("Evento {$event->id} atualizado com o coordenador do curso {$course->id}.");
                }
            }

            if ($event->event_type === 'general') {
                $coordinator = Coordinator::where('coordinator_type', 'general')->first();
                if ($coordinator) {
                    $event->updateQuietly(['coordinator_id' => $coordinator->id]);
                    $this->info("Evento {$event->id} atualizado com o coordenador general {$coordinator->id}.");
                }
            }
        }

        $this->info("Atualização Concluída!");
        return 0;
    }
}
