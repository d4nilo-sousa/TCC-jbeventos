<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Category;
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
        $categories = Category::all(); // carrega as categorias
        return view('coordinator.events.create', compact('categories')); //compact é usado para passar variáveis para a view
        
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
            'categories' => 'array',
            'categories.*' => 'exists:categories,id', // Valida se as categorias existem
        ]);

        // Pega o coordenador autenticado
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

        // Verifica se o usuário enviou uma imagem e a armazena
        // Se o usuário não enviar uma imagem, o campo event_image será null 
        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        // Define o coordenador e o curso associado
        // Se o coordenador não tiver curso associado, course_id será null
        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = optional($coordinator->coordinatedCourse)->id; 

        $event = Event::create($data); // Cria o evento com os dados validados

        // sincroniza as categorias selecionadas
        if($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories')); // Sincroniza as categorias selecionadas (categories); 
        } else{
            $event->eventCategories()->detach(); // Desassocia categorias se nenhuma for selecionada
        }

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    // Exibir detalhes do evento
    public function show($id)
    {
        // Carrega o evento com coordenador e curso associados
        $event = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse', 'eventCategories'])->findOrFail($id);
        return view('events.show', compact('event'));
    }

    // Formulário para editar evento
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        // Verifica se o usuário autenticado é o dono(coordenador) do evento
        $authCoordinator = auth()->user()->coordinator;
        if(!$authCoordinator || $authCoordinator->id !== $event->coordinator_id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        $categories = Category::all();
        return view('coordinator.events.edit', compact('event', 'categories')); //passando as variáveis para a view
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
            'categories' => 'array',
            'categories.*' => 'exists:categories,id', // Valida se as categorias existem
        ]);

        // Pega o coordenador autenticado
        $coordinator = auth()->user()->coordinator;
        if(!$coordinator || $event->coordinator_id !== $coordinator->id) {
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

        // sincroniza as categorias selecionadas
        if($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));  
        } else{
            $event->eventCategories()->detach(); // Desassocia categorias se nenhuma for selecionada
        }

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    // Excluir evento
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $coordinator = auth()->user()->coordinator;

        // Verifica se o usuário autenticado é o dono(coordenador) do evento
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
