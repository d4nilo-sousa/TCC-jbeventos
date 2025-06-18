<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Coordinator;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Lista todos os eventos
    public function index()
    {
        // Carrega todos os eventos com coordenador e curso para evitar N+1
        $events = Event::with('eventCoordinator.course')->get();
        return view('coordinator.events.index', compact('events'));
    }

    // Exibe o formulário para criar um evento
    public function create()
    {
        return view('coordinator.events.form_events');
    }

    // Salva novo evento no banco
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

        // Busca o coordenador para puxar curso e tipo
        $coordinator = Coordinator::with('course')->findOrFail($request->coordinator_id);

        $data = $request->all();

        // Se enviou imagem, salva na storage/public/event_images
        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        // Setar os campos coordenador e curso automaticamente
        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = $coordinator->course_id;

        // Cria o evento
        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    // Exibe os detalhes de um evento específico
    public function show($id)
    {
        $event = Event::with('eventCoordinator.course')->findOrFail($id);
        return view('coordinator.events.show', compact('event'));
    }

    // Exibe formulário para editar evento
    public function edit($id)
    {
        $event = Event::with('eventCoordinator.course')->findOrFail($id);
        return view('coordinator.events.edit', compact('event'));
    }

    // Atualiza o evento
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

        $coordinator = Coordinator::with('course')->findOrFail($request->coordinator_id);

        $data = $request->all();

        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = $coordinator->course_id;

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    // Exclui o evento
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }
}
