<!-- Navbar principal -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
  <div class="container">
    <!-- Logo e nome do site com link para a página inicial -->
    <a class="navbar-brand d-flex align-items-center" href="/">
      <img src="{{ asset('imgs/logo.png') }}" alt="Logo JBEventos" class="logo me-2">
      <strong>JBEeventos</strong>
    </a>

    <!-- Botão para colapsar a navbar em telas menores -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Itens da navbar que serão exibidos/ocultados no colapso -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <!-- Link para página de eventos -->
        <li class="nav-item"><a class="nav-link" href="{{route('events.index')}}">Eventos</a></li>

        <!-- Link para página de cursos -->
        <li class="nav-item"><a class="nav-link" href="{{route('courses.index')}}">Cursos</a></li>

        <!-- Link para página de coordenadores (restrição futura para admin) -->
        <li class="nav-item"><a class="nav-link" href="{{route('coordinators.index')}}">Coordenadores</a></li> {{-- Futuramente restringir ao admin --}}

        <!-- Link para criar evento (restrição futura para coordenadores) -->
        <li class="nav-item"><a class="nav-link" href="{{route('events.create')}}">Criar Evento</a></li> {{-- Futuramente restringir a coordenadores --}}

        <!-- Link para criar curso (restrição futura para admin) -->
        <li class="nav-item"><a class="nav-link" href="{{ route('courses.create') }}">Criar Curso</a></li>  {{-- Futuramente restringir ao admin --}}

        <!-- Link para criar coordenador (restrição futura para admin) -->
        <li class="nav-item"><a class="nav-link" href="{{ route('coordinators.create') }}">Criar Coordenador</a></li>  {{-- Futuramente restringir ao admin --}}
      </ul>
    </div>
  </div>
</nav>
