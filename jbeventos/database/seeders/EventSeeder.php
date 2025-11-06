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
        // Pega um Coordenador de Curso
        $courseCoordinator = Coordinator::where('coordinator_type', 'course')
            ->whereHas('userAccount', function ($query) {
                $query->where('user_type', 'coordinator');
            })->first();

        // Pega o primeiro curso disponível (para o evento de curso)
        $course = Course::first();

        // Pega outros cursos (para simular a associação de múltiplos cursos, se houver)
        $extraCourse = Course::where('id', '!=', optional($course)->id)->inRandomOrder()->first();

        $categories = Category::all();

        // Pega um Coordenador Geral
        $generalCoordinator = Coordinator::where('coordinator_type', 'general')
            ->whereHas('userAccount', function ($query) {
                $query->where('user_type', 'coordinator');
            })->first();

        // --- Evento de Curso ---
        if ($courseCoordinator && $course && $categories->count() >= 2) {
            $eventCourse = Event::create([
                'event_name' => 'Evento de Curso (Teste)',
                'event_description' => 'Este evento foi criado apenas para testes',
                'event_location' => 'Local Teste',
                'event_scheduled_at' => Carbon::now()->addDays(10),
                'event_expired_at' => null,
                'event_image' => null,
                'event_type' => 'course',
                'coordinator_id' => $courseCoordinator->id,
            ]);

            // Associa cursos
            $coursesToAttach = [$course->id];
            if ($extraCourse) {
                $coursesToAttach[] = $extraCourse->id;
            }
            $eventCourse->courses()->attach($coursesToAttach);

            // Associa categorias
            $eventCourse->eventCategories()->attach($categories->pluck('id')->take(2));
        }

        // --- Evento Geral ---
        if ($generalCoordinator && $categories->count() >= 2) {
            $eventGeneral = Event::create([
                'event_name' => 'Evento Geral (Teste)',
                'event_description' => 'Este evento foi criado apenas para testes',
                'event_location' => 'Local Teste',
                'event_scheduled_at' => Carbon::now()->addDays(10),
                'event_expired_at' => null,
                'event_image' => null,
                'event_type' => 'general',
                'coordinator_id' => $generalCoordinator->id,
            ]);

            // Associa categorias
            $eventGeneral->eventCategories()->attach($categories->pluck('id')->take(2));
        }
    }
}
