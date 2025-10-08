<x-app-layout>
    <div class="py-12">
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 lg:p-12">
                
                <!-- Título e Ícone da Página -->
                <div class="flex flex-col items-center justify-center mb-8 text-center">
                    <div class="p-3 bg-indigo-50 rounded-full mb-4 shadow-sm flex items-center justify-center">
                        <img src="{{ asset('imgs/edit.png') }}" class="h-10 w-10 text-indigo-600">
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Editar Coordenador</h2>
                </div>

                <!-- Mensagens de Erro -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200 shadow-sm">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('coordinators.update', $coordinator->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                            <input type="text" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed" value="{{ $coordinator->userAccount->name ?? '-' }}" disabled>
                        </div>
    
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed" value="{{ $coordinator->userAccount->email ?? '-' }}" disabled>
                        </div>
                    </div>

                    <div>
                        <label for="coordinator_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Coordenador</label>
                        <select id="coordinator_type" name="coordinator_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="general" {{ $coordinator->coordinator_type == 'general' ? 'selected' : '' }}>Geral</option>
                            <option value="course" {{ $coordinator->coordinator_type == 'course' ? 'selected' : '' }}>Curso</option>
                        </select>
                    </div>

                    <h3 class="text-xl font-semibold text-gray-700 border-b pb-2"></h3>
                    
                    <div class="flex justify-end space-x-3 mt-8">
                        <a href="{{ route('coordinators.index') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-full shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Atualizar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
