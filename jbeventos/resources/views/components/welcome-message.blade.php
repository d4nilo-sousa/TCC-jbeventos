@props(['name', 'role'])

<div class="flex justify-center">
    <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 p-4 mt-3 mb-6 w-full md:w-3/4 lg:w-2/3" role="alert">
        <div class="flex">
            <div class="py-1">
                <svg class="h-6 w-6 text-blue-400 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold">Olá, {{ $name }}!</p>
                <p class="text-sm">Bem-vindo(a) ao seu painel de controle. Aqui você pode acompanhar suas interações e atividades recentes dos coordenadores e eventos da nossa escola.</p>
            </div>
        </div>
    </div>
</div>