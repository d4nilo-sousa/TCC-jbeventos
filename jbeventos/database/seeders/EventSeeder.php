<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Course;
use App\Models\Category;
use App\Models\EventImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class EventSeeder extends Seeder
{
    /**
     * Define a função auxiliar para copiar o arquivo
     */
    private function copyFile(string $fileName, string $targetDir): ?string
    {
        $sourceDir = database_path('image-data/event-images');
        $targetDisk = 'public';
        $sourceFilePath = $sourceDir . '/' . $fileName;

        if (File::exists($sourceFilePath)) {
            // Garante que o diretório de destino existe (storage/app/public/event-images)
            Storage::disk($targetDisk)->makeDirectory($targetDir);
            
            // Copia o arquivo usando putFileAs
            Storage::disk($targetDisk)->putFileAs(
                $targetDir, 
                new SymfonyFile($sourceFilePath), 
                $fileName
            );
            
            // Retorna o caminho que será salvo no BD (ex: 'event-images/interclasses-capa.jpeg')
            return $targetDir . '/' . $fileName;
        }

        return null;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Inicialização de dados estáticos
        $courses = Course::all()->keyBy('course_name'); // Indexa por nome para busca rápida
        $categories = Category::all()->keyBy('category_name'); // Indexa por nome para busca rápida
        
        // Define o diretório de destino das imagens
        $imageTargetDir = 'event-images';

        // 2. Dados Consolidados dos Eventos
        $eventsData = [
            // --- EVENTO GERAL 1: Interclasses 2025 ---
            [
                'event_name' => 'Interclasses 2025',
                'event_info' => 'Veja fotos do Interclasses da Etec JB! Torneio de esportes que promove a competição saudável e a união entre as classes.', // CORRIGIDO
                'event_location' => 'Escola (Quadras)',
                'event_scheduled_at' => '2025-06-23 08:00:00',
                'event_expired_at' => null,
                'event_type' => 'general',
                'coordinator_emails' => ['paula@example.com'],
                'course_names' => [],
                'categories' => ['Esportivo', 'Integração de Cursos'],
                'capa_file' => 'interclasses-capa.jpeg',
                'galeria_files' => ['interclasses-1.jpeg', 'interclasses-2.jpeg', 'interclasses-3.jpeg', 'interclasses-4.jpeg'],
            ],
            // --- EVENTO GERAL 2: Festa Junina ---
            [
                'event_name' => 'Festa Junina',
                'event_info' => 'Venha curtir o nosso arraiá! Comidas típicas, danças e muita diversão para a comunidade escolar celebrar a cultura junina.', // CORRIGIDO
                'event_location' => 'Praça da Escola',
                'event_scheduled_at' => '2025-06-16 08:30:00',
                'event_expired_at' => null,
                'event_type' => 'general',
                'coordinator_emails' => ['paula@example.com'],
                'course_names' => [],
                'categories' => ['Cultural', 'Educacional'],
                'capa_file' => 'festa-junina-capa-e-galeria.jpg',
                'galeria_files' => ['festa-junina-capa-e-galeria.jpg'],
            ],
            // --- EVENTO DE CURSO 1: Palestra CRQ ---
            [
                'event_name' => 'Palestra CRQ',
                'event_info' => 'alunos do curso de Química diurno e noturno puderam assistir a uma palestra realizada pelo representante e fiscal do Conselho Regional de Química (CRQ 4)', // CORRIGIDO
                'event_location' => 'Auditório',
                'event_scheduled_at' => '2025-10-02 09:30:00',
                'event_expired_at' => null,
                'event_type' => 'course',
                'coordinator_emails' => ['paulomazieiro@example.com'],
                'course_names' => ['Química'],
                'categories' => ['Educacional'],
                'capa_file' => 'palestraCRQ-capa.jpg',
                'galeria_files' => ['palestraCRQ-1.jpg', 'palestraCRQ-2.jpg', 'palestraCRQ-3.jpg'],
            ],
            // --- EVENTO DE CURSO 2: Palestra Eventos Corporativos ---
            [
                'event_name' => 'Palestra - Eventos Corporativos e Sociais na Hotelaria',
                'event_info' => 'Palestra proferida pela Gerente de Alimentos e Bebidas do Eco Resort Canto da Floresta - Mara Beatriz Pereira e também pelo nosso estimado professor Daniel da Costa Matoso Fabri.', // CORRIGIDO
                'event_location' => 'Auditório',
                'event_scheduled_at' => '2025-04-30 08:00:00',
                'event_expired_at' => null,
                'event_type' => 'course',
                'coordinator_emails' => ['evandro@example.com'],
                'course_names' => ['Eventos'],
                'categories' => ['Educacional'],
                'capa_file' => 'palestra-eventos-capa.jpg',
                'galeria_files' => ['palestra-eventos-1.jpg', 'palestra-eventos-2.jpg'],
            ],
            // --- EVENTO DE CURSO 3: Palestra - Merlin Batista ---
            [
                'event_name' => 'Palestra - Merlin Batista',
                'event_info' => 'Tivemos a honra de receber Merllin Batista - cientista, fisioterapeuta, especialista em Saúde Digital e População Negra, doutora-mestra e Ph.D sanduíche em Harvard. Uma trajetória que inspira!', // CORRIGIDO
                'event_location' => 'Auditório',
                'event_scheduled_at' => '2025-10-22 08:00:00',
                'event_expired_at' => null,
                'event_type' => 'course',
                'coordinator_emails' => ['lidiane@example.com', 'truzzi@example.com', 'paulomazieiro@example.com'],
                'course_names' => ['Ciências da Natureza', 'Edificações', 'Química'],
                'categories' => ['Educacional', 'Profissionalizante'],
                'capa_file' => 'palestra-Merlin-Batista-capa.jpg',
                'galeria_files' => ['palestra-Merlin-Batista-1.jpg', 'palestra-Merlin-Batista-2.jpg'],
            ],
        ];

        // 3. Itera e Cria os Eventos
        foreach ($eventsData as $eventData) {
            // A) Busca Coordenador (usa apenas o primeiro e-mail da lista)
            $coordinator = Coordinator::whereHas('userAccount', function ($query) use ($eventData) {
                $query->where('email', $eventData['coordinator_emails'][0]);
            })->first();

            if (!$coordinator) {
                echo "Aviso: Coordenador '{$eventData['coordinator_emails'][0]}' não encontrado para o evento '{$eventData['event_name']}'. Pulando.\n";
                continue;
            }

            // B) Copia a Imagem de Capa
            $eventImagePath = $this->copyFile($eventData['capa_file'], $imageTargetDir);
            
            // C) Cria o Evento
            $event = Event::create([
                'event_name' => $eventData['event_name'],
                'event_info' => $eventData['event_info'],
                'event_location' => $eventData['event_location'],
                'event_scheduled_at' => Carbon::createFromFormat('Y-m-d H:i:s', $eventData['event_scheduled_at']),
                'event_expired_at' => $eventData['event_expired_at'] ? Carbon::createFromFormat('Y-m-d H:i:s', $eventData['event_expired_at']) : null,
                'event_image' => $eventImagePath,
                'event_type' => $eventData['event_type'],
                'coordinator_id' => $coordinator->id,
            ]);

            // D) Associa Categorias
            $categoryIds = $categories->whereIn('category_name', $eventData['categories'])->pluck('id');
            if ($categoryIds->isNotEmpty()) {
                $event->eventCategories()->attach($categoryIds);
            }

            // E) Associa Cursos
            if (!empty($eventData['course_names'])) {
                $courseIds = $courses->whereIn('course_name', $eventData['course_names'])->pluck('id');
                if ($courseIds->isNotEmpty()) {
                    $event->courses()->attach($courseIds);
                }
            }

            // F) Copia e Salva as Imagens de Galeria
            $galleryImagesToCreate = [];
            foreach ($eventData['galeria_files'] as $galeriaFile) {
                $imagePath = $this->copyFile($galeriaFile, $imageTargetDir);
                if ($imagePath) {
                    $galleryImagesToCreate[] = ['image_path' => $imagePath];
                }
            }

            if (!empty($galleryImagesToCreate)) {
                $event->images()->createMany($galleryImagesToCreate);
            }
        }
    }
}
