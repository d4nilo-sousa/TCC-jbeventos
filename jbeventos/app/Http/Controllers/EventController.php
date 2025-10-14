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
        $events = Event::with(['courses', 'eventCoordinator.userAccount', 'eventCoordinator.coordinatedCourse', 'eventCategories']);

        // Filtros por tipo
        if ($request->filled('event_type')) {
            $events = $events->where('event_type', $request->event_type);
        }

        // 🔹 Filtro de cursos via relacionamento
        if ($request->filled('course_id')) {
            $courseIds = is_array($request->course_id) ? $request->course_id : [$request->course_id];
            $events = $events->whereHas('courses', fn($q) => $q->whereIn('courses.id', $courseIds));
        }

        // Filtro de categorias via relacionamento
        if ($request->filled('category_id')) {
            $categoryIds = is_array($request->category_id) ? $request->category_id : [$request->category_id];
            $events = $events->whereHas('eventCategories', fn($q) => $q->whereIn('categories.id', $categoryIds));
        }

        // Filtro por datas
        if ($request->filled('start_date')) {
            $events = $events->whereDate('event_scheduled_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $events = $events->whereDate('event_scheduled_at', '<=', $request->end_date);
        }

        // Ordenação por curtidas
        if ($request->filled('likes_order')) {
            $events = $events->withCount(['reactions as likes_count' => fn($q) => $q->where('reaction_type', 'like')]);
            $events = $request->likes_order === 'most'
                ? $events->orderBy('likes_count', 'desc')
                : $events->orderBy('likes_count', 'asc');
        }

        // Ordenação por agendamento
        if ($request->filled('schedule_order')) {
            $events = $request->schedule_order === 'soonest'
                ? $events->orderBy('event_scheduled_at', 'asc')
                : $events->orderBy('event_scheduled_at', 'desc');
        } else {
            $events = $events->orderBy('created_at', 'desc');
        }

        // Pesquisa por nome
        if ($request->filled('search')) {
            $events = $events->where('event_name', 'like', "%{$request->search}%");
        }

        // Paginação
        $events = $events->paginate(20)->withQueryString();

        // Para filtros
        $courses = Course::all();
        $categories = Category::all();

        // Retorno AJAX
        if ($request->ajax()) {
            $eventsHtml = $events->map(fn($event) => view('partials.events.event-card', ['event' => $event])->render())->implode('');

            if ($events->isEmpty()) {
                $eventsHtml = '
                <div class="col-span-full flex flex-col items-center justify-center p-12">
                    <img src="' . asset('imgs/notFound.png') . '" alt="Nenhum evento encontrado" class="w-32 h-32 mb-4 text-gray-400">
                    <p class="text-xl font-semibold text-gray-500">Nenhum evento encontrado...</p>
                    <p class="text-sm text-gray-400 mt-2">Tente ajustar os filtros ou a pesquisa.</p>
                </div>
            ';
            }

            $paginationHtml = (string) $events->links();

            return response()->json([
                'eventsHtml' => $eventsHtml,
                'paginationHtml' => $paginationHtml,
            ]);
        }

        return view('events.index', compact('events', 'courses', 'categories', 'loggedCoordinator'));
    }

    // Formulário de criação
    public function create()
    {
        // Carrega todas as categorias para o formulário
        $categories = Category::all();

        // Carrega todos os cursos disponíveis para o campo de seleção múltipla
        $allCourses = Course::orderBy('course_name')->get();

        $minExpiredAt = Carbon::now()->format('Y-m-d\TH:i');
        $eventExpiredAt = '';

        return view('coordinator.events.create', compact('categories', 'minExpiredAt', 'eventExpiredAt', 'allCourses'));
    }

    // Cria o evento
    public function store(Request $request)
    {
        // 1. VALIDAÇÃO: Adiciona a validação para o novo campo 'courses'
        $request->validate([
            'event_name' => 'required|unique:events,event_name',
            'event_description' => 'required|string',
            'event_info' => 'nullable|string|max:2000',
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date_format:Y-m-d\TH:i|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',

            // NOVO: Permite que sejam enviados múltiplos IDs de cursos
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $coordinator = auth()->user()->coordinator;
        if (!$coordinator) abort(403, 'Usuário não está vinculado a um coordenador.');

        $data = $request->only([
            'event_name',
            'event_description',
            'event_info',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
        ]);

        // Lógica de upload da imagem principal
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

            $originalName = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
            $slug = Str::slug($originalName);
            $uniqueId = uniqid();
            $extension = strtolower($upload->getClientOriginalExtension());
            $imageName = "{$uniqueId}_{$slug}.{$extension}";

            $path = storage_path('app/public/event_images/' . $imageName);

            if (in_array($extension, ['jpg', 'jpeg'])) {
                $image->save($path, 90);
            } elseif ($extension === 'png') {
                $image->save($path, 9);
            } else {
                $image->save($path);
            }

            $data['event_image'] = 'event_images/' . $imageName;
        }

        $data['coordinator_id'] = $coordinator->id;
        $data['event_type'] = $coordinator->coordinator_type;

        // REMOVIDO: A atribuição a 'course_id' foi removida, pois a coluna não existe mais.
        // $data['course_id'] = optional($coordinator->coordinatedCourse)->id;

        // Cria o evento no banco de dados
        $event = Event::create($data);

        // 2. ASSOCIAÇÃO DO(S) CURSO(S)

        // Pega o ID do curso padrão do coordenador
        $defaultCourseId = optional($coordinator->coordinatedCourse)->id;

        // Pega os IDs de cursos adicionais da requisição (se houver)
        $extraCourseIds = $request->input('courses', []);

        // Combina o curso padrão com os cursos extras
        $allCourseIds = collect($extraCourseIds);
        if ($defaultCourseId) {
            $allCourseIds->push($defaultCourseId);
        }

        // Filtra nulos e garante IDs únicos (para usar no sync)
        $uniqueCourseIds = $allCourseIds->filter()->unique()->toArray();

        // Sincroniza (adiciona) todos os IDs de cursos à tabela pivô 'course_event'
        if (!empty($uniqueCourseIds)) {
            // Usa a relação 'courses()' definida no modelo Event (belongsToMany)
            $event->courses()->sync($uniqueCourseIds);
        }

        // Lógica de upload de imagens adicionais
        if ($request->hasFile('event_images')) {
            foreach ($request->file('event_images') as $upload) {
                // ... (Lógica de processamento e salvamento das imagens adicionais) ...
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

                $originalName = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
                $slug = Str::slug($originalName);
                $uniqueId = uniqid();
                $extension = strtolower($upload->getClientOriginalExtension());
                $imageName = "{$uniqueId}_{$slug}.{$extension}";

                $path = storage_path('app/public/event_images/' . $imageName);

                if (in_array($extension, ['jpg', 'jpeg'])) {
                    $image->save($path, 90);
                } elseif ($extension === 'png') {
                    $image->save($path, 9);
                } else {
                    $image->save($path);
                }

                $event->images()->create([
                    'image_path' => 'event_images/' . $imageName
                ]);
            }
        }

        // Lógica de categorias
        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        // 3. LÓGICA DE NOTIFICAÇÃO ADAPTADA PARA MÚLTIPLOS CURSOS

        // Carrega os cursos e seus seguidores para evitar consultas N+1
        $event->load('courses.followers');
        $notifiedUsers = []; // Array para rastrear usuários já notificados

        foreach ($event->courses as $course) {
            foreach ($course->followers as $user) {
                // Verifica se o usuário já foi notificado (evita notificação duplicada)
                if (!in_array($user->id, $notifiedUsers)) {
                    $user->notify(new NewEventNotification($event));
                    $notifiedUsers[] = $user->id;
                }
            }
        }

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    // Detalhes do evento
    public function show(Event $event)
    {
        $user = auth()->user();
        $event->load(['eventCoordinator.userAccount', 'eventCategories']);

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

        // Carrega todos os cursos disponíveis para o campo de seleção múltipla
        $allCourses = Course::orderBy('course_name')->get();

        if (!$authCoordinator || $authCoordinator->id !== $event->coordinator_id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        $categories = Category::all();
        return view('coordinator.events.edit', compact('event', 'categories', 'minExpiredAt', 'eventExpiredAt', 'allCourses'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // =====================
        // 1. VALIDAÇÃO
        // =====================
        $validated = $request->validate([
            'event_name' => 'required|string|unique:events,event_name,' . $event->id,
            'event_description' => 'nullable|string',
            'event_info' => 'nullable|string', // <-- ADICIONADO
            'event_location' => 'required|string',
            'event_scheduled_at' => 'required|date',
            'event_expired_at' => 'nullable|date_format:Y-m-d\TH:i|after:event_scheduled_at',
            'event_image' => 'nullable|image|max:2048',
            'event_images.*' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        // =====================
        // 2. AUTORIZAÇÃO
        // =====================
        $coordinator = auth()->user()->coordinator;
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Usuário não está vinculado a um coordenador");
        }

        // =====================
        // 3. PREPARAÇÃO DOS DADOS
        // =====================
        $original = $event->getOriginal();

        $data = $request->only([
            'event_name',
            'event_description',
            'event_info', // <-- ADICIONADO
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
        ]);

        $data['coordinator_id'] = $coordinator->id;

        // =====================
        // 4. IMAGEM PRINCIPAL
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

            $originalName = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
            $slug = Str::slug($originalName);
            $uniqueId = uniqid();
            $extension = strtolower($upload->getClientOriginalExtension());
            $imageName = "{$uniqueId}_{$slug}.{$extension}";

            $path = storage_path('app/public/event_images/' . $imageName);

            $quality = ($extension === 'png') ? 9 : 90;
            $image->save($path, $quality);

            $data['event_image'] = 'event_images/' . $imageName;
        }

        // =====================
        // 5. IMAGENS EXTRAS
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

                $originalName = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
                $slug = Str::slug($originalName);
                $uniqueId = uniqid();
                $extension = strtolower($upload->getClientOriginalExtension());
                $imageName = "{$uniqueId}_{$slug}.{$extension}";

                $path = storage_path('app/public/event_images/' . $imageName);

                $quality = ($extension === 'png') ? 9 : 90;
                $image->save($path, $quality);

                $event->images()->create([
                    'image_path' => 'event_images/' . $imageName
                ]);
            }
        }

        // =====================
        // 6. ATUALIZA EVENTO
        // =====================
        $event->update($data);

        // ===================================
        // 7. SINCRONIZA CURSOS (PIVOT M:N)
        // ===================================
        $formCourseIds = $request->input('courses') ? array_map('intval', $request->input('courses')) : [];
        $coordinatorCourseId = optional($coordinator->coordinatedCourse)->id;

        $finalCourseIds = collect($formCourseIds);
        if ($coordinatorCourseId) {
            $finalCourseIds = $finalCourseIds->push($coordinatorCourseId)->unique()->values()->all();
        } else {
            $finalCourseIds = $finalCourseIds->unique()->values()->all();
        }

        $event->courses()->sync($finalCourseIds);

        // =====================
        // 8. SINCRONIZA CATEGORIAS (PIVOT)
        // =====================
        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        // =====================
        // 9. NOTIFICAÇÃO
        // =====================
        $changed = $event->getChanges();
        $importantChanges = array_intersect_key($changed, array_flip(['event_name', 'event_scheduled_at', 'event_location']));

        if (!empty($importantChanges)) {
            $allFollowers = collect();

            foreach ($event->courses as $course) {
                if ($course->followers->isNotEmpty()) {
                    $allFollowers = $allFollowers->merge($course->followers);
                }
            }

            if ($allFollowers->isNotEmpty()) {
                $uniqueFollowers = $allFollowers->unique('id');
                Notification::send($uniqueFollowers, new EventUpdatedNotification($event, $importantChanges));
            }

            if ($event->notifiableUsers->isNotEmpty()) {
                Notification::send($event->notifiableUsers, new EventUpdatedNotification($event, $importantChanges));
            }
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
