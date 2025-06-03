<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="{{ asset('imgs/logo.png') }}" alt="Logo JBEventos" class="logo me-2">
      <strong>JBEeventos</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="#">Eventos</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Cursos</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Criar Evento</a></li> {{-- Futuramente restringir a coordenadores --}}
        <li class="nav-item"><a class="nav-link" href="#">Criar Curso</a></li>  {{-- Futuramente restringir ao admin --}}
         <li class="nav-item"><a class="nav-link" href="#">Coordenadores</a></li> {{-- Futuramente restringir ao admin --}}
        <li class="nav-item"><a class="nav-link" href="#">Sobre</a></li>

      </ul>
    </div>
  </div>
</nav>

<!-- Header -->
<header class="header-bg">
  <div class="container">
    <h1 class="fw-bold">Bem-vindo ao JBEeventos!</h1>
    <p class="lead">Fique por dentro dos principais eventos da nossa escola</p>
  </div>
</header>
