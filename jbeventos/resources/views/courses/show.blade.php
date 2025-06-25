@extends('layouts.layout')

@section('content')

<div class="container">
    <h1>{{ $course->course_name }}</h1>

    @if($course->course_icon)
        <p><strong>Ícone do Curso:</strong><br>
        <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone do Curso" width="100"></p>
    @endif

    @if($course->course_banner)
        <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner do Curso" style="max-width:100%; height:auto; margin-bottom:20px;">
    @endif

    <p>{{ $course->course_description }}</p>

    <p><strong>Coordenador:</strong> {{ $course->courseCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}</p>

    <div class="mt-4">
        <dl class="row">
            <dt class="col-sm-3">Criado em</dt>
            <dd class="col-sm-9">{{ $course->created_at->format('d/m/Y H:i') }}</dd>

            <dt class="col-sm-3">Atualizado em</dt>
            <dd class="col-sm-9">{{ $course->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>

        <a href="{{ route('courses.index') }}" class="btn btn-secondary">Voltar para lista</a>
    </div>
</div>

@endsection
