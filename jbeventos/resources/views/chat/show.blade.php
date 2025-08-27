<x-app-layout>
    <div class="relative">
        {{-- Botão de Voltar no canto superior esquerdo --}}
        <a href="{{ route('dashboard') }}" 
           class="absolute top-4 left-4 inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>

        {{-- Chat --}}
        @livewire('chat', ['otherUser' => $otherUser])
    </div>
</x-app-layout>
