<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Course;
use App\Models\Category;
use Carbon\Carbon;
// Não precisamos de Storage/File aqui, pois as imagens estão nulas por enquanto.

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Busca todos os Cursos e Categorias
        $courses = Course::all();
        $categories = Category::all();

        // 2. Busca o Coordenador Geral (Paula)
        $paulaCoordinator = Coordinator::whereHas('userAccount', function ($query) {
            $query->where('email', 'paula@coordenadora.com');
        })->first();

        if (!$paulaCoordinator) {
            echo "Aviso: Coordenadora Geral (paula@coordenadora.com) não encontrada. Os eventos gerais não serão cadastrados.\n";
            return;
        }

        // 3. Definição dos Eventos Gerais
        $generalEventsData = [
            [
                'name' => 'Interclasses 2025',
                'description' => 'Veja fotos do Interclasses da Etec JB! Torneio de esportes que promove a competição saudável e a união entre as classes.',
                'location' => 'Escola (Quadras)',
                'scheduled_at' => '2025-06-23 08:00:00',
                'expired_at' => null,
                'categories' => ['Esportivo', 'Integração de Cursos'],
                'type' => 'general',
            ],
            [
                'name' => 'Festa Junina',
                'description' => 'Venha curtir o nosso arraiá! Comidas típicas, danças e muita diversão para a comunidade escolar celebrar a cultura junina.',
                'location' => 'Praça da Escola',
                'scheduled_at' => '2025-06-16 08:30:00',
                'expired_at' => null,
                'categories' => ['Cultural', 'Educacional'],
                'type' => 'general',
            ],
            [
                'name' => 'HalloMuertos',
                'description' => 'Celebração unindo as tradições do Halloween (EUA) e o Dia de Muertos (México). Uma festa de cultura, arte e fantasias!',
                'location' => 'Praça da Escola',
                'scheduled_at' => '2025-10-31 08:00:00',
                'expired_at' => null,
                'categories' => ['Cultural', 'Educacional', 'Arte'],
                'type' => 'general',
            ],
        ];

        // 4. Criação dos Eventos Gerais
        foreach ($generalEventsData as $eventData) {
            
            $event = Event::create([
                'event_name' => $eventData['name'],
                'event_description' => $eventData['description'],
                'event_location' => $eventData['location'],
                'event_scheduled_at' => Carbon::parse($eventData['scheduled_at']),
                'event_expired_at' => $eventData['expired_at'] ? Carbon::parse($eventData['expired_at']) : null,
                
                'event_image' => null, // Imagem nula por enquanto
                
                // *** CORREÇÃO APLICADA: REMOVIDO 'course_id' ***
                
                'event_type' => $eventData['type'],
                'coordinator_id' => $paulaCoordinator->id,
            ]);

            // Associa Categorias (via tabela pivot category_event)
            $categoryIds = $categories->whereIn('category_name', $eventData['categories'])->pluck('id');
            if ($categoryIds->isNotEmpty()) {
                $event->eventCategories()->attach($categoryIds);
            }
        }
    }
}