@extends('layouts.layout')

@section('content')

<div class="container">
    <h1>{{ $course->course_name }}</h1>

    @if($course->course_banner)
        <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner do Curso" style="max-width:100%; height:auto; margin-bottom:20px;">
    @endif

    <p>{{ $course->course_description }}</p>

    <p><strong>Coordenador:</strong> {{ $course->coordinator ? $course->coordinator->name : 'Nenhum coordenador definido' }}</p>

    @if($course->course_icon)
        <p><strong>Ícone do Curso:</strong><br>
        <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone do Curso" width="100"></p>
    @endif

    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Voltar para lista</a>
</div>

@endsection