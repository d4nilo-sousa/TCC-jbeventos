@extends('layouts.layout')

@section('content')

    <div class="container mt-5">
    <h2 class="mb-4">Cadastrar Novo Evento</h2>

    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Upload/Arrastar Imagens para o Carrossel --}}
        <div class="mb-4">
            <label for="images" class="form-label">Imagem de Capa (arraste ou selecione imagens)</label>
            <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
            <div id="carouselPreview" class="carousel slide mt-3 d-none" data-bs-ride="carousel">
                <div class="carousel-inner" id="carouselInner"></div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselPreview" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselPreview" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>

        {{-- Campos do Evento --}}
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Evento</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo do Evento</label>
            <input type="text" class="form-control" id="tipo" name="tipo" required>
        </div>

        <div class="mb-3">
            <label for="categoria" class="form-label">Categoria</label>
            <input type="text" class="form-control" id="categoria" name="categoria" required>
        </div>

        <div class="mb-3">
            <label for="curso" class="form-label">Curso Atribuído</label>
            <select class="form-select" id="curso" name="curso" required>
                <option selected disabled>Selecione um curso</option>
                {{-- Exemplo de opções --}}
                <option value="Informática">Informática</option>
                <option value="Administração">Administração</option>
                <option value="Desenvolvimento de Sistemas">Desenvolvimento de Sistemas</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" class="form-control" id="data" name="data" required>
        </div>

        <div class="mb-3">
            <label for="horario" class="form-label">Horário</label>
            <input type="time" class="form-control" id="horario" name="horario" required>
        </div>

        <div class="mb-3">
            <label for="local" class="form-label">Local</label>
            <input type="text" class="form-control" id="local" name="local" required>
        </div>

        {{-- Botões --}}
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">Publicar</button>
            <a href="{{ route('events.index') }}" class="btn btn-secondary">Descartar</a>
        </div>
    </form>
</div>

@endsection