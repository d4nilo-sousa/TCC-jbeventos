<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Coordinator;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    // Lista todos os eventos com seus coordenadores e cursos associados
    public function index()
    {
        $events = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse'])->where('visible_event', true)->get(); // Uso do Where pra listar apenas eventos que não foram ocultos
        return view('events.index', compact('events'));
    }

    // Exibe o formulário para criação de um novo evento
    public function create()
    {
        $categories = Category::all(); // Carrega todas as categorias
        $minExpiredAt = Carbon::now()->format('Y-m-d\TH:i'); // Define a data mínima permitida para expiração
        $eventExpiredAt = ''; // Inicializa como vazio para evitar erro na view

        return view('coordinator.events.create', compact('categories', 'minExpiredAt', 'eventExpiredAt'));
    }

    // Armazena um novo evento no banco de dados
    public function store(Request $request)
    {
        // Validação dos campos do formulário
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

        // Obtém o coordenador autenticado
        $coordinator = auth()->user()->coordinator;
        if (!$coordinator) {
            abort(403, 'Usuário não está vinculado a um coordenador.');
        }

        // Coleta os dados do formulário
        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
            'event_image',
            'visible_event',
        ]);

        // Armazena a imagem, se enviada
        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        // Armazena o Id e o Tipo do Coordenador
        $data['coordinator_id'] = $coordinator->id;
        $data['event_type'] = $coordinator->coordinator_type;

        // Verifica se o coordenador é de curso e se ele coordena algum, se for verdadeiro armazena o id do curso
        if ($coordinator->coordinator_type === 'course' && $coordinator->coordinatedCourse) {
            $data['course_id'] = $coordinator->coordinatedCourse->id;
        }

        // Cria o evento
        $event = Event::create($data);

        // Associa categorias ao evento
        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        // Formata datas, se estiverem presentes
        if (!empty($data['event_scheduled_at'])) {
            $data['event_scheduled_at'] = Carbon::createFromFormat('Y-m-d\TH:i', $data['event_scheduled_at'])->format('Y-m-d H:i:s');
        }

        if (!empty($data['event_expired_at'])) {
            $data['event_expired_at'] = Carbon::createFromFormat('Y-m-d\TH:i', $data['event_expired_at'])->format('Y-m-d H:i:s');
        }

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    // Exibe os detalhes de um evento específico
    public function show($id)
    {
        // Obtem o usuário autenticado
        $user = auth()->user();

        // Carrega o evento com coordenador, categorias e curso
        $event = Event::with(['eventCoordinator.userAccount', 'eventCategories', 'eventCourse'])->findOrFail($id);


        // Busca todas as reações desse usuário para esse evento
        $userReactions = \App\Models\EventUserReaction::where('event_id', $id)
            ->where('user_id', $user->id)
            ->pluck('reaction_type')
            ->toArray();

        return view('events.show', compact('event', 'userReactions', 'user'));
    }

    // Exibe o formulário para edição de um evento
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $minExpiredAt = Carbon::now()->format('Y-m-d\TH:i');

        // Converte a data de expiração para o formato aceito pelo input type="datetime-local"
        $eventExpiredAt = $event->event_expired_at
            ? Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i')
            : '';

        // Verifica se o usuário autenticado é o coordenador do evento
        $authCoordinator = auth()->user()->coordinator;
        if (!$authCoordinator || $authCoordinator->id !== $event->coordinator_id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        $categories = Category::all();
        return view('coordinator.events.edit', compact('event', 'categories', 'minExpiredAt', 'eventExpiredAt'));
    }

    // Atualiza os dados de um evento
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Validação dos campos
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

        // Verifica se o usuário tem permissão
        $coordinator = auth()->user()->coordinator;
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        // Coleta os dados do formulário
        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
            'event_image',
            'visible_event',
        ]);

        // Substitui imagem anterior, se uma nova foi enviada
        if ($request->hasFile('event_image')) {
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = $request->file('event_image')->store('event_images', 'public');
        }

        // Atualiza o evento
        $event->update($data);

        // Atualiza as categorias associadas
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

        // Verifica se o usuário autenticado é o coordenador do evento
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Você não tem permissão para excluir este evento.");
        }

        // Exclui a imagem do evento, se houver
        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        // Exclui o evento do banco
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }
}
