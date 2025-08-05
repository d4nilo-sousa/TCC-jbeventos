<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Category;
use App\Models\User;
use App\Notifications\NewEventNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Exibe a lista de todos os eventos com seus coordenadores e cursos associados.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $events = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse'])->get();
        return view('events.index', compact('events'));
    }

    /**
     * Exibe o formulário para criação de um novo evento.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        $minExpiredAt = Carbon::now()->format('Y-m-d\TH:i');
        $eventExpiredAt = '';

        return view('coordinator.events.create', compact('categories', 'minExpiredAt', 'eventExpiredAt'));
    }

    /**
     * Valida e armazena um novo evento no banco de dados.
     * Envia notificações para seguidores e usuários notificados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
        $data['course_id'] = optional($coordinator->coordinatedCourse)->id;

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

        if (!empty($data['event_scheduled_at'])) {
            $data['event_scheduled_at'] = Carbon::createFromFormat('Y-m-d\TH:i', $data['event_scheduled_at'])->format('Y-m-d H:i:s');
        }

        if (!empty($data['event_expired_at'])) {
            $data['event_expired_at'] = Carbon::createFromFormat('Y-m-d\TH:i', $data['event_expired_at'])->format('Y-m-d H:i:s');
        }

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um evento específico, incluindo reações do usuário autenticado.
     *
     * @param  int|string  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = auth()->user();

        $event = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse', 'eventCategories'])->findOrFail($id);

        $userReactions = \App\Models\EventUserReaction::where('event_id', $id)
                        ->where('user_id', $user->id)
                        ->pluck('reaction_type')
                        ->toArray();

        return view('events.show', compact('event', 'userReactions', 'user'));
    }

    /**
     * Exibe o formulário para edição de um evento, verificando permissões.
     *
     * @param  int|string  $id
     * @return \Illuminate\View\View
     */
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

    /**
     * Atualiza os dados de um evento, incluindo imagem e categorias, verificando permissões.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

        $event->update($data);

        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Exclui um evento após verificar se o usuário é o coordenador responsável.
     *
     * @param  int|string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $coordinator = auth()->user()->coordinator;

        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Você não tem permissão para excluir este evento.");
        }

        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }
}
