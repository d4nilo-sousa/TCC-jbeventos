@extends('layouts.layout')

@section('content')

<div class="container">
    <h1>Cursos</h1>

    <!-- Botão para criar um novo curso -->
    <a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">Criar Novo Curso</a>

    <!-- Exibe mensagem de sucesso se existir na sessão -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Verifica se há cursos para listar -->
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
                <!-- Loop para listar todos os cursos -->
                @foreach($courses as $course)
                    <tr>
                        <td>
                            <!-- Verifica se o curso possui ícone, se sim exibe a imagem -->
                            @if($course->course_icon)
                                <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone" width="50">
                            @else
                                <!-- Caso não possua ícone, exibe traço -->
                                ---
                            @endif
                        </td>
                        <td>
                            <!-- Verifica se o curso possui banner, se sim exibe a imagem -->
                            @if($course->course_banner)
                                <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner" width="100">
                            @else
                                <!-- Caso não possua banner, exibe traço -->
                                ---
                            @endif
                        </td>
                        <td>
                            <!-- Link para a página de detalhes do curso -->
                            <a href="{{ route('courses.show', $course->id) }}">
                                {{ $course->course_name }}
                            </a>
                        </td>
                        <td>
                            <!-- Exibe o nome do coordenador do curso, se existir -->
                            {{ $course->courseCoordinator?->userAccount?->name ?? 'Nenhum coordenador definido' }}
                        </td>
                        <td>
                            <!-- Botão para editar o curso -->
                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-warning">Editar</a>

                            <!-- Botão para visualizar detalhes do curso -->
                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-info btn-sm">Ver</a>

                            <!-- Formulário para excluir o curso com confirmação -->
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
        <!-- Mensagem exibida caso não existam cursos -->
        <p>Nenhum curso cadastrado.</p>
    @endif
</div>

@endsection
