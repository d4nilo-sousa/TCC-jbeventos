<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Curso
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

                <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="course_name" class="block font-medium">Nome do Curso</label>
                        <input type="text" name="course_name" id="course_name" value="{{ old('course_name', $course->course_name) }}" class="w-full border-gray-300 rounded shadow-sm" required>
                    </div>

                    <div>
                        <label for="course_description" class="block font-medium">Descrição</label>
                        <textarea name="course_description" id="course_description" class="w-full border-gray-300 rounded shadow-sm">{{ old('course_description', $course->course_description) }}</textarea>
                    </div>

                    <div>
                        <label for="coordinator_id" class="block font-medium">Coordenador (opcional)</label>
                        <select name="coordinator_id" id="coordinator_id" class="w-full border-gray-300 rounded shadow-sm">
                            <option value="">-- Nenhum --</option>
                            @foreach($coordinators as $coordinator)
                                <option value="{{ $coordinator->id }}" {{ (old('coordinator_id', $course->coordinator_id) == $coordinator->id) ? 'selected' : '' }}>
                                    {{ $coordinator->userAccount->name ?? 'Sem nome' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium">Ícone Atual</label>
                        <div class="mt-1">
                            @if($course->course_icon)
                                <img src="{{ asset('storage/' . $course->course_icon) }}" alt="Ícone do curso" class="w-20">
                            @else
                                <p>Nenhum ícone cadastrado.</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label for="course_icon" class="block font-medium">Alterar Ícone do Curso</label>
                        <input type="file" name="course_icon" id="course_icon" accept="image/*" class="w-full border-gray-300 rounded shadow-sm">
                    </div>

                    <div>
                        <label class="block font-medium">Banner Atual</label>
                        <div class="mt-1">
                            @if($course->course_banner)
                                <img src="{{ asset('storage/' . $course->course_banner) }}" alt="Banner do curso" class="w-36">
                            @else
                                <p>Nenhum banner cadastrado.</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label for="course_banner" class="block font-medium">Alterar Banner do Curso</label>
                        <input type="file" name="course_banner" id="course_banner" accept="image/*" class="w-full border-gray-300 rounded shadow-sm">
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar Curso</button>
                        <a href="{{ route('courses.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
