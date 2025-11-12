<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Coordinator;
use Illuminate\Support\Facades\Storage; // Importa Storage para manipulação de arquivos
use Illuminate\Support\Facades\File;    // Importa File para verificar se o arquivo existe
use Symfony\Component\HttpFoundation\File\File as SymfonyFile; // Alias para evitar conflito

class CourseSeeder extends Seeder
{
    /**
     * Executa o seeder para criar os cursos.
     */
    public function run(): void
    {
        // 1. Configurações de Ícones
        $sourceDir = database_path('image-data/course-icons');
        $targetDisk = 'public';
        $targetDir = 'course-icons'; // Pasta de destino dentro do disco 'public'
        
        // Garante que o diretório de destino existe (storage/app/public/course-icons)
        Storage::disk($targetDisk)->makeDirectory($targetDir);
        
        // Busca um usuário para ser o criador do curso (user_id).
        // Assume que o primeiro 'admin' é o criador, ou pega o primeiro usuário encontrado.
        $admin = User::where('user_type', 'admin')->first() ?? User::first(); 
        
        if (!$admin) {
            echo "Aviso: Nenhum usuário encontrado para ser o criador dos cursos. Seeder interrompido.\n";
            return;
        }

        // 2. Dados dos Cursos
        // Adicionei cores hexadecimais como um placeholder para o banner, já que você usará cores.
        $coursesData = [
            [
                'name' => 'Química',
                'description' => 'Bem vindo ao curso de Química!',
                'coordinator_email' => 'paulomazieiro@example.com',
                'icon_file' => 'etiq-course-icon.jpg',
                'banner_color' => '#6103a0ff',
            ],
            [
                'name' => 'Ciências da Natureza',
                'description' => 'Bem vindo ao curso de CNAT!',
                'coordinator_email' => 'lidiane@example.com',
                'icon_file' => 'cnat-course-icon.jpg',
                'banner_color' => '#006400',
            ],
            [
                'name' => 'Edificações',
                'description' => 'Bem vindo ao curso de Edificações!',
                'coordinator_email' => 'truzzi@example.com',
                'icon_file' => 'eda-course-icon.jpg',
                'banner_color' => '#dcd800ff',
            ],
            [
                'name' => 'Eventos',
                'description' => 'Bem vindo ao curso de Eventos!',
                'coordinator_email' => 'evandro@example.com',
                'icon_file' => 'eventos-course-icon.jpg',
                'banner_color' => '#c01d00ff',
            ],
            [
                'name' => 'Eletrônica',
                'description' => 'Bem vindo ao curso de Eletrônica',
                'coordinator_email' => 'guilhermebim@example.com',
                'icon_file' => 'etel-course-icon.jpg',
                'banner_color' => '#1d0087ff',
            ],
            [
                'name' => 'Administração',
                'description' => 'Bem vindo ao curso de Administração',
                'coordinator_email' => 'eduardo@example.com',
                'icon_file' => 'ada-course-icon.jpg', 
                'banner_color' => '#46007cff', 
            ],
        ];

        // 3. Itera sobre os dados e cria os cursos
        foreach ($coursesData as $courseData) {
            
            // A) Busca o Coordenador
            // Procura o registro do Coordenador através do e-mail do usuário associado
            $coordinator = Coordinator::whereHas('userAccount', function ($query) use ($courseData) {
                $query->where('email', $courseData['coordinator_email']);
            })->first();

            if (!$coordinator) {
                echo "Aviso: Coordenador com e-mail '{$courseData['coordinator_email']}' não encontrado para o curso '{$courseData['name']}'. Pulando este curso.\n";
                continue;
            }

            // B) Lógica para copiar o Ícone
            $iconPath = null;
            $sourceFilePath = $sourceDir . '/' . $courseData['icon_file'];
            
            if (File::exists($sourceFilePath)) {
                // Copia o arquivo para o disco public
                Storage::disk($targetDisk)->putFileAs(
                    $targetDir, 
                    new SymfonyFile($sourceFilePath), 
                    $courseData['icon_file']
                );
                
                // Salva o caminho para o BD
                $iconPath = $targetDir . '/' . $courseData['icon_file'];
            }
            
            // C) Cria o Curso
            Course::create([
                'course_name' => $courseData['name'],
                'course_description' => $courseData['description'],
                'course_icon' => $iconPath,
                // O course_banner está sendo preenchido com a cor hexadecimal
                'course_banner' => $courseData['banner_color'], 
                'user_id' => $admin->id,
                'coordinator_id' => $coordinator->id,
            ]);
        }
    }
}