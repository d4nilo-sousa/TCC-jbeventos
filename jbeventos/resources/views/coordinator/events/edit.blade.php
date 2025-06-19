@extends('layouts.layout')

@section('content')     
    <div class="container mt-5">
        <h2 class="mb-4">Editar Evento</h2>

        <!-- Verifica se houveram erros de validação -->
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

        <!-- Passa o evento para o formulário -->
        @include('coordinator.events.form_events', ['event' => $event])


@endsection