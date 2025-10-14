<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Post; // Removido da lógica de feed
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    /**
     * Exibe a tela de Feed.
     */
     public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Coleta de Eventos (apenas visíveis e ordenados pelo agendamento mais recente)
        $events = Event::with(['eventCoordinator.userAccount', 'images', 'reactions'])
            ->latest('event_scheduled_at') // Ordena eventos por agendamento mais recente
            ->get()
            ->map(function ($event) {
                $event->type = 'event';
                // Define a data de agendamento como base para ordenação no feed
                $event->sort_date = $event->event_scheduled_at; 
                return $event;
            });

        // 2. Posts: A listagem e paginação está sendo feita inteiramente pelo componente Livewire app/livewire/FeedPosts.php
        // Não é necessário buscar nem misturar posts aqui.

        // 3. O $feedItems conterá apenas os eventos para a coluna da esquerda.
        $feedItems = $events->values();

        // Lógica para o Modal de Boas-Vindas (usando a sessão)
        $isFirstLogin = !$request->session()->has('has_seen_welcome_modal');
        if ($isFirstLogin) {
            $request->session()->put('has_seen_welcome_modal', true);
        }

        return view('feed.index', [
            'feedItems' => $feedItems,
            'isFirstLogin' => $isFirstLogin,
            'user' => $user, 
            // Variável 'posts' removida.
        ]);
    }
}