<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    /**
     * Exibe a tela de Feed.
     */
     public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Coleta de Eventos (apenas visíveis. Removido o filtro de data/expiração)
        $events = Event::with(['eventCourse', 'eventCoordinator.userAccount', 'images', 'reactions'])
            ->where('visible_event', true)
            ->get()
            ->map(function ($event) {
                $event->type = 'event';
                // Define a data de agendamento como base para ordenação no feed
                $event->sort_date = $event->event_scheduled_at; 
                return $event;
            });

        // 2. Posts serão carregados e gerenciados pelo componente Livewire FeedPosts.
        $posts = Post::select('id', 'created_at')->get() // Busca apenas o necessário para ordenar
            ->map(function ($post) {
                $post->type = 'post';
                $post->sort_date = $post->created_at; 
                return $post;
            });

        // 3. Combina e Ordena (do mais novo/agendado para o mais antigo)
        $feedItems = $events->merge($posts)
            ->sortByDesc('sort_date')
            ->values();

        // Lógica para o Modal de Boas-Vindas (usando a sessão)
        $isFirstLogin = !$request->session()->has('has_seen_welcome_modal');
        if ($isFirstLogin) {
            $request->session()->put('has_seen_welcome_modal', true);
        }

        return view('feed.index', [
            'feedItems' => $feedItems,
            'isFirstLogin' => $isFirstLogin,
            'user' => $user, 
        ]);
    }
}