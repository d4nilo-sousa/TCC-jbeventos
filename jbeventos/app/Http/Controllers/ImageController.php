<?php

namespace App\Http\Controllers;

use App\Models\Event; // Necessário para a capa do evento (event_image)
use App\Models\EventImage;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log; // Para logar erros

class ImageController extends Controller
{
    /**
     * Exclui uma imagem da GALERIA (EventImage model) do evento via AJAX.
     * Esta rota é: DELETE /event-images/{id}
     */
    public function destroyEventImage($id): JsonResponse
    {
        $image = EventImage::findOrFail($id);
        $event = $image->event; // Assume que EventImage tem uma relação belongsTo com Event

        // 1. AUTORIZAÇÃO: Verifica se o usuário logado é o coordenador do evento
        $coordinator = auth()->user()->coordinator;
        if (!$coordinator || !$event || $event->coordinator_id !== $coordinator->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $path = $image->image_path;

        // 2. Exclusão de Arquivo com Storage (Padrão Laravel)
        if ($path) {
            try {
                // Usa Storage para garantir a exclusão no disco correto
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
     * Esta rota deve ser criada, ex: DELETE /events/{event_id}/cover
     */
    public function removeCoverImage($event_id): JsonResponse
    {
        // Usa o modelo Event e o ID do evento
        $event = Event::findOrFail($event_id);
        
        // 1. AUTORIZAÇÃO: Verifica se o usuário logado é o coordenador do evento
        $coordinator = auth()->user()->coordinator;
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
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
     * Esta rota deve ser criada, ex: DELETE /courses/{id}/{type}
     */
    public function destroyCourseImage($id, $type): JsonResponse
    {
        if (!in_array($type, ['icon', 'banner'])) {
            return response()->json(['error' => 'Tipo de imagem inválido.'], 400);
        }

        $course = Course::findOrFail($id); 

        // 1. AUTORIZAÇÃO: Verifica se o usuário logado é o coordenador do curso
        $coordinator = auth()->user()->coordinator;
        if (!$coordinator || $course->coordinator_id !== $coordinator->id) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

        $column = $type === 'icon' ? 'course_icon' : 'course_banner';
        $path = $course->$column; 

        // 2. Exclusão de Arquivo com Storage (Padrão Laravel)
        if ($path) {
            try {
                // Substitui a chamada 'unlink(public_path(...))' pela Facade Storage
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