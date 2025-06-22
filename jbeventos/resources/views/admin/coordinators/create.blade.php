@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h1>Criar Coordenador</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('coordinators.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Senha</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="coordinator_type" class="form-label">Tipo de Coordenador</label>
            <select name="coordinator_type" class="form-select" required>
                <option value="">Selecione...</option>
                <option value="general" {{ old('coordinator_type') == 'general' ? 'selected' : '' }}>Geral</option>
                <option value="course" {{ old('coordinator_type') == 'course' ? 'selected' : '' }}>Curso</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Criar</button>
        <a href="{{ route('coordinators.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
