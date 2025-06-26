@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        {{-- Imagem de capa --}}
        @if($event->event_image)
            <img src="{{ asset('storage/' . $event->event_image) }}" class="card-img-top" style="max-height: 400px; object-fit: cover;" alt="Imagem do Evento">
        @endif

        <div class="card-body">
            <h2 class="card-title">{{ $event->event_name }}</h2>
            <p class="card-text">{{ $event->event_description }}</p>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>📍 Local:</strong> {{ $event->event_location }}
                </div>
                <div class="col-md-6">
                    <strong>📅 Início:</strong> {{ \Carbon\Carbon::parse($event->event_scheduled_at)->format('d/m/Y H:i') }}
                    @if($event->event_expired_at)
                        <br><strong>⏱ Fim:</strong> {{ \Carbon\Carbon::parse($event->event_expired_at)->format('d/m/Y H:i') }}
                    @endif
                </div>
            </div>

           <div class="mb-3">
    {{-- Acessa o coordenador através do relacionamento eventCoordinator --}}
    <strong>👤 Coordenador:</strong> {{ $event->eventCoordinator->userAccount->name ?? 'Coordenador Não Atribuído' }}<br>
    {{-- Acessa o curso através do relacionamento coordinatedCourse do coordenador --}}
    <strong>🎓 Curso:</strong> {{ $event->eventCoordinator->coordinatedCourse->course_name ?? 'Evento Geral' }}
</div>
            {{-- Botões de ação --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('events.index') }}" class="btn btn-secondary">← Voltar</a>
                <div class="d-flex gap-2">
                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-warning">Editar</a>

                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este evento?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
