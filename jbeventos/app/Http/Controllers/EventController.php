<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Category;
use App\Models\User;
use App\Notifications\NewEventNotification;
use App\Notifications\EventReminderNotification;
use App\Notifications\WeeklyEventsSumaryNotification;
use App\Notifications\EventUpdatedNotification;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Events\EventCreated;
use App\Events\EventDeleted;
use App\Events\EventUpdated;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $loggedCoordinator = auth()->user()->coordinator;

        // Query base com relacionamentos
        $events = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse']);


        if ($loggedCoordinator) {
            if ($request->status === 'visible') {
                // Eventos visíveis do coordenador logado
                $events->where('coordinator_id', $loggedCoordinator->id)
                    ->where('visible_event', true);
            } elseif ($request->status === 'hidden') {
                // Eventos ocultos do coordenador logado
                $events->where('coordinator_id', $loggedCoordinator->id)
                    ->where('visible_event', false);
            } else {
                // Sem filtro: eventos visíveis de todos + eventos do coordenador logado (ocultos ou visíveis)
                $events->where(function ($query) use ($loggedCoordinator) {
                    $query->where('visible_event', true)
                        ->orWhere('coordinator_id', $loggedCoordinator->id);
                });
            }
        } else {
            // Usuários que não são coordenadores veem apenas eventos visíveis
            $events->where('visible_event', true);
        }

        // Filtros adicionais
        if ($request->filled('event_type')) {
            $events->where('event_type', $request->event_type);
        }

        if ($request->filled('course_id')) {
            $events->whereIn('course_id', $request->course_id);
        }

        if ($request->filled('category_id')) {
            $events->whereHas('eventCategories', function ($q) use ($request) {
                $q->whereIn('categories.id', $request->category_id);
            });
        }

        if ($request->filled('start_date')) {
            $events->whereDate('event_scheduled_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $events->whereDate('event_scheduled_at', '<=', $request->end_date);
        }

        // Ordenação por curtidas
        if ($request->filled('likes_order')) {
            $events->withCount([
                'reactions as likes_count' => function ($query) {
                    $query->where('reaction_type', 'like');
                }
            ]);

            if ($request->likes_order === 'most') {
                $events->orderBy('likes_count', 'desc');
            } elseif ($request->likes_order === 'least') {
                $events->orderBy('likes_count', 'asc');
            }
        }

        // Ordenação por agendamento
        if ($request->filled('schedule_order')) {
            if ($request->schedule_order === 'soonest') {
                $events->orderBy('event_scheduled_at', 'asc');
            } elseif ($request->schedule_order === 'latest') {
                $events->orderBy('event_scheduled_at', 'desc');
            }
        } else {
            $events->orderBy('created_at', 'desc');
        }

        // Filtro por search
        if ($search = $request->input('search')) {
            $events->where('event_name', 'like', "%{$search}%"); // substitua 'name' pelo campo correto
        }

        // Executa a query
        $events = $events->get();

        // Busca cursos e categorias
        $courses = Course::all();
        $categories = Category::all();

        return view('events.index', compact('events', 'courses', 'categories', 'loggedCoordinator'));
    }

    // Formulário de criação
    public function create()
    {
        $categories = Category::all();
        $minExpiredAt = Carbon::now()->format('Y-m-d\TH:i');
        $eventExpiredAt = '';

        return view('coordinator.events.create', compact('categories', 'minExpiredAt', 'eventExpiredAt'));
    }

    // Armazena evento
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|unique:events,event_name',
            'event_description' => 'nullable|string',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date_format:Y-m-d\TH:i|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $coordinator = auth()->user()->coordinator;
        if (!$coordinator) {
            abort(403, 'Usuário não está vinculado a um coordenador.');
        }

        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
            'event_image',
            'visible_event',
        ]);

        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        $data['coordinator_id'] = $coordinator->id;
        $data['event_type'] = $coordinator->coordinator_type;

        if ($coordinator->coordinator_type === 'course' && $coordinator->coordinatedCourse) {
            $data['course_id'] = $coordinator->coordinatedCourse->id;
        }

        $event = Event::create($data);

        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        if ($event->course && $event->course->followers) {
            Notification::send($event->course->followers, new NewEventNotification($event));
        }

        if ($event->notifiableUsers->isNotEmpty()) {
            Notification::send($event->notifiableUsers, new NewEventNotification($event));
        }

        broadcast(new EventCreated($event))->toOthers();

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    // Detalhes do evento
    public function show(Event $event)
    {
        $user = auth()->user();
        $event->load(['eventCoordinator.userAccount', 'eventCategories', 'eventCourse']);

        $userReactions = \App\Models\EventUserReaction::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->pluck('reaction_type')
            ->toArray();

        return view('events.show', compact('event', 'userReactions', 'user'));
    }

    // Formulário de edição
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $minExpiredAt = Carbon::now()->format('Y-m-d\TH:i');
        $eventExpiredAt = $event->event_expired_at
            ? Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i')
            : '';

        $authCoordinator = auth()->user()->coordinator;
        if (!$authCoordinator || $authCoordinator->id !== $event->coordinator_id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        $categories = Category::all();
        return view('coordinator.events.edit', compact('event', 'categories', 'minExpiredAt', 'eventExpiredAt'));
    }

    // Atualiza evento
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'event_name' => 'required|string|unique:events,event_name,' . $event->id,
            'event_description' => 'nullable|string',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date_format:Y-m-d\TH:i|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $coordinator = auth()->user()->coordinator;
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
            'event_image',
            'visible_event',
        ]);

        if ($request->hasFile('event_image')) {
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = optional($coordinator->coordinatedCourse)->id;

        $originalData = $event->getOriginal();
        $event->fill($data);
        $changed = array_diff_assoc($event->getAttributes(), $originalData);

        $event->update($data);

        $changed = $event->getChanges();
        unset($changed['updated_at'], $changed['created_at']);

        if (!empty($changed)) {
            if ($event->course && $event->course->followers->isNotEmpty()) {
                Notification::send($event->course->followers, new EventUpdatedNotification($event, $changed));
            }

            if ($event->notifiableUsers->isNotEmpty()) {
                Notification::send($event->notifiableUsers, new EventUpdatedNotification($event, $changed));
            }
        }

        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    // Exclui um evento
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $coordinator = auth()->user()->coordinator;

        // Verifica permissão
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Você não tem permissão para excluir este evento.");
        }

        // Exclui a imagem, se houver
        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        // Guarda o ID para o evento de broadcast
        $eventId = $event->id;

        // Deleta o evento do banco
        $event = $event->delete();

        broadcast(new EventDeleted($eventId))->toOthers();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }
}
