<x-app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-20 flex justify-center">
            <div class="w-full max-w-7xl bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto mt-2">

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
                            <input type="text" name="name" value="{{ old('name') }}"
                                autocomplete="off"
                                placeholder="Digite o nome do Coordenador"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded p-3"
                                required>
                        </div>
                        <div class="flex flex-col flex-1 mt-4 sm:mt-0">
                            <label for="email" class="block font-medium px-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                autocomplete="off"
                                placeholder="Digite o gmail do Coordenador"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded p-3"
                                required>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row w-full px-3 gap-5">
                        <div class="flex flex-col flex-1">
                            <label for="password" class="block font-medium px-2">Senha</label>
                            <input type="password" name="password"
                                autocomplete="off"
                                placeholder="Digite a senha do Coordenador"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded p-3"
                                required>
                        </div>
                        <div class="flex flex-col flex-1 mt-4 sm:mt-0">
                            <label for="password_confirmation" class="block font-medium px-2">Confirmar Senha</label>
                            <input type="password" name="password_confirmation"
                                autocomplete="off"
                                placeholder="Confirme a senha do Coordenador"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 focus:bg-white rounded p-3"
                                required>
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-0 px-2">
                        <label for="coordinator_type" class="block font-medium mt-4 px-3">Tipo de Coordenador</label>
                        <select name="coordinator_type" id="coordinator_type"
                            class="w-full border border-gray-300 rounded focus:bg-white focus:border-stone-600 focus:ring-stone-600 cursor-pointer p-3"
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
                        <a href="{{ route('coordinators.index') }}" class="bg-stone-900 text-white px-4 py-2 rounded hover:bg-stone-700">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>