<div class="min-h-screen flex overflow-hidden">


    <!-- Lado esquerdo -->
    <div
        x-data="{ show: false }"
        x-init="setTimeout(() => show = true, 100)"
        x-show="show"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 -translate-x-10"
        x-transition:enter-end="opacity-100 translate-x-0"
        class="hidden md:flex w-1/2 relative">
        
        @if (Route::is('login'))
            <img src="{{ asset('imgs/etecLogin2.png') }}" alt="Imagem de fundo"
                class="absolute inset-0 w-full h-full object-cover z-0" />
        @elseif (Route::is('register'))
            <img src="{{ asset('imgs/etecCadastro.jpeg') }}" alt="Imagem de fundo"
                class="absolute inset-0 w-full h-full object-cover z-0" />
        @endif

        <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>

        <div
            class="relative z-20 flex flex-col justify-center items-center text-white px-10 text-center mx-auto max-w-[50%]">
            @if (Route::is('login'))
                <h2 class="text-3xl font-bold mb-3">Novo por aqui?</h2>
                <p class="w-full text-lg mb-6">
                    Crie uma conta agora mesmo e descubra tudo que o <strong>JB Eventos</strong> pode oferecer!
                </p>
                <a href="{{ route('register') }}"
                    class="inline-block bg-white text-red-700 font-semibold px-6 py-2 rounded-full hover:bg-red-100 transition">
                    Criar conta
                </a>
            @elseif (Route::is('register'))
                <h2 class="text-3xl font-bold mb-3">Já possui uma conta?</h2>
                <p class="max-w-md leading-relaxed text-lg mb-6">
                    Faça login agora mesmo e aproveite tudo que o <strong>JB Eventos</strong> pode oferecer!
                </p>
                <a href="{{ route('login') }}"
                    class="inline-block bg-white text-red-700 font-semibold px-6 py-2 rounded-full hover:bg-red-100 transition">
                    Fazer login
                </a>
            @endif
        </div>
    </div>

    <!-- Lado direito -->
    <div
        x-data="{ show: false }"
        x-init="setTimeout(() => show = true, 300)"
        x-show="show"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 translate-x-10"
        x-transition:enter-end="opacity-100 translate-x-0"
        class="md:w-1/2 flex flex-col justify-center items-center bg-white text-white">
        
        <div class="mb-[10%]">
            {{ $logo }}
        </div>

        <div class="w-full max-w-md">
            <div class="py-10 text-gray-800 shadow-xl rounded-xl border-2 border-gray-100 p-10">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
