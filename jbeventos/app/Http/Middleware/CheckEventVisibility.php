<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEventVisibility
{
    /**
     * Verifica se o evento pode ser acessado de acordo com sua visibilidade
     * e permissões do usuário.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtém o evento a partir da rota
        $event = $request->route('event');

        // Se não encontrou o evento, retorna erro 404
        if (!$event) {
            abort(404, 'Evento não encontrado');
        }

        // Se o evento está oculto
        if (!$event->visible_event) {

            $user = auth()->user();

            // Permite acesso apenas se for o coordenador responsável pelo evento
            if (
                !$user || // Não está logado
                $user->user_type !== 'coordinator' || // Não é coordenador
                $user->coordinator->id !== $event->coordinator_id // Não é o coordenador do evento
            ) {
                abort(403, 'Você não tem permissão pra acessar este evento');
            }
        }

        // Se passou por todas as verificações, segue o fluxo normalmente
        return $next($request);
    }
}
