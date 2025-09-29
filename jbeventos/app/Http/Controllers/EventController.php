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

        // Query base com relacionamentos
        $events = Event::with(['eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse', 'eventCategories']);

        // Filtragem por visibilidade do coordenador
        if ($loggedCoordinator) {
            if ($request->status === 'visible') {
                $events = $events->where('coordinator_id', $loggedCoordinator->id)
                    ->where('visible_event', true);
            } elseif ($request->status === 'hidden') {
                $events = $events->where('coordinator_id', $loggedCoordinator->id)
                    ->where('visible_event', false);
            } else {
                $events = $events->where('visible_event', true);
            }
        } else {
            $events = $events->where('visible_event', true);
        }

        // Filtros adicionais
        if ($request->filled('event_type')) {
            $events = $events->where('event_type', $request->event_type);
        }

        if ($request->filled('course_id')) {
            $events = $events->whereIn('course_id', $request->course_id);
        }

        if ($request->filled('category_id')) {
            $events = $events->whereHas('eventCategories', function ($q) use ($request) {
                $q->whereIn('categories.id', $request->category_id);
            });
        }

        if ($request->filled('start_date')) {
            $events = $events->whereDate('event_scheduled_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $events = $events->whereDate('event_scheduled_at', '<=', $request->end_date);
        }

        // Ordenação por curtidas
        if ($request->filled('likes_order')) {
            $events = $events->withCount([
                'reactions as likes_count' => function ($query) {
                    $query->where('reaction_type', 'like');
                }
            ]);

            if ($request->likes_order === 'most') {
                $events = $events->orderBy('likes_count', 'desc');
            } elseif ($request->likes_order === 'least') {
                $events = $events->orderBy('likes_count', 'asc');
            }
        }

        // Ordenação por agendamento
        if ($request->filled('schedule_order')) {
            if ($request->schedule_order === 'soonest') {
                $events = $events->orderBy('event_scheduled_at', 'asc');
            } elseif ($request->schedule_order === 'latest') {
                $events = $events->orderBy('event_scheduled_at', 'desc');
            }
        } else {
            $events = $events->orderBy('created_at', 'desc');
        }

        // Pesquisa
        $events = $events->when($request->filled('search'), function ($query) use ($request) {
            $query->where('event_name', 'like', "%{$request->search}%");
        });

        // Paginação
        $events = $events->paginate(20)->withQueryString();

        // Cursos e categorias (para filtros)
        $courses = Course::all();
        $categories = Category::all();

        // ----------------------------------------------------------------------
        // ✅ CORREÇÃO: Resposta para requisições AJAX (Sem o Helper)
        // ----------------------------------------------------------------------
        if ($request->ajax()) {

            // 1. Renderiza o HTML dos cards de evento
            $eventsHtml = $events->map(function ($event) {
                // **IMPORTANTE**: O caminho do partial DEVE ser EXATO.
                return view('partials.events.event-card', ['event' => $event])->render();
            })->implode('');

            // 2. Lógica para NENHUM EVENTO ENCONTRADO (Injetando o HTML da mensagem)
            if ($events->isEmpty()) {
                // Este HTML é injetado diretamente no events-container
                $eventsHtml = '
                <div class="col-span-full flex flex-col items-center justify-center p-12">
                    <img src="' . asset('imgs/notFound.png') . '"
                        alt="Nenhum evento encontrado"
                        class="w-32 h-32 mb-4 text-gray-400">
                    <p class="text-xl font-semibold text-gray-500">Nenhum evento encontrado...</p>
                    <p class="text-sm text-gray-400 mt-2">Tente ajustar os filtros ou a pesquisa.</p>
                </div>
            ';
            }

            // 3. Renderiza o HTML dos links de paginação
            $paginationHtml = (string) $events->links();

            // 4. Retorna o JSON
            return response()->json([
                'eventsHtml' => $eventsHtml,
                'paginationHtml' => $paginationHtml,
            ]);
        }
        // ----------------------------------------------------------------------

        // Retorna view completa normalmente
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

        if ($event->course) {
            $followers = $event->course->followers;

            foreach ($followers as $user) {
                $user->notify(new NewEventNotification($event));
            }
        }

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
    // Atualiza evento
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Pega dados validados
        $validated = $request->validate([
            'event_name' => 'required|string|unique:events,event_name,' . $event->id,
            'event_description' => 'nullable|string',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date_format:Y-m-d\TH:i|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Confere se usuário tem permissão
        $coordinator = auth()->user()->coordinator;
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        // Guarda dados originais
        $original = $event->getOriginal();

        // Prepara dados para update
        $data = $request->only([
            'event_name',
            'event_description',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
            'visible_event'
        ]);

        // =====================
        // Imagem principal
        // =====================
        if ($request->input('remove_event_image') == '1') {
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }
            $data['event_image'] = null;
        } elseif ($request->hasFile('event_image')) {
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

        // =====================
        // Imagens extras
        // =====================
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

        // Salva coordenador e curso
        $data['coordinator_id'] = $coordinator->id;
        $data['course_id'] = optional($coordinator->coordinatedCourse)->id;

        // Atualiza evento
        $event->update($data);

        $changed = $event->getChanges();

        $importantChanges = array_intersect_key($changed, array_flip(['event_name', 'event_scheduled_at', 'event_location']));

        if (!empty($importantChanges)) {
            if ($event->course && $event->course->followers->isNotEmpty()) {
                Notification::send($event->course->followers, new EventUpdatedNotification($event, $importantChanges));
            }

            if ($event->notifiableUsers->isNotEmpty()) {
                Notification::send($event->notifiableUsers, new EventUpdatedNotification($event, $importantChanges));
            }
        }
        
        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        return redirect()->route('events.show', $event->id)
            ->with('success', 'Evento atualizado com sucesso!');
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
