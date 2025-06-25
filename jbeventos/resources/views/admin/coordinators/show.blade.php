@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h1>Detalhes do Coordenador</h1>

    <dl class="row">
        <dt class="col-sm-3">Nome</dt>
        <dd class="col-sm-9">{{ $coordinator->userAccount->name}}</dd>

        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9">{{ $coordinator->userAccount->email}}</dd>

        <dt class="col-sm-3">Tipo de Coordenador</dt>
        <dd class="col-sm-9">{{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type]}}</dd>

        <dt class="col-sm-3">Criado em</dt>
        <dd class="col-sm-9">{{ $coordinator->created_at->format('d/m/Y H:i') }}</dd>

        <dt class="col-sm-3">Atualizado em</dt>
        <dd class="col-sm-9">{{ $coordinator->updated_at->format('d/m/Y H:i') }}</dd>
    </dl>

    <a href="{{ route('coordinators.index') }}" class="btn btn-secondary">Voltar</a>
</div>
@endsection