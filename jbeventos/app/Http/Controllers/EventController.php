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
        <div id="no-events-message" class="col-span-full flex flex-col items-center justify-center gap-6 text-center w-full my-4 p-6">
            <img src="' . asset('imgs/notFound.png') . '" class="w-auto h-40 object-contain" alt="not-found">
            <div>
                <p class="text-2xl font-bold text-stone-800">Ops! Nada foi encontrado...</p>
                <p class="text-gray-500 mt-2 text-md max-w-lg mx-auto">
                    Não encontramos nenhum evento com os termos de busca. Tente refazer a pesquisa!
                </p>
            </div>
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

    public function calendarEvents(Request $request)
    {
        // 1. Inicializa a query com os relacionamentos
        $query = Event::with(['eventCoordinator.userAccount', 'courses']);

        // 2. Aplica Filtros de Data do FullCalendar
        // FullCalendar envia 'start' e 'end' no formato ISO 8601 para a view atual.
        if ($request->filled('start') && $request->filled('end')) {
            // Usa event_scheduled_at para o início do evento
            $query->whereBetween('event_scheduled_at', [
                $request->input('start'), 
                $request->input('end')
            ]);
        } else {
            // Se a data não for fornecida (apenas por segurança), usa a lógica antiga.
            // No entanto, é altamente recomendado que o FullCalendar sempre envie 'start'/'end'.
            $query->whereDate('event_scheduled_at', '>=', now()->subMonths(3));
        }
        
        // 3. Aplica Filtros Personalizados do Formulário (enviados via JS extraParams)

        // Filtro de Busca por Título
        if ($request->filled('search')) {
            $query->where('event_name', 'like', '%' . $request->input('search') . '%');
        }

        // Filtro por Tipo de Evento (event_type)
        if ($request->filled('event_type')) {
            $eventType = $request->input('event_type');
            if ($eventType === 'general' || $eventType === 'course') {
                $query->where('event_type', $eventType);
            }
        }
        
        // Filtro por Cursos (course_id) - Assumindo que course_id pode ser um array de IDs
        if ($request->filled('course_id')) {
            $courseIds = $request->input('course_id');
            // Garante que a query só inclua eventos que estejam relacionados a um dos IDs de curso
            $query->whereHas('courses', function ($q) use ($courseIds) {
                // Verifica se é um array (múltiplas checkboxes) ou uma string (seleção única)
                $ids = is_array($courseIds) ? $courseIds : [$courseIds];
                $q->whereIn('courses.id', $ids);
            });
        }

        // Filtro por Categoria (category_id)
        if ($request->filled('category_id')) {
            $categoryIds = $request->input('category_id');
            $ids = is_array($categoryIds) ? $categoryIds : [$categoryIds];
            $query->whereIn('category_id', $ids);
        }
        
        // Executa a consulta
        $events = $query->get();

        // 4. Formata os eventos para o FullCalendar
        $formattedEvents = $events->map(function ($event) {

            $color = match ($event->event_type) {
                'course' => '#009688', // Teal
                'general' => '#E91E63', // Rosa (Para eventos gerais)
                default => '#2196F3', // Azul padrão
            };

            $coordinatorName = optional($event->eventCoordinator->userAccount)->name ?? 'N/A';
            $courseNames = $event->courses->pluck('course_name')->implode(', ');

            // 1. DATA DE TÉRMINO (END)
            $end = null; 

            // Adicionando 1 dia para que eventos 'allDay' apareçam corretamente no FullCalendar
            if ($event->event_expired_at && $event->event_scheduled_at->format('Y-m-d') === $event->event_expired_at->format('Y-m-d')) {
                // Se for um evento de um dia, mas com hora de término (não allDay)
                $end = $event->event_expired_at->format('Y-m-d H:i:s');
            } elseif ($event->event_expired_at) {
                // Se for evento de vários dias, adicione 1 dia inteiro para o FullCalendar.
                // Exemplo: 2025-06-01 a 2025-06-03 deve ter 'end' como 2025-06-04
                $end = $event->event_expired_at->copy()->addDay()->format('Y-m-d H:i:s');
            }
            
            // 2. ALL-DAY
            // Consideramos all-day se: 
            // a) O horário agendado é meia-noite E não tem hora de término OU
            // b) O horário agendado e de término são ambos meia-noite (múltiplos dias inteiros)
            $isAllDay = $event->event_scheduled_at->format('H:i:s') == '00:00:00' && (
                $event->event_expired_at === null || $event->event_expired_at->format('H:i:s') == '00:00:00'
            );

            return [
                'id' => $event->id,
                'title' => $event->event_name,
                // START: Sempre no formato ISO
                'start' => $event->event_scheduled_at->format('Y-m-d H:i:s'),

                // END: Data de término corrigida para FullCalendar
                'end' => $end,

                'allDay' => $isAllDay,
                'color' => $color,
                'extendedProps' => [
                    'location' => $event->event_location,
                    'type' => $event->event_type,
                    'coordinator' => $coordinatorName,
                    'courses' => $courseNames,
                    'url' => route('events.show', $event->id),
                ],
            ];
        });

        return response()->json($formattedEvents);
    }

    // Cria o evento
    public function store(Request $request)
    {
        // 1. VALIDAÇÃO: Adiciona a validação para o novo campo 'courses'
        $request->validate([
            'event_name' => 'required|unique:events,event_name',
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


        // Carrega os cursos e seus seguidores (evita N+1)
        $event->load('courses.followers');
        $notifiedUsers = []; // Evita duplicações

        foreach ($event->courses as $course) {
            foreach ($course->followers as $user) {
                if (!in_array($user->id, $notifiedUsers)) {

                    // 🔔 Envia notificação por e-mail e salva no banco
                    $user->notify(new \App\Notifications\NewEventNotification($event));

                    $notifiedUsers[] = $user->id;
                }
            }
        }

        // Retorno
        return redirect()
            ->route('events.index')
            ->with('success', 'Evento criado com sucesso! Usuários seguidores foram notificados.');
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
            'event_info' => 'nullable|string',
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
        // 3. ESTADO ORIGINAL
        // =====================
        $original = $event->replicate(); // Clona estado antes da atualização
        $originalCourseIds = $event->courses()->pluck('id')->sort()->values()->all();

        // =====================
        // 4. DADOS ATUALIZADOS
        // =====================
        $data = $request->only([
            'event_name',
            'event_info',
            'event_location',
            'event_scheduled_at',
            'event_expired_at',
        ]);

        $data['coordinator_id'] = $coordinator->id;

        // =====================
        // 5. IMAGEM PRINCIPAL
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

            // Centraliza corte
            $width = $image->width();
            $height = $image->height();
            $cropX = max(0, intval(($width - 1200) / 2));
            $cropY = max(0, intval(($height - 600) / 2));
            $image->crop(1200, 600, $cropX, $cropY);

            // Salva imagem processada
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
        // 6. IMAGENS EXTRAS
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
        // 7. ATUALIZA EVENTO
        // =====================
        $event->update($data);

        // =====================
        // 8. SINCRONIZA CURSOS
        // =====================
        $formCourseIds = $request->input('courses', []);
        $formCourseIds = array_map('intval', $formCourseIds);

        $coordinatorCourseId = optional($coordinator->coordinatedCourse)->id;
        if ($coordinatorCourseId) {
            $formCourseIds[] = $coordinatorCourseId;
        }

        $finalCourseIds = collect($formCourseIds)->unique()->values()->all();
        $event->courses()->sync($finalCourseIds);

        // =====================
        // 9. SINCRONIZA CATEGORIAS
        // =====================
        if ($request->has('categories')) {
            $event->eventCategories()->sync($request->input('categories'));
        } else {
            $event->eventCategories()->detach();
        }

        // =====================
        // 10. DETECTA MUDANÇAS
        // =====================
        $changedFields = [];
        $importantFields = ['event_name', 'event_scheduled_at', 'event_location', 'event_expired_at'];

        foreach ($importantFields as $field) {
            $originalValue = $original->$field;
            $currentValue = $event->$field;

            // Se for campo de data, compara como timestamp
            if (in_array($field, ['event_scheduled_at', 'event_expired_at'])) {
                $originalValue = $originalValue ? \Carbon\Carbon::parse($originalValue)->timestamp : null;
                $currentValue = $currentValue ? \Carbon\Carbon::parse($currentValue)->timestamp : null;
            }

            if ($originalValue != $currentValue) {
                $changedFields[$field] = [
                    'old' => $original->$field,
                    'new' => $event->$field,
                ];
            }
        }

        // =====================
        // 11. NOTIFICA USUÁRIOS (notify)
        // =====================

        // 1. Verifica mudanças nos campos importantes
        $newCourseIds = collect($finalCourseIds)->sort()->values()->all();
        $coursesChanged = $originalCourseIds !== $newCourseIds;

        // 2. Pega somente usuários que clicaram em "notificar"
        if (!empty($changedFields) || $coursesChanged) {
            $userIds = \App\Models\EventUserReaction::where('event_id', $event->id)
                ->where('reaction_type', 'notify')
                ->pluck('user_id')
                ->toArray();

            $users = \App\Models\User::whereIn('id', $userIds)->get();

            $oldCourses = \App\Models\Course::whereIn('id', $originalCourseIds)->get();
            $newCourses = \App\Models\Course::whereIn('id', $newCourseIds)->get();

            foreach ($users as $user) {
                $user->notify(new \App\Notifications\EventUpdatedNotification(
                    $event,
                    $changedFields,
                    $coursesChanged,
                    $oldCourses,
                    $newCourses
                ));
            }
        }

        // =====================
        // 12. RETORNO
        // =====================
        return redirect()
            ->route('events.show', $event->id)
            ->with('success', 'Evento atualizado com sucesso!');
    }



    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $coordinator = auth()->user()->coordinator;

        // Verifica permissão
        if (!$coordinator || $event->coordinator_id !== $coordinator->id) {
            abort(403, "Você não tem permissão para excluir este evento.");
        }

        // Pega usuários que clicaram em "notificar"
        $userIds = \App\Models\EventUserReaction::where('event_id', $event->id)
            ->where('reaction_type', 'notify')
            ->pluck('user_id')
            ->toArray();

        $users = \App\Models\User::whereIn('id', $userIds)->get();

        // Envia notificação de evento excluído
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\EventDeletedNotification($event));
        }

        // Exclui a imagem, se houver
        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        // Guarda o ID para o evento de broadcast
        $eventId = $event->id;

        // Deleta o evento do banco
        $event->delete();

        broadcast(new EventDeleted($eventId))->toOthers();

        return redirect()->route('events.index')->with('success', 'Evento excluído e usuários notificados com sucesso!');
    }
}
