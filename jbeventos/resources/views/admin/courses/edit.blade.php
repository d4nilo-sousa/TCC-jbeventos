@extends('layouts.layout') {{-- Usa o layout base da aplicação --}}

@section('content')

<div class="container">
    <h1>Editar Curso</h1>

    {{-- Exibe erros de validação, se houverem --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário para atualizar o curso --}}
    <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
        @csrf {{-- Token CSRF --}}
        @method('PUT') {{-- Método HTTP spoofing para PUT --}}

        {{-- Campo: Nome do curso --}}
        <div class="mb-3">
            <label for="course_name" class="form-label">Nome do Curso</label>
            <input type="text" name="course_name" id="course_name" class="form-control" 
                   value="{{ old('course_name', $course->course_name) }}" required>
        </div>

        {{-- Campo: Descrição do curso --}}
        <div class="mb-3">
            <label for="course_description" class="form-label">Descrição</label>
            <textarea name="course_description" id="course_description" class="form-control">{{ old('course_description', $course->course_description) }}</textarea>
        </div>

        {{-- Campo: Coordenador do curso (opcional) --}}
        <div class="mb-3">
            <label for="coordinator_id" class="form-label">Coordenador (opcional)</label>
            <select name="coordinator_id" id="coordinator_id" class="form-select">
                <option value="">-- Nenhum --</option>
                @foreach($coordinators as $coordinator)
                    <option value="{{ $coordinator->id }}" 
                        {{ (old('coordinator_id', $course->coordinator_id) == $coordinator->id) ? 'selected' : '' }}>
                        {{ $coordinator->userAccount->name ?? 'Sem nome' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Exibe o ícone atual (caso exista) --}}
        <div class="mb-3">
            <label>Ícone Atual</label><br>
            @if($course->course_icon)
                <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone do curso" width="80">
            @else
                Nenhum ícone cadastrado.
            @endif
        </div>

        {{-- Campo: Novo ícone (upload de imagem) --}}
        <div class="mb-3">
            <label for="course_icon" class="form-label">Alterar Ícone do Curso</label>
            <input type="file" name="course_icon" id="course_icon" class="form-control" accept="image/*">
        </div>

        {{-- Exibe o banner atual (caso exista) --}}
        <div class="mb-3">
            <label>Banner Atual</label><br>
            @if($course->course_banner)
                <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner do curso" width="150">
            @else
                Nenhum banner cadastrado.
            @endif
        </div>

        {{-- Campo: Novo banner (upload de imagem) --}}
        <div class="mb-3">
            <label for="course_banner" class="form-label">Alterar Banner do Curso</label>
            <input type="file" name="course_banner" id="course_banner" class="form-control" accept="image/*">
        </div>

        {{-- Botões de ação --}}
        <button type="submit" class="btn btn-primary">Atualizar Curso</button>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@endsection
