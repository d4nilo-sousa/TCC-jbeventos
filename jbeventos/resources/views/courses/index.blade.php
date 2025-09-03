<x-app-layout backgroundClass="bg-gradient-to-br from-blue-500 via-blue-200 to-blue-100">
    <div class="py-[5rem] min-h-screen">
        <div class="w-full max-w-[100rem] mx-auto sm:px-6 lg:px-5 flex justify-center">
            <div class="w-full bg-white shadow-md rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto min-h-[70vh]">

                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-3 gap-5 w-full flex-wrap mb-10 mt-3">
                    <div class="mt-1">
                        <p
                            class="text-center bg-gradient-to-r from-stone-900 to-stone-400 bg-clip-text text-transparent font-extrabold text-3xl sm:text-5xl tracking-wide drop-shadow-md">
                            Lista de Cursos
                        </p>
                        {{-- "hr" personalizado --}}
                        <div class="w-[5rem] h-1 bg-blue-500 rounded-full mt-3 shadow-xl"></div>
                    </div>

                    @php
                        $loggedAdmin = auth()->user()->admin;
                    @endphp

                    <div
                        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-5 w-full sm:w-auto flex-wrap">
                        <!-- Botão Criar Curso -->
                        <div
                            class="flex justify-center gap-1 border-2 rounded-full overflow-hidden shadow-md transition-colors duration-200">
                            @if ($loggedAdmin)
                                <a href="{{ route('courses.create') }}"
                                    class="text-blue-700 gap-2 px-5 py-2 rounded-lg flex items-center justify-center
                                        hover:bg-blue-500 hover:text-white transition-colors duration-200">
                                    <img src="{{ asset('imgs/add-button.png') }}" class="w-8">
                                    Criar Curso
                                </a>
                            @endif
                        </div>

                        <!-- Barra de pesquisa -->
                        <form action="{{ route('courses.index') }}" method="GET"
                            class="flex items-center w-full sm:w-auto">
                            <div
                                class="flex items-center bg-white rounded-full overflow-hidden shadow-md border-2 w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Pesquisar cursos..." autocomplete="off"
                                    class="px-6 flex-1 min-w-[200px] sm:min-w-[300px] lg:min-w-[350px] text-gray-800 placeholder-gray-500 border-none outline-none focus:ring-0 bg-white">
                                <button type="submit"
                                    class="flex items-center justify-center bg-stone-900 hover:bg-stone-800 transition-colors px-6 py-3">
                                    <img src="{{ asset('imgs/lupaBranca.svg') }}" class="w-7 h-7">
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                {{-- Mensagens de sucesso --}}
                @if (session('success'))
                    <div class="mb-4 p-4 text-green-800 bg-green-100 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif



                <div
                    class="bg-white shadow-xl rounded-2xl p-4 sm:p-6 lg:p-9 mx-auto border-2 border-stone-100">
                    <div class="mb-10 ">
                        {{-- Lista de cursos --}}
                        @if ($courses->count())
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-10 p-4 mt-10 w-full">
                                @foreach ($courses as $course)
                                    <div
                                        class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-lg transition flex flex-col">
                                        {{-- Imagem do evento ou placeholder --}}
                                        @if ($course->course_icon)
                                            <img src="{{ $course->image ? asset('storage/' . $course->image) : asset('imgs/placeholder.png') }}"
                                                alt="{{ $course->name }}" class="h-48 w-full object-cover">
                                        @else
                                            <div
                                                class="h-48 w-full bg-blue-100 flex items-center justify-center text-gray-500">
                                                Sem imagem
                                            </div>
                                        @endif


                                        <div class="p-4 flex flex-col flex-grow">
                                            <h3 class="font-bold text-2xl text-stone-700 mb-2">
                                                {{ $course->course_name }}
                                            </h3>
                                            <p class="text-xs text-gray-500 mb-4">
                                                Coordenador:
                                                {{ $course->coordinator->userAccount->name ?? 'Não definido' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                    </div>






                    {{-- Paginação --}}
                    <div class="mt-6">
                        {{ $courses->links() }}
                    </div>
                @else
                    <div class="w-full flex flex-col items-center mt-16">
                        <p class="text-gray-500 mt-5 text-base sm:text-lg">Nenhum evento cadastrado . . .</p>
                    </div>
                    @endif
                </div>



            </div>
        </div>
    </div>
</x-app-layout>
