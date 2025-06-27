@extends('layouts.layout') {{-- Usa o layout principal da aplicação --}}

@section('content')
<div class="container mt-4">
    <h1>Coordenadores</h1>

    <!-- Botão para criar novo coordenador -->
    <a href="{{ route('coordinators.create') }}" class="btn btn-primary mb-3">Novo Coordenador</a>

    <!-- Exibe mensagem de sucesso, se houver -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Verifica se há coordenadores cadastrados -->
    @if($coordinators->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Itera sobre os coordenadores -->
                @foreach($coordinators as $coordinator)
                <tr>
                    <!-- Exibe o nome e e-mail vinculados ao usuário -->
                    <td>{{ $coordinator->userAccount->name }}</td>
                    <td>{{ $coordinator->userAccount->email }}</td>

                    <!-- Converte o tipo salvo ('general' ou 'course') em texto amigável -->
                    <td>{{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}</td>

                    <!-- Ações: ver, editar e excluir -->
                    <td>
                        <!-- Ver detalhes -->
                        <a href="{{ route('coordinators.show', $coordinator->id) }}" class="btn btn-info btn-sm">Ver</a>

                        <!-- Editar coordenador -->
                        <a href="{{ route('coordinators.edit', $coordinator->id) }}" class="btn btn-warning btn-sm">Editar</a>

                        <!-- Formulário de exclusão -->
                        <form action="{{ route('coordinators.destroy', $coordinator->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Confirma exclusão?')">
                            @csrf <!-- Token CSRF obrigatório -->
                            @method('DELETE') <!-- Método HTTP DELETE -->
                            <button class="btn btn-danger btn-sm" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <!-- Mensagem caso não existam coordenadores -->
        <p>Nenhum coordenador cadastrado.</p>
    @endif
</div>
@endsection
