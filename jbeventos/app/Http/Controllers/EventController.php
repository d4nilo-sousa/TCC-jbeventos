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
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $loggedCoordinator = auth()->user()->coordinator;

        // Inicia a query com os relacionamentos necessários para a view.
        $query = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse']);

        // Lógica principal de visibilidade baseada no tipo de usuário e filtro.
        if ($loggedCoordinator) {
            if ($request->status === 'visible') {
                $query->where('coordinator_id', $loggedCoordinator->id)
                    ->where('visible_event', true);
            } elseif ($request->status === 'hidden') {
                $query->where('coordinator_id', $loggedCoordinator->id)
                    ->where('visible_event', false);
            } else {
                $query->where(function ($q) use ($loggedCoordinator) {
                    $q->where('visible_event', true)
                    ->orWhere('coordinator_id', $loggedCoordinator->id);
                });
            }
        } else {
            $query->where('visible_event', true);
        }
        
        // --- Aplica os filtros adicionais (encadeados na mesma query) ---

        // Filtro por tipo de evento
        $query->when($request->filled('event_type'), function ($q) use ($request) {
            $q->where('event_type', $request->event_type);
        });

        // Filtro por curso
        $query->when($request->filled('course_id'), function ($q) use ($request) {
            $q->whereIn('course_id', $request->course_id);
        });

        // Filtro por categoria
        $query->when($request->filled('category_id'), function ($q) use ($request) {
            $q->whereHas('eventCategories', function ($subQuery) use ($request) {
                $subQuery->whereIn('categories.id', $request->category_id);
            });
        });

        // Filtro por intervalo de datas
        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->whereDate('event_scheduled_at', '>=', $request->start_date);
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->whereDate('event_scheduled_at', '<=', $request->end_date);
        });

        // --- Aplica a ordenação ---

        // Ordenação por curtidas
        if ($request->filled('likes_order')) {
            $query->withCount([
                'reactions as likes_count' => function ($subQuery) {
                    $subQuery->where('reaction_type', 'like');
                }
            ]);
            
            $query->orderBy('likes_count', $request->likes_order === 'most' ? 'desc' : 'asc');
        }

        // Ordenação por agendamento
        if ($request->filled('schedule_order')) {
            $query->orderBy('event_scheduled_at', $request->schedule_order === 'soonest' ? 'asc' : 'desc');
        } else {
            // Ordenação padrão: mais recente primeiro
            $query->orderBy('created_at', 'desc');
        }

        // Filtro de pesquisa (usando 'when' para ser opcional)
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->input('search');
            $q->where('event_name', 'like', "%{$search}%");
        });
        
        // Executa a query com paginação e preserva os filtros na URL
        $events = $query->paginate(20)->withQueryString();

        // Busca cursos e categorias (sem filtros)
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

    // Cria o evento
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
        if (!$coordinator) abort(403, 'Usuário não está vinculado a um coordenador.');

        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
            'visible_event'
        ]);


        // Capa do evento
        if ($request->hasFile('event_image')) {
            $upload = $request->file('event_image');

            $image = Image::read($upload);

            $image->resize(1200, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $width = $image->width();
            $height = $image->height();
            $cropX = max(0, intval(($width - 1200) / 2));
            $cropY = max(0, intval(($height - 600) / 2));
            $image->crop(1200, 600, $cropX, $cropY);

            $imageName = time() . '_' . $upload->getClientOriginalName();
            $path = storage_path('app/public/event_images/' . $imageName);

            $ext = strtolower($upload->getClientOriginalExtension());
            if (in_array($ext, ['jpg', 'jpeg'])) {
                $image->save($path, 90);
            } elseif ($ext === 'png') {
                $image->save($path, 9);
            } else {
                $image->save($path);
            }

            $data['event_image'] = 'event_images/' . $imageName;
        }

        $data['coordinator_id'] = $coordinator->id;
        $data['event_type'] = $coordinator->coordinator_type;
        $data['course_id'] = optional($coordinator->coordinatedCourse)->id;

        $event = Event::create($data);

        // Imagens extras do carrossel
        if ($request->hasFile('event_images')) {
            foreach ($request->file('event_images') as $upload) {

                $image = Image::read($upload);

                $image->resize(1200, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $width = $image->width();
                $height = $image->height();
                $cropX = max(0, intval(($width - 1200) / 2));
                $cropY = max(0, intval(($height - 600) / 2));
                $image->crop(1200, 600, $cropX, $cropY);

                $imageName = time() . '_' . $upload->getClientOriginalName();
                $path = storage_path('app/public/event_images/' . $imageName);

                $ext = strtolower($upload->getClientOriginalExtension());
                if (in_array($ext, ['jpg', 'jpeg'])) {
                    $image->save($path, 90);
                } elseif ($ext === 'png') {
                    $image->save($path, 9);
                } else {
                    $image->save($path);
                }

                $event->images()->create([
                    'image_path' => 'event_images/' . $imageName
                ]);
            }
        }

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

       // Carrega a relação de reações do usuário
        if ($user) {
            $user->load('commentReactions');
        }

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
            'visible_event'
        ]);

        if ($request->input('remove_event_image') == '1') {
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = null;
        } else if ($request->hasFile('event_image')) {
            $upload = $request->file('event_image');

            $image = Image::read($upload);

            $image->resize(1200, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $width = $image->width();
            $height = $image->height();
            $cropX = max(0, intval(($width - 1200) / 2));
            $cropY = max(0, intval(($height - 600) / 2));
            $image->crop(1200, 600, $cropX, $cropY);

            $imageName = time() . '_' . $upload->getClientOriginalName();
            $path = storage_path('app/public/event_images/' . $imageName);

            $ext = strtolower($upload->getClientOriginalExtension());
            if (in_array($ext, ['jpg', 'jpeg'])) {
                $image->save($path, 90);
            } elseif ($ext === 'png') {
                $image->save($path, 9);
            } else {
                $image->save($path);
            }

            $data['event_image'] = 'event_images/' . $imageName;
        }

        if ($request->filled('remove_event_images')) {
            foreach ($request->input('remove_event_images') as $imageId => $remove) {
                if ($remove == '1') {
                    $image = $event->images()->find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }
        }

        if ($request->hasFile('event_images')) {
            foreach ($request->file('event_images') as $upload) {

                $image = Image::read($upload);

                $image->resize(1200, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $width = $image->width();
                $height = $image->height();
                $cropX = max(0, intval(($width - 1200) / 2));
                $cropY = max(0, intval(($height - 600) / 2));
                $image->crop(1200, 600, $cropX, $cropY);

                $imageName = time() . '_' . $upload->getClientOriginalName();
                $path = storage_path('app/public/event_images/' . $imageName);

                $ext = strtolower($upload->getClientOriginalExtension());
                if (in_array($ext, ['jpg', 'jpeg'])) {
                    $image->save($path, 90);
                } elseif ($ext === 'png') {
                    $image->save($path, 9);
                } else {
                    $image->save($path);
                }

                $event->images()->create([
                    'image_path' => 'event_images/' . $imageName
                ]);
            }
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

        return redirect()->route('coordinator.dashboard')->with('success', 'Evento excluído com sucesso!');
    }
}