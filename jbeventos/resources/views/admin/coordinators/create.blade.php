<x-app-layout>
    <div class="py-12">
        <div class="w-100% mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="w-[70rem] h-[46rem] bg-white shadow-md rounded-2xl p-9 mx-auto mt-2">
                <div class="w-100% flex justify-center grid place-items-center mb-5">
                    <p class="text-[2rem] text-stone-900">Criar Coordenadores</p>
                    <hr class="border-t-1 border-gray-100">
                </div>
                <div class="m-10 mb-16">
                    <img src="{{ asset('imgs/coordinator.png') }}" class="w-32 mx-auto">
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

                    <div class="flex w-full gap-4 mt">
                        <div class="flex flex-col flex-1">
                            <label for="name" class="block font-medium">Nome do Coordenador</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 
                            focus:bg-white rounded p-2"
                                required>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label for="email" class="block font-medium">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 
                            focus:bg-white rounded p-2"
                                required>
                        </div>
                    </div>


                    <div class="flex w-full gap-4 mt">
                        <div class="flex flex-col flex-1">
                            <label for="password" class="block font-medium">Senha</label>
                            <input type="password" name="password"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 
                                    focus:bg-white rounded p-2"
                                required>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label for="password_confirmation" class="block font-medium">Confirmar Senha</label>
                            <input type="password" name="password_confirmation"
                                class="w-full border border-gray-300 focus:border-stone-600 focus:ring-stone-600 
                            focus:bg-white rounded p-2"
                                required>
                        </div>
                    </div>

                    <label for="coordinator_type" class="block font-medium">Tipo de Coordenador</label>
                    <select name="coordinator_type"
                        class="w-full border border-gray-300 rounded focus:bg-white focus:border-stone-600 focus:ring-stone-600 cursor-pointer p-2"
                        required>
                        <option value="">Selecione...</option>
                        <option value="general" {{ old('coordinator_type') == 'general' ? 'selected' : '' }}>
                            Geral</option>
                        <option value="course" {{ old('coordinator_type') == 'course' ? 'selected' : '' }}>Curso
                        </option>
                    </select>


                    <!-- Botões para enviar ou cancelar -->
                    <br><br><br>
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
    </div>
</x-app-layout>
