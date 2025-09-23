<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Course;
use App\Models\Coordinator;
use App\Models\User;
use App\Models\EventUserReaction;

class ExploreController extends Controller
{
    /**
     * Display a listing of events, courses, coordinators, and users based on search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Busca de Eventos com a contagem de curtidas
        $eventsQuery = Event::query()->withCount([
            'reactions as likes_count' => function ($query) {
                $query->where('reaction_type', 'like');
            }
        ]);
        if ($search) {
            $eventsQuery->where('event_name', 'like', '%' . $search . '%')
                        ->orWhere('event_description', 'like', '%' . $search . '%');
        }
        $events = $eventsQuery->orderBy('event_scheduled_at', 'desc')->get();

        // Busca de Cursos
        $coursesQuery = Course::query();
        if ($search) {
            $coursesQuery->where('course_name', 'like', '%' . $search . '%');
        }
        $courses = $coursesQuery->orderBy('course_name', 'asc')->get();

        // Busca de Coordenadores
        $coordinatorsQuery = Coordinator::query()->with('userAccount', 'course');
        if ($search) {
            $coordinatorsQuery->whereHas('userAccount', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('course', function ($query) use ($search) {
                $query->where('course_name', 'like', '%' . $search . '%');
            });
        }
        $coordinators = $coordinatorsQuery->orderBy('created_at', 'desc')->get();

        // Busca de Usuários Normais
        $usersQuery = User::query()->where('user_type', 'user');
        if ($search) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }
        $users = $usersQuery->orderBy('name', 'asc')->get();

        return view('explore.index', [
            'events' => $events,
            'courses' => $courses,
            'coordinators' => $coordinators,
            'users' => $users,
        ]);
    }
}