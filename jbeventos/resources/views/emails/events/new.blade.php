@component('mail::message')
# Olá, {{ $user->name }} 👋

Um novo evento foi adicionado ao curso que você segue:

**Curso:** {{ $event->course->course_name }}

@component('mail::panel')
@if($event->event_image)
<img src="{{ asset('storage/' . $event->event_image) }}" alt="{{ $event->event_name }}" style="width:100%; max-height:300px; object-fit:cover; margin-bottom:10px;">
@endif

<h3 style="font-size:18px; font-weight:bold; color:#111827; margin-bottom:4px; line-height:1.2; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
    {{ $event->event_name }}
</h3>

<p style="font-size:14px; color:#4B5563; margin-bottom:6px; line-height:1.4; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
    {{ $event->event_description }}
</p>

<div style="display:flex; flex-wrap:wrap; gap:4px; margin-bottom:6px; font-size:12px;">
    @forelse($event->eventCategories as $category)
        <span style="background-color:#E5E7EB; color:#374151; padding:2px 6px; border-radius:9999px; display:inline-block;">
            {{ $category->category_name }}
        </span>
    @empty
        <span style="background-color:#E5E7EB; color:#374151; padding:2px 6px; border-radius:9999px; display:inline-block;">
            Sem Categoria
        </span>
    @endforelse
</div>

<p style="display:flex; align-items:center; gap:4px; font-size:14px; color:#374151; margin-top:4px;">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor" style="height:16px; width:16px;">
        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 19.9l-4.95-5.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
    </svg>
    {{ $event->event_location }}
</p>

<p style="display:flex; align-items:center; gap:4px; font-size:14px; color:#374151; margin-top:2px;">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="height:16px; width:16px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h.01M3 15h18M3 21h18a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
    {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D MMMM YYYY, HH:mm') }}
</p>

@component('mail::button', ['url' => route('events.show', $event->id)])
Ver detalhes do evento
@endcomponent
@endcomponent

Fique ligado para não perder!  
{{ config('app.name') }}
@endcomponent
