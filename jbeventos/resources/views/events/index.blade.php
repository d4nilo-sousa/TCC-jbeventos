@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Meus Eventos</h2>

    {{-- Mensagens de sucesso --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Botão para novo evento --}}
    <div class="mb-4 text-end">
        <a href="{{ route('events.create') }}" class="btn btn-primary">+ Novo Evento</a>
    </div>

    <!-- Lista de eventos -->
    @if($events->count() > 0)
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($events as $event)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        {{-- Imagem do evento --}}
                        @if($event->event_image)
                            <img src="{{ asset('storage/' . $event->event_image) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Imagem do Evento">
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $event->event_name }}</h5>
                            <p class="card-text">{{ Str::limit($event->event_description, 100) }}</p>
                            <p class="text-muted small mb-1">
                                📍 {{ $event->event_location }}<br>
                                📅 {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-secondary small mb-2">
                                Coordenador: {{ $event->eventCoordinator->name ?? 'Não informado' }}<br>
                                Curso: {{ $event->eventCoordinator->coordinatedCourse->course_name ?? 'Evento Geral' }}
                            </p>

                            {{-- Botões --}}
                            <div class="mt-auto">
                                <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-primary btn-sm mb-2 w-100">Ver</a>
                                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-outline-warning btn-sm mb-2 w-100">Editar</a>

                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este evento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">Nenhum evento cadastrado até o momento.</p>
    @endif
</div>
@endsection
