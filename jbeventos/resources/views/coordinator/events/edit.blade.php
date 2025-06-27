@extends('layouts.layout')

@section('content')     
    <div class="container mt-5">
        <h1 class="mb-4">Editar Evento</h1>

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

        <!-- Passa o evento para o formulário -->
        @include('coordinator.events.form_events', ['event' => $event])
    </div>
@endsection
