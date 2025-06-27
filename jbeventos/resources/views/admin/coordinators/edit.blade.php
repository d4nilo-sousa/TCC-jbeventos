@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <!-- Título da página -->
    <h1>Editar Coordenador</h1>

    <!-- Exibição de erros de validação, se houver -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Formulário para atualizar o coordenador -->
    <form action="{{ route('coordinators.update', $coordinator->id) }}" method="POST">
        @csrf <!-- Proteção contra CSRF -->
        @method('PUT') <!-- Spoof do método PUT, já que forms só suportam GET/POST -->

        <!-- Campo Nome (apenas leitura) -->
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" class="form-control" value="{{ $coordinator->userAccount->name ?? '-' }}" disabled>
        </div>

        <!-- Campo Email (apenas leitura) -->
        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" value="{{ $coordinator->userAccount->email ?? '-' }}" disabled>
        </div>

        <!-- Campo para alterar o tipo do coordenador -->
        <div class="mb-3">
            <label for="coordinator_type" class="form-label">Tipo de Coordenador</label>
            <select name="coordinator_type" class="form-select" required>
                <!-- Marca como selecionado se o valor atual for 'geral' ou 'course' -->
                <option value="general" {{ $coordinator->coordinator_type == 'geral' ? 'selected' : '' }}>Geral</option>
                <option value="course" {{ $coordinator->coordinator_type == 'course' ? 'selected' : '' }}>Curso</option>
            </select>
        </div>

        <!-- Botões de ação -->
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('coordinators.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
