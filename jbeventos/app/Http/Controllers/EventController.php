<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Coordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // Lista todos os eventos com coordenador e curso (se houver)
    public function index()
    {
        $events = Event::with(['coordinator.course'])->get();
        return view('coordinator.events.index', compact('events'));
    }

    // Formulário para criar evento
    public function create()
    {
        // Carregar coordenadores com curso para preencher select
        $coordinators = Coordinator::with('course')->get();
        return view('coordinator.events.form_events', compact('coordinators'));
    }

    // Salvar novo evento
    public function store(Request $request)
    {
        // Validação dos dados do evento
        $request->validate([
            'event_name' => 'required|unique:events,event_name',
            'event_description' => 'nullable|string',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'coordinator_id' => 'required|exists:coordinators,id',
        ]);

        // Verifica se o coordenador existe
        $coordinator = Coordinator::findOrFail($request->coordinator_id);

        // Coleta os dados do formulário
        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
        ]);

        // Upload da imagem (se houver)
        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        // Setar coordenador e curso (nullable)
        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = $coordinator->course_id; // pode ser null

        // Cria o evento
        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    // Exibir detalhes de evento
    public function show($id)
    {
        // Busca o evento com coordenador e curso (se houver)
        $event = Event::with(['coordinator.course'])->findOrFail($id);
        return view('coordinator.events.show', compact('event'));
    }

    // Formulário para editar evento
    public function edit($id)
    {
        // Busca o evento e carrega coordenadores com curso para preencher select
        $event = Event::findOrFail($id);
        $coordinators = Coordinator::with('course')->get();
        return view('coordinator.events.edit', compact('event', 'coordinators'));
    }

    // Atualizar evento
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Validação dos dados do evento
        $request->validate([
            'event_name' => 'required|string|unique:events,event_name,' . $event->id,
            'event_description' => 'nullable|string',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'coordinator_id' => 'required|exists:coordinators,id',
        ]);

        // Verifica se o coordenador existe
        $coordinator = Coordinator::findOrFail($request->coordinator_id);

        // Coleta os dados do formulário
        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
        ]);

        if ($request->hasFile('event_image')) {
            // Apaga imagem antiga se existir
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = $coordinator->course_id; // nullable

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    // Excluir evento
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        // Apaga imagem se existir
        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        $event->delete(); // Exclui o evento

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }
}
