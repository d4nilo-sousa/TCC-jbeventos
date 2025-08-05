<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Course;
use App\Models\Category;
use Carbon\Carbon;


class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseCoordinator = Coordinator::where('coordinator_type', 'course')
            ->whereHas('userAccount', function ($query) {
                $query->where('user_type', 'coordinator');
            })->first();

        $course = Course::first();

        $categories = Category::all();

        $generalCoordinator = Coordinator::where('coordinator_type', 'general')
            ->whereHas('userAccount', function ($query) {
                $query->where('user_type', 'coordinator');
            })->first();

        if ($courseCoordinator && $course && $categories->count() >= 2) {
            $eventGeneral = Event::create([
                'event_name' => 'Evento de Curso (Teste)',
                'event_description' => 'Este evento foi criado apenas para testes',
                'event_location' => 'Local Teste',
                'event_scheduled_at' => Carbon::now()->addDays(10),
                'event_expired_at' => null,
                'event_image' => null,
                'visible_event' => true,
                'event_type' => 'course',
                'coordinator_id' => $courseCoordinator->id,
                'course_id' => $course->id,
            ]);
        }

        if ($generalCoordinator && $categories->count() >= 2) {
            $eventCourse = Event::create([
                'event_name' => 'Evento Geral (Teste)',
                'event_description' => 'Este evento foi criado apenas para testes',
                'event_location' => 'Local Teste',
                'event_scheduled_at' => Carbon::now()->addDays(10),
                'event_expired_at' => null,
                'event_image' => null,
                'visible_event' => true,
                'event_type' => 'general',
                'coordinator_id' => $generalCoordinator->id,
                'course_id' => null,
            ]);
        }

        $eventCourse->EventCategories()->attach($categories->pluck('id')->take(2));
        $eventGeneral->EventCategories()->attach($categories->pluck('id')->take(2));
    }
}
