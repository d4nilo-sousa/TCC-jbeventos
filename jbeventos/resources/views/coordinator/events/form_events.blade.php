@extends('layouts.layout')

@section('content')

<form action="{{ isset($event) ? route('events.update', $event->id) : route('events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf 
    {{-- Verifica se é uma atualização de evento --}}
    @if(isset($event))
        @method('PUT')
    @endif

    {{-- Upload de Imagem --}}
    <div class="mb-4">
        <label for="event_image" class="form-label">Imagem de Capa</label>
        <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">

        @if(isset($event) && $event->event_image) <!-- Verifica se o evento existe e possui uma imagem -->
            <div class="mt-3">
                <strong>Imagem atual:</strong>
                <img src="{{ asset('storage/' . $event->event_image) }}" alt="Imagem atual" class="img-fluid rounded mt-2" style="max-height: 300px;">
            </div>
        @endif
    </div>

    {{-- Nome --}}
    <div class="mb-3">
        <label for="event_name" class="form-label">Nome do Evento</label>
        <input type="text" name="event_name" class="form-control" id="event_name" value="{{ old('event_name', $event->event_name ?? '') }}" required>
    </div>

    {{-- Descrição --}}
    <div class="mb-3">
        <label for="event_description" class="form-label">Descrição</label>
        <textarea name="event_description" class="form-control" rows="4" required>{{ old('event_description', $event->event_description ?? '') }}</textarea> {{---old() para manter o antigo valor em caso de erro de validação ---}}
    </div>

    {{-- Local --}}
    <div class="mb-3">
        <label for="event_location" class="form-label">Local</label>
        <input type="text" name="event_location" class="form-control" value="{{ old('event_location', $event->event_location ?? '') }}" required>
    </div>

     {{-- Categorias do Evento --}}
    <div class="mb-3">
        <label class="form-label">Categorias do Evento</label>
        <div class="d-flex flex-wrap gap-3">
            @foreach($categories as $category)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}"
                        {{-- Lógica corrigida para verificar se a categoria deve ser marcada --}}
                        {{ in_array($category->id, old('categories', isset($event) ? $event->eventCategories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="category_{{ $category->id }}">
                        {{ $category->category_name }}
                    </label>
                </div>
            @endforeach
        </div>
        @error('categories')
            <div class="text-danger mt-2">{{ $message }}</div>
        @enderror
    </div>
  

    {{-- Data e Hora --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="event_scheduled_at" class="form-label">Data e Hora do Evento</label>
            {{-- Usando Carbon para formatar a data e hora corretamente --}}
            {{-- old() para manter o antigo valor em caso de erro de validação --}}
            <input type="datetime-local" name="event_scheduled_at" class="form-control" value="{{ old('event_scheduled_at', isset($event) ? \Carbon\Carbon::parse($event->event_scheduled_at)->format('Y-m-d\TH:i') : '') }}" required>
        </div>
        <div class="col-md-6">
            <label for="event_expired_at" class="form-label">Data/Hora de Encerramento (opcional)</label>
            <input type="datetime-local" name="event_expired_at" class="form-control" value="{{ old('event_expired_at', isset($event) && $event->event_expired_at ? \Carbon\Carbon::parse($event->event_expired_at)->format('Y-m-d\TH:i') : '') }}">
        </div>
    </div>

 {{-- Coordenador --}}
<div class="mb-3">
    <label for="coordinator_id" class="form-label">Coordenador Responsável</label>
    <select name="coordinator_id" class="form-select" required>
        <option value="">Selecione</option>
        @foreach($coordinators as $coordinator)
            <option value="{{ $coordinator->id }}"
                {{ old('coordinator_id', $event->coordinator_id ?? '') == $coordinator->id ? 'selected' : '' }}>
                {{-- Acessa o nome do coordenador através do relacionamento userAccount --}}
                {{ $coordinator->userAccount->name ?? 'Coordenador Sem Nome' }}
                {{-- Exibe o nome do curso associado, se existir --}}
                — {{ $coordinator->coordinatedCourse->course_name ?? 'Evento Geral' }}
            </option>
        @endforeach
    </select>
</div>

    {{-- Botões --}}
    <div class="d-flex justify-content-between mt-4">
        <button type="submit" class="btn btn-success">
            {{ isset($event) ? 'Atualizar Evento' : 'Criar Evento' }}
        </button>
        <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

@endsection