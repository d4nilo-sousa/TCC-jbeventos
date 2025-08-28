<x-app-layout>
    <div class="relative">
        {{-- Botão de Voltar (menor e sem fundo sólido) --}}
        <a href="{{ route('dashboard') }}" 
           class="absolute top-6 left-5 inline-flex items-center text-gray-600 hover:text-gray-900 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="h4 w-4 mr-1" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>

        {{-- Chat --}}
        @livewire('chat', ['otherUser' => $otherUser])
    </div>
</x-app-layout>
