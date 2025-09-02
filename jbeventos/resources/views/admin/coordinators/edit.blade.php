<x-app-layout>
    <div class="py-12">
        <div class="w-100% mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="w-[70rem] h-[46rem] bg-white shadow-md rounded-2xl p-9 mx-auto mt-2">
                <div class="w-100% flex justify-center grid place-items-center mb-5">
                    <p class="text-[2rem] text-stone-900">Editar Coordenador</p>
                    <hr class="border-t-1 border-gray-100">
                </div>
                <div class="m-10 mb-16">
                    <img src="{{ asset('imgs/edit.png') }}" class="w-20 mx-auto">
                </div>
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
                        <label class="block font-medium text-xl px-1">Nome</label>
                        <input type="text" class="w-full border-gray-300 rounded shadow-sm"
                            value="{{ $coordinator->userAccount->name ?? '-' }}" disabled>
                    </div>

                    <div>
                        <label class="block font-medium text-xl px-1 mt-10">Email</label>
                        <input type="email" class="w-full border-gray-300 rounded shadow-sm"
                            value="{{ $coordinator->userAccount->email ?? '-' }}" disabled>
                    </div>

                    <div>
                        <label for="coordinator_type" class="block font-medium text-xl px-1 mt-10">Tipo de Coordenador</label>
                        <select name="coordinator_type" class="w-full border-gray-300 rounded shadow-sm" required>
                            <option value="general" {{ $coordinator->coordinator_type == 'general' ? 'selected' : '' }}>
                                Geral</option>
                            <option value="course" {{ $coordinator->coordinator_type == 'course' ? 'selected' : '' }}>
                                Curso</option>
                        </select>
                    </div>
                    
                    <br><br>
                    <div class="mt-6 w-full flex justify-end space-x-2">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar</button>
                        <a href="{{ route('coordinators.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
