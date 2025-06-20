@extends('layouts.layout')

@section('content')

<div class="container">
    <h1>Criar Curso</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="course_name" class="form-label">Nome do Curso</label>
            <input type="text" name="course_name" id="course_name" class="form-control" value="{{ old('course_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="course_description" class="form-label">Descrição</label>
            <textarea name="course_description" id="course_description" class="form-control">{{ old('course_description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="coordinator_id" class="form-label">Coordenador (opcional)</label>
            <select name="coordinator_id" id="coordinator_id" class="form-select">
                <option value="">-- Nenhum --</option>
                @foreach($coordinators as $coordinator)
                    <option value="{{ $coordinator->id }}" {{ old('coordinator_id') == $coordinator->id ? 'selected' : '' }}>
                        {{ $coordinator->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="course_icon" class="form-label">Ícone do Curso (imagem)</label>
            <input type="file" name="course_icon" id="course_icon" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label for="course_banner" class="form-label">Banner do Curso (imagem)</label>
            <input type="file" name="course_banner" id="course_banner" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Curso</button>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@endsection
