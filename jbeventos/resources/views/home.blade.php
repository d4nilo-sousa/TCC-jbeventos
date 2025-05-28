@extends('layout')

@section('content')
    <h1>Bem-vindo ao JBEventos!</h1>
    <ul>
        <li><a href="/">Início</a></li>
        <li><a href="/events">Eventos</a></li> <!-- Todos os usuários -->

        {{-- Futuramente restringir a coordenadores --}}
        <li><a href="/events/new">Criar Evento</a></li>

        <li><a href="/courses">Cursos</a></li> <!-- Todos os usuários -->

        {{-- Futuramente restringir a administradores --}}
        <li><a href="/courses/new">Criar Curso</a></li>

        <li><a href="/contact">Contatos</a></li> <!-- Coordenadores e Usuarios -->
        <li><a href="/about">Sobre</a></li> <!-- Todos os usuários -->
    </ul>
@endsection
