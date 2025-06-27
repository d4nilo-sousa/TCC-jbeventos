@extends('layouts.layout')

@section('content')

<div class="container mt-5">
    <h1 class="mb-4">Cadastrar Evento</h1>

    <!-- Exibe mensagens de erro de validação automaticamente fornecidas pelo Laravel -->
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Erros!</strong> Corrija os seguintes erros antes de continuar:
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Inclui o formulário de evento, passando $event se existir -->
    @include('coordinator.events.form_events', ['event' => $event ?? null]) 

</div>

@endsection
