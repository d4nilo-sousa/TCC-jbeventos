@extends('layouts.layout') {{-- Extende o layout principal da aplicação --}}

@section('content')

<div class="container">
    <h1>Criar Curso</h1>

    {{-- Exibe mensagens de erro, caso existam --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário para criação de curso --}}
    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf {{-- Proteção contra CSRF --}}

        {{-- Campo para nome do curso --}}
        <div class="mb-3">
            <label for="course_name" class="form-label">Nome do Curso</label>
            <input type="text" name="course_name" id="course_name" class="form-control" value="{{ old('course_name') }}" required>
        </div>

        {{-- Campo para descrição do curso --}}
        <div class="mb-3">
            <label for="course_description" class="form-label">Descrição</label>
            <textarea name="course_description" id="course_description" class="form-control">{{ old('course_description') }}</textarea>
        </div>

        {{-- Seleção de coordenador (opcional) --}}
        <div class="mb-3">
            <label for="coordinator_id" class="form-label">Coordenador (opcional)</label>
            <select name="coordinator_id" id="coordinator_id" class="form-select">
                <option value="">-- Nenhum --</option>
                @foreach($coordinators as $coordinator)
                    @if($coordinator->coordinator_type === 'course') 
                        <option value="{{ $coordinator->id }}" {{ old('coordinator_id') == $coordinator->id ? 'selected' : '' }}>
                            {{ $coordinator->userAccount->name ?? 'Sem nome' }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        {{-- Upload de ícone do curso --}}
        <div class="mb-3">
            <label for="course_icon" class="form-label">Ícone do Curso (imagem)</label>
            <input type="file" name="course_icon" id="course_icon" class="form-control" accept="image/*">
        </div>

        {{-- Upload de banner do curso --}}
        <div class="mb-3">
            <label for="course_banner" class="form-label">Banner do Curso (imagem)</label>
            <input type="file" name="course_banner" id="course_banner" class="form-control" accept="image/*">
        </div>

        {{-- Botões de ação --}}
        <button type="submit" class="btn btn-primary">Salvar Curso</button>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@endsection
