<x-guest-layout>
    @push('head')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
        
        <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @endpush

    <style>
        /* Escopo do CSS para não afetar outros locais do sistema */
        .landing-wrapper {
            --primary-color: #990000;
            --primary-hover: #7a0000;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --bg-color: #f9fafb;
            --glass-bg: rgba(255, 255, 255, 0.85);
            
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header Customizado desta Landing Page */
        .landing-header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 50;
            background: transparent;
        }

        .logo-img {
            height: 45px;
        }

        /* Botão de Login */
        .btn-nav-login {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background-color: white;
            color: var(--primary-color);
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .btn-nav-login:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Layout Hero */
        .hero-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 120px 5% 50px;
            max-width: 1400px;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        /* Textos */
        .hero-text {
            flex: 1;
            max-width: 600px;
        }

        .news-badge {
            display: inline-block;
            background-color: #fff1f1;
            color: var(--primary-color);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-size: 3.5rem;
            line-height: 1.15;
            color: #111;
            margin-bottom: 24px;
        }

        .highlight-red {
            color: var(--primary-color);
        }

        .hero-desc {
            font-size: 1.125rem;
            color: var(--text-gray);
            line-height: 1.7;
            margin-bottom: 40px;
        }

        /* Botão Principal */
        .btn-hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 36px;
            background: linear-gradient(135deg, var(--primary-color), #b30000);
            color: white;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(153, 0, 0, 0.25);
        }

        .btn-hero-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(153, 0, 0, 0.35);
            filter: brightness(1.1);
        }

        /* Imagem e Decoração */
        .hero-visual {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            position: relative;
        }

        .img-frame {
            position: relative;
            width: 100%;
            max-width: 500px;
            border-radius: 24px;
            overflow: hidden;
            transform: rotate(2deg);
            border: 8px solid white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }

        .img-frame img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.6s ease;
        }

        .img-frame:hover img {
            transform: scale(1.05);
        }

        /* Cards flutuantes */
        .floating-card {
            position: absolute;
            background: white;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 20;
            animation: floatCard 4s ease-in-out infinite;
        }

        .fc-icon {
            width: 48px; /* Aumentei um pouquinho */
            height: 48px;
            background-color: #fff0f0;
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px; /* Tamanho do ícone aumentado */
            flex-shrink: 0; /* Garante que a bolinha não amasse */
        }

        .fc-top-left { top: 10%; left: 0; animation-delay: 0s; }
        .fc-bottom-right { bottom: 5%; right: 20px; animation-delay: 2s; }

        .fc-content strong { display: block; font-size: 0.95rem; color: #1f2937; }
        .fc-content span { font-size: 0.8rem; color: #6b7280; }

        /* Background Blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            z-index: 0;
            filter: blur(80px);
        }
        .blob-1 { top: -100px; right: -100px; width: 500px; height: 500px; background: rgba(153,0,0,0.08); }
        .blob-2 { bottom: -100px; left: -100px; width: 600px; height: 600px; background: rgba(153,0,0,0.05); }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        /* Mobile */
        @media (max-width: 1024px) {
            .hero-section { flex-direction: column-reverse; text-align: center; padding-top: 100px; justify-content: center; }
            .hero-text { margin-top: 50px; }
            .hero-title { font-size: 2.5rem; }
            .img-frame { transform: rotate(0deg); max-width: 100%; margin: 0 auto; }
            .floating-card { display: none; }
            .landing-header { justify-content: center; }
            .logo-img { margin-right: auto; }
            .landing-header { padding: 15px; }
        }
    </style>

    <div class="landing-wrapper">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <header class="landing-header">
            <div class="logo-area">
                <img src="/imgs/logoJB.png" alt="JB Eventos Logo" class="logo-img">
            </div>
            
            @if (Route::has('login'))
                <nav>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-nav-login">
                            <i class="ph-bold ph-squares-four"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav-login">
                            <i class="ph-bold ph-sign-in"></i> Login
                        </a>
                    @endauth
                </nav>
            @endif
        </header>

        <main class="hero-section">
            
            <div class="hero-text">
                <span class="news-badge">Novidades 2025</span>
                <h1 class="hero-title">Conecte-se aos melhores eventos da <span class="highlight-red">Escola.</span></h1>
                <p class="hero-desc">
                    Descubra palestras, workshops e atividades extracurriculares. O ecossistema JB Eventos conecta você a experiências únicas de aprendizado.
                </p>
                
                <div class="cta-area">
                    <a href="{{ route('login') }}" class="btn-hero-cta">
                        Acessar Plataforma
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="img-frame">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Alunos estudando">
                </div>

                <div class="floating-card fc-top-left">
                    <div class="fc-icon">
                        <i class="ph-fill ph-code"></i>
                    </div>
                    <div class="fc-content">
                        <strong>Semana Dev</strong>
                        <span>13 de Setembro</span>
                    </div>
                </div>

                <div class="floating-card fc-bottom-right">
                    <div class="fc-icon">
                        <i class="ph-fill ph-users-three"></i>
                    </div>
                    <div class="fc-content">
                        <strong>Networking</strong>
                        <span>Conecte-se agora</span>
                    </div>
                </div>
            </div>

        </main>
    </div>
</x-guest-layout>