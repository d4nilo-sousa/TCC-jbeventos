<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Reply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str; // Usaremos para gerar slugs/caminhos de imagem aleatórios (apenas se necessário)

class ReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Verificação de dependências
        if (Post::count() === 0) {
            $this->command->warn('⚠️ Pulei ReplySeeder: Nenhum Post encontrado. Execute PostSeeder primeiro.');
            return;
        }

        // Limpa respostas antigas para evitar duplicatas em re-seeding
        Reply::truncate();

        // 2. Pegar IDs de todos os usuários (que serão os autores das respostas)
        $userIds = User::pluck('id');
        $posts = Post::all();
        
        // Se não houver usuários, não é possível criar respostas
        if ($userIds->isEmpty()) {
            $this->command->warn('⚠️ Pulei ReplySeeder: Nenhum Usuário encontrado. Execute UserSeeder/CoordinatorSeeder primeiro.');
            return;
        }

        // 3. Respostas de Exemplo para cada Post
        $sampleContents = [
            // Respostas ao Post 1: Palestra Eventos Corporativos
            'Parabéns ao curso de Eventos pela iniciativa! Parece que foi um sucesso de aprendizado.',
            'Adorei as fotos! A palestra foi muito interessante e valiosa. Muito obrigado pelo compartilhamento.',
            'Queria ter ido! O setor de Hotelaria está em alta, com certeza foi muito enriquecedor.',
            'Evento nota 10! A Gerente Mara compartilhou dicas valiosíssimas para quem quer crescer na carreira!',

            // Respostas ao Post 2: Palestra Merlin Batista
            'Uau, Merlin Batista na Etec? Que oportunidade incrível! #SaúdeDigital é o futuro.',
            'Essa palestra foi muito motivacional! É ótimo ver a interseção de Ciências, Edificações e Química.',
            'Um agradecimento especial à Lidiane por organizar algo tão relevante. O conteúdo sobre Ciência e Tecnologia foi demais!',
            'Realmente transformador! A visão da Merlin abriu minha mente para novas possibilidades na área de Química.',

            // Respostas ao Post 3: Palestra CRQ
            'A palestra do CRQ foi extremamente informativa! Me sinto mais preparado para os desafios da profissão.',
            'Excelente iniciativa do curso de Química em trazer o CRQ para compartilhar conhecimento.',
            'Agradeço ao CRQ pela parceria e pelo conteúdo compartilhado. Foi uma experiência enriquecedora!',
            'Com certeza, essa palestra vai fazer a diferença na minha carreira como químico.',
        ];

        $replyIndex = 0;
        
        // 4. Itera sobre os posts e cria respostas
        $posts->each(function ($post) use ($userIds, $sampleContents, &$replyIndex) {
            
            // Define quantas respostas criar para este post (entre 2 e 4)
            $numberOfReplies = rand(2, 4);

            for ($i = 0; $i < $numberOfReplies; $i++) {
                
                // Pega o conteúdo da amostra e avança o índice (usa o operador % para reciclar se acabar)
                $content = $sampleContents[$replyIndex % count($sampleContents)];
                $replyIndex++;

                // Cria uma data ligeiramente após a criação do post
                $createdAt = Carbon::parse($post->created_at)->addMinutes(rand(10, 240));

                Reply::create([
                    'post_id' => $post->id,
                    'user_id' => $userIds->random(), // Seleciona um autor aleatório
                    'content' => $content, // Usa o conteúdo da lista de amostras
                    'created_at' => $createdAt, 
                    'updated_at' => $createdAt,
                ]);
            }
        });

        $this->command->info('✅ Respostas (Replies) cadastradas com sucesso!');
    }
}