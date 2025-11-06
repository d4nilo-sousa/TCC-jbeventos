<?php

namespace App\Http\Controllers;

use App\Models\Event; 
use App\Models\EventImage;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    /**
     * Helper para verificar se o usuário logado é Administrador.
     * Baseado na coluna 'user_type' na tabela 'users'.
     */
    private function userIsAdmin(): bool
    {
        // Retorna true se o usuário estiver logado E o user_type for 'admin'
        return optional(auth()->user())->user_type === 'admin';
    }

    /**
     * Exclui uma imagem da GALERIA (EventImage model) do evento via AJAX.
     * Esta rota é: DELETE /event-images/{id}
     * Permissão: SOMENTE Coordenador do Evento.
     */
    public function destroyEventImage($id): JsonResponse
    {
        $image = EventImage::findOrFail($id);
        $event = $image->event; 

        // 1. AUTORIZAÇÃO: Verifica se o usuário logado é o coordenador do evento
        // Usamos optional() para evitar erro se auth()->user() for null
        $coordinatorId = optional(auth()->user()->coordinator)->id;
        
        // Verifica se existe coordenador logado E se o ID do coordenador do evento bate
        if (!$coordinatorId || !$event || $event->coordinator_id !== $coordinatorId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $path = $image->image_path;

        // 2. Exclusão de Arquivo com Storage (Padrão Laravel)
        if ($path) {
            try {
                Storage::disk('public')->delete($path);
            } catch (\Exception $e) {
                Log::warning("Falha ao excluir o arquivo {$path} da galeria do evento {$event->id}.");
            }
        }

        // 3. Exclui o registro no banco
        $image->delete();

        return response()->json(['success' => true]);
    }

// --------------------------------------------------------------------------

    /**
     * Exclui a imagem de CAPA (coluna event_image) de um evento via AJAX.
     * Permissão: SOMENTE Coordenador do Evento.
     */
    public function removeCoverImage($event_id): JsonResponse
    {
        $event = Event::findOrFail($event_id);
        
        // 1. AUTORIZAÇÃO: Verifica se o usuário logado é o coordenador do evento
        $coordinatorId = optional(auth()->user()->coordinator)->id;
        
        if (!$coordinatorId || $event->coordinator_id !== $coordinatorId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $old_image_path = $event->event_image;

        // 2. Zera o campo no banco
        $event->event_image = null;
        $event->save();

        // 3. Exclusão de Arquivo com Storage (Padrão Laravel)
        if ($old_image_path) {
            try {
                Storage::disk('public')->delete($old_image_path);
            } catch (\Exception $e) {
                Log::warning("Falha ao excluir o arquivo de capa do evento {$event_id}: {$old_image_path}.");
            }
        }
        
        return response()->json(['success' => true]);
    }

// --------------------------------------------------------------------------

    /**
     * Exclui o ÍCONE ou BANNER de um curso via AJAX.
     * Permissão: SOMENTE Administrador.
     */
    public function destroyCourseImage($id, $type): JsonResponse
    {
        if (!in_array($type, ['icon', 'banner'])) {
            return response()->json(['error' => 'Tipo de imagem inválido.'], 400);
        }

        $course = Course::findOrFail($id); 
        
        // ***************************************************************
        // NOVO: AUTORIZAÇÃO APENAS PARA ADMINISTRADOR (user_type = 'admin')
        $userIsAdmin = $this->userIsAdmin();

        if (!$userIsAdmin) {
            // Loga a tentativa de acesso negado para ajudar no diagnóstico
            $userId = optional(auth()->user())->id;
            $userType = optional(auth()->user())->user_type ?? 'guest'; // Pega o tipo de usuário para o log
            Log::warning("403 Acesso Negado em {$type} do curso {$id}. UserID: {$userId}. Tipo de Usuário: {$userType}. Requer Admin.");
             
            return response()->json(['message' => 'Unauthorized: Only Administrator can delete course image.'], 403);
        }
        // ***************************************************************
        
        $column = $type === 'icon' ? 'course_icon' : 'course_banner';
        $path = $course->$column; 

        // 2. Exclusão de Arquivo com Storage (Padrão Laravel)
        if ($path) {
            try {
                Storage::disk('public')->delete($path);
            } catch (\Exception $e) {
                Log::warning("Falha ao excluir o {$type} do curso {$id}: {$path}.");
            }
        }

        // 3. Atualiza o registro no banco, setando a coluna para NULL
        $course->update([$column => null]);

        return response()->json(['success' => true]);
    }
}
