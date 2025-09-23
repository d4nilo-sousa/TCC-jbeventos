<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Course;
use App\Models\Coordinator;
use App\Models\User;
use App\Models\Post; // Importe o modelo Post

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Lógica de busca e recuperação de dados
        $events = Event::with('course')
            ->when($search, function ($query, $search) {
                return $query->where('event_name', 'like', "%{$search}%")
                             ->orWhere('event_description', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $courses = Course::when($search, function ($query, $search) {
            return $query->where('course_name', 'like', "%{$search}%");
        })
        ->latest()
        ->get();

        // Alterado para buscar apenas coordenadores
        $coordinators = Coordinator::with('userAccount', 'course')
            ->when($search, function ($query, $search) {
                return $query->whereHas('userAccount', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Nova lógica para buscar os posts
        $posts = Post::with('author', 'course')
            ->when($search, function ($query, $search) {
                return $query->where('content', 'like', "%{$search}%")
                             ->orWhereHas('author', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->get();


        // Passa todas as variáveis para a view
        return view('explore.index', compact('events', 'courses', 'coordinators', 'posts'));
    }
}