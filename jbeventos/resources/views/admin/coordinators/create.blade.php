@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <!-- Título da página -->
    <h1>Criar Coordenador</h1>

    <!-- Exibe os erros de validação, se houver -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            <!-- Percorre todos os erros e exibe cada um em uma lista -->
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Formulário para criar um novo coordenador -->
    <form action="{{ route('coordinators.store') }}" method="POST">
        @csrf <!-- Proteção contra CSRF -->

        <!-- Campo de texto para o nome -->
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <!-- Mantém o valor antigo enviado em caso de erro -->
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <!-- Campo de email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>

        <!-- Campo para senha (provisória) -->
        <div class="mb-3">
            <label for="password" class="form-label">Senha Provisória</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <!-- Select para escolher o tipo do coordenador -->
        <div class="mb-3">
            <label for="coordinator_type" class="form-label">Tipo de Coordenador</label>
            <select name="coordinator_type" class="form-select" required>
                <option value="">Selecione...</option>
                <!-- Mantém a opção selecionada em caso de erro -->
                <option value="general" {{ old('coordinator_type') == 'general' ? 'selected' : '' }}>Geral</option>
                <option value="course" {{ old('coordinator_type') == 'course' ? 'selected' : '' }}>Curso</option>
            </select>
        </div>

        <!-- Botões para enviar ou cancelar o formulário -->
        <button type="submit" class="btn btn-success">Criar</button>
        <a href="{{ route('coordinators.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
