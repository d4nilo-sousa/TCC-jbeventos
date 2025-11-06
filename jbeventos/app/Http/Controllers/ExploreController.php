<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Course;
use App\Models\Coordinator;
use App\Models\User;
use App\Models\Post; // Importe o modelo Post
use Carbon\Carbon; // 💡 Importa Carbon para manipulação de data/hora

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Lógica de busca e recuperação de dados para Eventos
        // 🎯 ALTERADO: Lógica para buscar os 5 eventos mais curtidos no futuro
        $events = Event::with('courses')
            // 1. Conta o número de curtidas (likes)
            ->withCount('likes')
            ->when($search, function ($query, $search) {
                return $query->where('event_name', 'like', "%{$search}%")
                    ->orWhere('event_description', 'like', "%{$search}%");
            })
            // Filtra apenas eventos agendados para HOJE ou para o FUTURO (mantido)
            ->where('event_scheduled_at', '>=', Carbon::now())
            // 2. Ordena pela contagem de curtidas (do maior para o menor)
            ->orderByDesc('likes_count')
            // 3. Ordena pela data mais próxima como desempate (se houver empate em curtidas)
            ->orderBy('event_scheduled_at', 'asc')
            // 4. Limita o resultado aos 5 mais populares
            ->take(5)
            ->get();

        // Lógica de busca e recuperação de dados para Cursos 🚀 NOVO: Top 5 mais seguidos
        $courses = Course::
            // 1. Conta o número de seguidores (followers)
            withCount('followers')
            ->when($search, function ($query, $search) {
                return $query->where('course_name', 'like', "%{$search}%");
            })
            // 2. Ordena pela contagem de seguidores (do maior para o menor)
            ->orderByDesc('followers_count')
            // 3. Limita o resultado aos 5 mais seguidos
            ->take(5)
            ->get();

        $coordinators = Coordinator::with('userAccount', 'course')
            ->withCount('managedEvents') // ✅ aqui está o nome certo
            ->when($search, function ($query, $search) {
                return $query->whereHas('userAccount', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('managed_events_count') // ✅ observe o _count gerado pelo withCount
            ->take(5)
            ->get();

        // 🚀 Lógica para buscar os posts (Top 3 Discussões) (MANTIDO)
        $posts = Post::with('author', 'course')
            // 1. Conta o número de respostas (assume que o relacionamento se chama 'replies')
            ->withCount('replies')

            ->when($search, function ($query, $search) {
                return $query->where('content', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            // 2. Ordena pela contagem de respostas em ordem decrescente (do maior para o menor)
            ->orderByDesc('replies_count')
            // 3. Limita o resultado a 3 posts
            ->take(3)
            ->get();


        // Passa todas as variáveis para a view
        return view('explore.index', compact('events', 'courses', 'coordinators', 'posts'));
    }
}
