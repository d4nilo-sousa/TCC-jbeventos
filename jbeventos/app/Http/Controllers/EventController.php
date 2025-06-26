<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Coordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // Lista todos os eventos com coordenador e curso
    public function index()
    {
         $events = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse'])->get();
        return view('events.index', compact('events'));
    }

    // Formulário para criar evento
    public function create()
    {
        // Carregar coordenadores com curso para o select
        $coordinators = Coordinator::with('coordinatedCourse')->get();
        return view('coordinator.events.form_events', compact('coordinators'));
    }

    // Salvar novo evento
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|unique:events,event_name',
            'event_description' => 'nullable|string',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'coordinator_id' => 'required|exists:coordinators,id',
        ]);

        $coordinator = Coordinator::findOrFail($request->coordinator_id);

        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
        ]);

        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = optional($coordinator->coordinatedCourse)->id; // Pode ser null

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    // Exibir detalhes do evento
    public function show($id)
    {
        $event = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse'])->findOrFail($id);
        return view('events.show', compact('event'));
    }

    // Formulário para editar evento
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $coordinators = Coordinator::with('coordinatedCourse')->get();
        return view('coordinator.events.edit', compact('event', 'coordinators'));
    }

    // Atualizar evento
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'event_name' => 'required|string|unique:events,event_name,' . $event->id,
            'event_description' => 'nullable|string',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'coordinator_id' => 'required|exists:coordinators,id',
        ]);

        $coordinator = Coordinator::findOrFail($request->coordinator_id);

        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
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

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    // Excluir evento
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }
}
