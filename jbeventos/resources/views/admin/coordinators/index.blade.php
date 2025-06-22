@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h1>Coordenadores</h1>

    <a href="{{ route('coordinators.create') }}" class="btn btn-primary mb-3">Novo Coordenador</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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
                @foreach($coordinators as $coordinator)
                <tr>
                    <td>{{ $coordinator->userAccount->name ?? 'Sem usuário' }}</td>
                    <td>{{ $coordinator->userAccount->email ?? '-' }}</td>
                    <td>{{ ucfirst($coordinator->coordinator_type) }}</td>
                    <td>
                        <a href="{{ route('coordinators.show', $coordinator->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('coordinators.edit', $coordinator->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('coordinators.destroy', $coordinator->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Confirma exclusão?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Nenhum coordenador cadastrado.</p>
    @endif
</div>
@endsection
