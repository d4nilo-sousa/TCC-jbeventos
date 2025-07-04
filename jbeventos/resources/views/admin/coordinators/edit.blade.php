<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Coordenador
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('coordinators.update', $coordinator->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-medium">Nome</label>
                        <input type="text" class="w-full border-gray-300 rounded shadow-sm" value="{{ $coordinator->userAccount->name ?? '-' }}" disabled>
                    </div>

                    <div>
                        <label class="block font-medium">Email</label>
                        <input type="email" class="w-full border-gray-300 rounded shadow-sm" value="{{ $coordinator->userAccount->email ?? '-' }}" disabled>
                    </div>

                    <div>
                        <label for="coordinator_type" class="block font-medium">Tipo de Coordenador</label>
                        <select name="coordinator_type" class="w-full border-gray-300 rounded shadow-sm" required>
                            <option value="general" {{ $coordinator->coordinator_type == 'general' ? 'selected' : '' }}>Geral</option>
                            <option value="course" {{ $coordinator->coordinator_type == 'course' ? 'selected' : '' }}>Curso</option>
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar</button>
                        <a href="{{ route('coordinators.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>