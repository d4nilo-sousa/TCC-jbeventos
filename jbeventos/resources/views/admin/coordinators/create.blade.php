<x-app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-20 flex justify-center">
            <div class="w-full max-w-7xl bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto h-[90vh]">

                <div class="w-full flex justify-center grid place-items-center mb-5">
                    <p class="text-2xl sm:text-3xl text-stone-900 font-semibold">Criar Coordenadores</p>
                    <hr class="border-t border-gray-100 w-full mt-2">
                </div>

                <div class="m-10 mb-16">
                    <img src="{{ asset('imgs/coordinator.png') }}" class="w-36 mx-auto">
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

                <form action="{{ route('coordinators.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="flex flex-col sm:flex-row w-full px-3 gap-5">
                        <div class="flex flex-col flex-1">
                            <label for="name" class="block font-medium px-2">Nome do Coordenador</label>
                            <input type="text" name="name" value="{{ old('name') }}" autocomplete="off"
                                placeholder="Digite o nome do Coordenador"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded p-3"
                                required>
                        </div>
                        <div class="flex flex-col flex-1 mt-4 sm:mt-0">
                            <label for="email" class="block font-medium px-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" autocomplete="off"
                                placeholder="Digite o gmail do Coordenador"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded p-3"
                                required>
                        </div>
                    </div>

                    {{-- Campo de senha provisória --}}
                    <div class="mt-4 sm:mt-0 px-3">
                        <label for="generated_password" class="block font-medium px-2">Senha Provisória</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="generated_password" name="password" 
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 bg-gray-100 rounded p-3" 
                                readonly required>
                            <button type="button" onclick="generatePassword()" 
                                class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Gerar
                            </button>
                        </div>
                    </div>

                    {{-- Campo de tipo de coordenador --}}
                    <div class="mt-4 sm:mt-0 px-3">
                        <label for="coordinator_type" class="block font-medium px-2">Tipo de Coordenador</label>
                        <select name="coordinator_type" id="coordinator_type"
                            class="w-full border border-gray-300 rounded p-3 focus:bg-white focus:border-stone-600 focus:ring-stone-600 cursor-pointer"
                            required>
                            <option value="">Selecione...</option>
                            <option value="general" {{ old('coordinator_type') == 'general' ? 'selected' : '' }}>Geral</option>
                            <option value="course" {{ old('coordinator_type') == 'course' ? 'selected' : '' }}>Curso</option>
                        </select>
                    </div>

                    <div class="mt-6 w-full flex justify-end space-x-2">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Criar Coordenador
                        </button>
                        <a href="{{ route('coordinators.index') }}"
                            class="bg-stone-900 text-white px-4 py-2 rounded hover:bg-stone-700">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@vite('resources/js/password-generator.js')
