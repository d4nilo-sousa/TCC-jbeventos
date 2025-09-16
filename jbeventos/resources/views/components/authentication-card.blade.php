<div class="min-h-screen flex">
    <!-- Lado esquerdo: imagem com conteúdo promocional -->
    <div class="hidden md:flex w-1/2 relative">
        <!-- Imagem de fundo -->
        <img src="{{ asset('imgs/etecLogin2.png') }}" alt="Imagem de fundo"
            class="absolute inset-0 w-full h-full object-cover z-0" />

        <!-- Overlay escura para contraste -->
        <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>

        <!-- Conteúdo sobre a imagem -->
        <div class="relative z-20 flex flex-col justify-center items-center text-white px-10 text-center mx-auto">
            <h2 class="text-3xl font-bold mb-4">Novo por aqui?</h2>
            <p class="text-lg mb-6 max-w-sm">
                Crie uma conta agora mesmo e descubra tudo que o <strong>JB Eventos</strong> pode oferecer para você!
            </p>
            <a href="{{ route('register') }}"
                class="inline-block bg-white text-red-700 font-semibold px-6 py-2 rounded-full hover:bg-red-100 transition">
                Criar conta
            </a>
        </div>
    </div>

    <!-- Lado direito: formulário de login -->
    <div
        class="md:w-1/2 flex flex-col justify-center items-center bg-white text-white">

        <!-- Formulário -->
        <div class="w-full max-w-md">
            <div class="py-10 text-gray-800">
                <div class="mb-[20%]">
                    {{ $logo }}
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
