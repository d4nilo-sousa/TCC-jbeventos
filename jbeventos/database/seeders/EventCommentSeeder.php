<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Comment; 
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. DESABILITA AS VERIFICAÇÕES DE CHAVE ESTRANGEIRA
        // Isso permite que o TRUNCATE rode mesmo com dependências em 'comment_reactions'
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Verificação de dependências
        if (Event::count() === 0) {
            $this->command->warn('⚠️ Pulei EventCommentSeeder: Nenhum Evento encontrado. Execute EventSeeder primeiro.');
            
            // Reabilita em caso de retorno antecipado
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 
            return;
        }
        
        $userIds = User::pluck('id');
        if ($userIds->isEmpty()) {
            $this->command->warn('⚠️ Pulei EventCommentSeeder: Nenhum Usuário encontrado. Execute UserSeeder/CoordinatorSeeder primeiro.');
            
            // Reabilita em caso de retorno antecipado
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 
            return;
        }

        // Limpa comentários antigos
        Comment::truncate(); 

        // 2. Dados de Comentários de Exemplo
        $sampleContents = [
            'Que evento incrível! Parabéns aos organizadores e a todos que participaram.',
            'Adorei as fotos! O clima de união da escola é contagiante. Ansioso pelo próximo!',
            'Muito bom ver o envolvimento da comunidade escolar. #OrgulhoEtec',
            'O conteúdo da palestra foi de altíssimo nível. Aprendi muito, obrigado!',
            'Detalhes da organização impecáveis. Uma experiência enriquecedora.',
            'Evento show! A Etec está sempre à frente, trazendo temas relevantes.',
            'Queria ter participado, parece que foi muito divertido!',
            'Obrigado por compartilhar as imagens! Ótima recordação.',
        ];

        $commentIndex = 0;
        $events = Event::all();

        // 3. Itera sobre os eventos e cria comentários
        $events->each(function ($event) use ($userIds, $sampleContents, &$commentIndex) {
            
            // Define quantas respostas queremos para cada evento (entre 1 e 5)
            $numberOfComments = rand(1, 5);

            for ($i = 0; $i < $numberOfComments; $i++) {
                
                // Pega o conteúdo da amostra e avança o índice
                $content = $sampleContents[$commentIndex % count($sampleContents)];
                $commentIndex++;

                // Cria uma data ligeiramente após o evento ter sido agendado
                $createdAt = Carbon::parse($event->event_scheduled_at)->addDays(rand(1, 7))->addHours(rand(1, 23));

                Comment::create([
                    'event_id' => $event->id, 
                    'user_id' => $userIds->random(), 
                    'comment' => $content, 
                    'parent_id' => null, 
                    'media_path' => null, 
                    'created_at' => $createdAt, 
                    'updated_at' => $createdAt,
                ]);
            }
        });

        $this->command->info('✅ Comentários de Eventos (Comments) cadastrados com sucesso!');

        // 2. HABILITA AS VERIFICAÇÕES DE CHAVE ESTRANGEIRA
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}