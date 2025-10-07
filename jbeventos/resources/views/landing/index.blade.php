<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-md flex flex-col-reverse lg:flex-row items-center overflow-hidden">

                <!-- Textos -->
                <div class="w-full lg:w-1/2 p-8 lg:p-12 text-center lg:text-left">
                    <h1 class="text-3xl md:text-4xl font-semibold text-gray-800 font-ubuntu">
                        Bem-vindo ao <span class="text-red-600">JBeventos</span>!
                    </h1>
                    <p class="mt-4 text-gray-600 text-lg">
                        O jeito fácil de acompanhar tudo que acontece na <strong>Etec João Belarmino</strong>.
                    </p>
                    <div class="mt-6 flex justify-center lg:justify-start gap-4">
                        <a href="#eventos"
                            class="px-6 py-3 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition">
                            Ver eventos
                        </a>
                        <a href="#cursos"
                            class="px-6 py-3 border border-red-600 text-red-600 rounded-md hover:bg-red-50 transition">
                            Explorar cursos
                        </a>
                    </div>
                </div>

                <!-- Imagem -->
                <div class="w-full lg:w-1/2">
                    <img src="{{ asset('imgs/etecLogin2.png') }}" alt="Imagem Etec" class="w-full h-full object-cover">
                </div>

            </div>
        </div>
    </div>


</x-app-layout>
