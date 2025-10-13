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

        // --- Evento de Curso (com associação obrigatória e opcional) ---
        if ($courseCoordinator && $course && $categories->count() >= 2) {
            $eventCourse = Event::create([
                'event_name' => 'Evento de Curso (Teste)',
                'event_description' => 'Este evento foi criado apenas para testes',
                'event_location' => 'Local Teste',
                'event_scheduled_at' => Carbon::now()->addDays(10),
                'event_expired_at' => null,
                'event_image' => null,
                'visible_event' => true,
                'event_type' => 'course',
                'coordinator_id' => $courseCoordinator->id,
                
                // REMOVIDO: 'course_id' => $course->id,
            ]);

            // NOVO: Associa o curso obrigatório e, se houver, um curso extra
            $coursesToAttach = [$course->id];
            if ($extraCourse) {
                $coursesToAttach[] = $extraCourse->id;
            }
            
            // Usa o método courses() e o attach() para inserir na tabela pivô (N:M)
            $eventCourse->courses()->attach($coursesToAttach);


            // Associa duas categorias ao evento criado
            $eventCourse->eventCategories()->attach($categories->pluck('id')->take(2));

        }

        // --- Evento Geral (sem associação de curso) ---
        if ($generalCoordinator && $categories->count() >= 2) {
            $eventGeneral = Event::create([
                'event_name' => 'Evento Geral (Teste)',
                'event_description' => 'Este evento foi criado apenas para testes',
                'event_location' => 'Local Teste',
                'event_scheduled_at' => Carbon::now()->addDays(10),
                'event_expired_at' => null,
                'event_image' => null,
                'visible_event' => true,
                'event_type' => 'general',
                'coordinator_id' => $generalCoordinator->id,
                
                // REMOVIDO: 'course_id' => null,
            ]);
            
            // NÃO HÁ course_id para eventos gerais, então nada é anexado à tabela pivô 'course_event'
            
            // Associa duas categorias ao evento criado
            $eventGeneral->eventCategories()->attach($categories->pluck('id')->take(2));
        }
    }
}