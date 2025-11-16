<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class PostSeeder extends Seeder
{
    /**
     * Define a função auxiliar para copiar o arquivo
     */
    private function copyFile(string $fileName, string $targetDir): ?string
    {
        $sourceDir = database_path('image-data/event-images'); // Usando o diretório de imagens de evento para posts
        $targetDisk = 'public';
        $sourceFilePath = $sourceDir . '/' . $fileName;

        if (File::exists($sourceFilePath)) {
            // Garante que o diretório de destino existe (storage/app/public/image-posts)
            $finalTargetDir = 'image-posts'; // Usaremos um diretório específico para posts
            Storage::disk($targetDisk)->makeDirectory($finalTargetDir);
            
            // Copia o arquivo usando putFileAs
            Storage::disk($targetDisk)->putFileAs(
                $finalTargetDir, 
                new SymfonyFile($sourceFilePath), 
                $fileName
            );
            
            // Retorna o caminho que será salvo no BD (ex: 'image-posts/interclasses-1.jpeg')
            return $finalTargetDir . '/' . $fileName;
        }

        return null;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Inicialização de dados estáticos e busca de modelos
        $users = User::all()->keyBy('email');
        $courses = Course::all()->keyBy('course_name');
        
        $postsData = [
            // --- POST 1: Palestra Eventos Corporativos por Evandro ---
            [
                'user_email' => 'evandro@example.com',
                'course_name' => 'Eventos',
                'content' => 'Nossa palestra sobre Eventos Corporativos e Sociais na Hotelaria com a Gerente Mara e o Prof. Daniel foi incrível! Informações valiosas para a carreira dos nossos alunos. Obrigado pela presença! 🎤 #EventosEtec #Hotelaria #Profissionalizante',
                'created_at' => '2025-05-01 14:30:00',
                //sem imagens de post
                'post_files' => [],
            ],
            // --- POST 2: Palestra Merlin Batista por Lidiane ---
            [
                'user_email' => 'lidiane@example.com',
                'course_name' => 'Ciências da Natureza', // Postado por Lidiane, mas associado a 3 cursos no evento
                'content' => 'Que honra receber Merllin Batista! Uma verdadeira inspiração em Saúde Digital e Ciência. Nossos alunos de Ciências, Edificações e Química tiveram uma experiência transformadora. O futuro é agora! ✨ #CienciaNaEtec #MerlinBatista #Inspiração',
                'created_at' => '2025-10-23 09:00:00',
                //sem imagens de post
                'post_files' => [],
            ],
            // ---POST 3: Palestra CRQ por paulo ---
            [
                'user_email' => 'paulomazieiro@example.com',
                'course_name' => 'Química',
                'content' => 'A palestra do Conselho Regional de Química foi um sucesso! Nossos alunos agora estão mais preparados para os desafios da profissão. Agradecemos ao CRQ pela parceria e pelo conhecimento compartilhado., #QuímicaEtec #CRQ #Profissionalizante',
                'created_at' => '2025-10-02 10:00:00',
                //sem imagens de post
                'post_files' => [],
            ],
        ];

        // 2. Itera e Cria os Posts
        foreach ($postsData as $postData) {
            
            $user = $users[$postData['user_email']] ?? null;
            $course = $postData['course_name'] ? ($courses[$postData['course_name']] ?? null) : null;

            if (!$user) {
                echo "Aviso: Usuário '{$postData['user_email']}' não encontrado. Pulando Post.\n";
                continue;
            }

            // A) Copia e Salva as Imagens do Post
            $imagePaths = [];
            foreach ($postData['post_files'] as $postFile) {
                $imagePath = $this->copyFile($postFile, 'image-posts');
                if ($imagePath) {
                    $imagePaths[] = $imagePath;
                }
            }
            
            // B) Cria o Post
            Post::create([
                'course_id' => $course?->id, // Pode ser nulo
                'user_id' => $user->id,
                'content' => $postData['content'],
                'images' => $imagePaths, // Salva o array de caminhos (será castado para JSON no BD)
                'created_at' => Carbon::parse($postData['created_at']),
                'updated_at' => Carbon::parse($postData['created_at']),
            ]);
        }
    }
}