@extends('layouts.layout')

@section('content')

<div class="container mt-5">
    <h2 class="mb-4"> Cadastrar Evento</h2>

    <!-- Verifica se houveram erros de validação -->
    <!-- $erros é uma variável que deve ser passada do controlador para a view -->
    <!-- Se não for passado, assume-se que não há erros -->
    @if($erros->any())
        <div class="alert alert-danger">
            <strong>Erros!</strong> Corrija os seguintes erros antes de continuar:
            <ul>
                @foreach($erros->all() as $erro)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Passa o evento se existir, caso contrário, passa null para criar um novo evento -->
    @include('coordinator.events.form_events', ['event' => $event ?? null]) 

</div>

@endsection