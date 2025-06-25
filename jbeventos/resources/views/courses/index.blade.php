@extends('layouts.layout')

@section('content')

<div class="container">
    <h1>Cursos</h1>

    <a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">Criar Novo Curso</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($courses->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ícone</th>
                    <th>Banner</th>
                    <th>Nome</th>
                    <th>Coordenador</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr>
                        <td>
                            @if($course->course_icon)
                                <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone" width="50">
                            @else
                                ---
                            @endif
                        </td>
                        <td>
                            @if($course->course_banner)
                                <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner" width="100">
                            @else
                                ---
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('courses.show', $course->id) }}">
                                {{ $course->course_name }}
                            </a>
                        </td>
                        <td>{{ $course->courseCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}</td>
                        <td>
                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-info btn-sm">Ver</a>

                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Nenhum curso cadastrado.</p>
    @endif
</div>

@endsection
