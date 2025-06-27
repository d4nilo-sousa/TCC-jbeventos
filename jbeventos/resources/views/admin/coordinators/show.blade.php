@extends('layouts.layout') {{-- Usa o layout principal da aplicação --}}

@section('content')
<div class="container mt-4">
    <h1>Detalhes do Coordenador</h1>

    {{-- Lista de detalhes do coordenador --}}
    <dl class="row">
        {{-- Nome do coordenador --}}
        <dt class="col-sm-3">Nome</dt>
        <dd class="col-sm-9">{{ $coordinator->userAccount->name }}</dd>

        {{-- Email do coordenador --}}
        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9">{{ $coordinator->userAccount->email }}</dd>

        {{-- Tipo de coordenador (Geral ou Curso) --}}
        <dt class="col-sm-3">Tipo de Coordenador</dt>
        <dd class="col-sm-9">{{ ['general' => 'Geral', 'course' => 'Curso'][$coordinator->coordinator_type] }}</dd>

        {{-- Exibe o curso que o coordenador gerencia, se for do tipo 'course' --}}
        @if($coordinator->coordinator_type === 'course' && $coordinator->coordinatedCourse) 
            <dt class="col-sm-3">Curso que gerencia</dt>
            <dd class="col-sm-9">{{ $coordinator->coordinatedCourse->course_name }}</dd>
        @elseif($coordinator->coordinator_type === 'course')
            <dt class="col-sm-3">Curso que gerencia</dt>
            <dd class="col-sm-9">Nenhum curso gerenciado</dd>
        @endif

        {{-- Datas de criação e última atualização --}}
        <dt class="col-sm-3">Criado em</dt>
        <dd class="col-sm-9">{{ $coordinator->created_at->format('d/m/Y H:i') }}</dd>

        <dt class="col-sm-3">Atualizado em</dt>
        <dd class="col-sm-9">{{ $coordinator->updated_at->format('d/m/Y H:i') }}</dd>
    </dl>

    {{-- Botão para voltar à lista de coordenadores --}}
    <a href="{{ route('coordinators.index') }}" class="btn btn-secondary">Voltar</a>
</div>
@endsection
