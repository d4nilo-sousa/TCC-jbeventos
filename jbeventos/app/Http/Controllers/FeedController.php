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

        // 1. Coleta de Eventos (apenas visíveis e não expirados)
        // Eager Loading: Carrega Curso, Coordenador (com UserAccount), Imagens e Reações.
        $events = Event::with(['eventCourse.courseCoordinator.userAccount', 'images', 'reactions'])
            ->where('visible_event', true)
            // Filtra eventos agendados para agora ou futuro.
            ->where(function ($query) {
                $query->whereNull('event_expired_at')
                      ->orWhere('event_expired_at', '>', now());
            })
            ->get()
            ->map(function ($event) {
                $event->type = 'event';
                // Define a data de agendamento como base para ordenação no feed
                $event->sort_date = $event->event_scheduled_at; 
                return $event;
            });

        // 2. Coleta de Posts (Todos, ordenados por data de criação)
        // Eager Loading: Carrega o Curso e o Autor do Post.
        $posts = Post::with(['course.courseCoordinator.userAccount', 'author'])
            ->get()
            ->map(function ($post) {
                $post->type = 'post';
                // Define a data de criação como base para ordenação no feed
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
            // Marca a sessão para que o modal não seja exibido novamente.
            $request->session()->put('has_seen_welcome_modal', true);
        }

        return view('feed.index', [
            'feedItems' => $feedItems,
            'isFirstLogin' => $isFirstLogin,
            'user' => $user, 
        ]);
    }
}
