@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h1>Editar Coordenador</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('coordinators.update', $coordinator->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nome</label>
            <input type="text" class="form-control" value="{{ $coordinator->userAccount->name ?? '-' }}" disabled>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" value="{{ $coordinator->userAccount->email ?? '-' }}" disabled>
        </div>

        <div class="mb-3">
            <label for="coordinator_type" class="form-label">Tipo de Coordenador</label>
            <select name="coordinator_type" class="form-select" required>
                <option value="general" {{ $coordinator->coordinator_type == 'general' ? 'selected' : '' }}>General</option>
                <option value="course" {{ $coordinator->coordinator_type == 'course' ? 'selected' : '' }}>Curso</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('coordinators.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
