<x-app-layout>
    {{-- Container principal com layout de duas colunas --}}
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-8">

        {{-- Coluna da Esquerda (Informações do Curso) --}}
        <div class="lg:w-1/3 space-y-6 lg:sticky lg:top-8 self-start">

            {{-- Card de Informações do Curso --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">

                {{-- Banner e ícone --}}
                <div class="relative mb-8">
                    <div class="w-full h-32 bg-gray-200 rounded-lg overflow-hidden relative group">
                        <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                            alt="Banner do Curso" class="object-cover w-full h-full">

                        {{-- Botão para Trocar o Banner --}}
                        @if (auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateBanner', $course->id) }}"
                                enctype="multipart/form-data"
                                class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_banner" id="bannerUpload" class="hidden"
                                    onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('bannerUpload').click()"
                                    class="bg-white px-3 py-1 text-sm rounded-full shadow-md hover:bg-gray-100 transition flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.9a2 2 0 001.664-1.11l.888-1.776A2 2 0 0110.112 3h3.776a2 2 0 011.664 1.11l.888 1.776A2 2 0 0018.1 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Trocar Banner
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="relative">
                        <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('images/default-icon.png') }}"
                            alt="Ícone do Curso"
                            class="w-24 h-24 rounded-full border-4 border-white absolute -bottom-10 left-4 object-cover">

                        {{-- Botão para Trocar o Ícone (sempre visível para admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateIcon', $course->id) }}"
                                enctype="multipart/form-data"
                                class="absolute -bottom-10 left-20 transition-opacity duration-300 opacity-100">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_icon" id="iconUpload" class="hidden"
                                    onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('iconUpload').click()"
                                    class="bg-white text-xs px-2 py-1 rounded-full shadow-md hover:bg-gray-100 transition flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Editar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-10">
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-bold text-stone-800">{{ $course->course_name }}</h1>

                        {{-- Botão de Seguir/Deixar de Seguir --}}
                        @auth
                            <div data-course-id="{{ $course->id }}">
                                @if (auth()->user()->followedCourses->contains($course->id))
                                    <button type="button"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-1.5 rounded-full shadow transition"
                                        id="unfollowButton" data-course-id="{{ $course->id }}">
                                        ✔ Seguindo
                                    </button>
                                @else
                                    <button type="button"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-1.5 rounded-full shadow transition"
                                        id="followButton" data-course-id="{{ $course->id }}">
                                        + Seguir
                                    </button>
                                @endif
                            </div>
                        @endauth
                    </div>

                    {{-- Contagem de Membros --}}
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="font-semibold">{{ $course->followers()->count() }}</span>
                        {{ Str::plural('Seguidores', $course->followers()->count()) }}
                    </p>

                    <p class="text-sm text-gray-500 mt-1">
                        <strong class="font-semibold">Coordenador:</strong>
                        @if ($course->courseCoordinator?->userAccount)
                            <a href="{{ route('profile.view', $course->courseCoordinator->userAccount->id) }}"
                                class="text-blue-500 hover:underline">
                                {{ $course->courseCoordinator->userAccount->name }}
                            </a>
                        @else
                            Nenhum coordenador definido
                        @endif
                    </p>

                    {{-- Campo de Descrição (Adicionado) --}}
                    <div x-data="{ isEditing: false, description: '{{ addslashes($course->course_description) }}' }">
                        <h3 class="text-base font-semibold text-stone-800 mt-4 mb-2 flex items-center gap-2">
                            Descrição do Curso
                            @if (auth()->user()->user_type === 'admin')
                                <button x-show="!isEditing" @click="isEditing = true"
                                    class="text-gray-400 hover:text-blue-500 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </h3>

                        {{-- Visualização da Descrição --}}
                        <div x-show="!isEditing" class="prose max-w-none text-gray-700 text-sm">
                            <p class="text-sm text-gray-700">
                                @if ($course->course_description)
                                    {{ $course->course_description }}
                                @else
                                    (Sem descrição)
                                @endif
                            </p>
                        </div>

                        {{-- Formulário de Edição (Somente para admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <form x-show="isEditing" action="{{ route('courses.updateDescription', $course->id) }}"
                                method="POST" @submit.prevent="$el.submit()">
                                @csrf
                                @method('PUT')
                                <textarea x-model="description" name="course_description" rows="5"
                                    class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                                <div class="mt-2 flex gap-2 justify-end">
                                    <button type="button"
                                        @click="isEditing = false; description = '{{ addslashes($course->course_description) }}'"
                                        class="px-3 py-1 text-sm text-gray-700 border border-gray-300 rounded-full hover:bg-gray-100 transition">Cancelar</button>
                                    <button type="submit"
                                        class="px-3 py-1 text-sm text-white bg-blue-600 rounded-full hover:bg-blue-700 transition">Salvar</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    {{-- Container de Ações do Admin (Edit/Delete Course) --}}
                    @if (auth()->user()->user_type === 'admin')
                        <div class="mt-6 border-t border-gray-200 pt-4 flex justify-end gap-4">
                            {{-- Botão Editar Curso --}}
                            <a href="{{ route('courses.edit', $course->id) }}"
                                class="flex items-center gap-1 px-3 py-1 text-sm text-blue-600 hover:text-blue-700 border border-blue-300 rounded-full shadow-sm transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Editar Curso
                            </a>

                            {{-- Botão Excluir Curso (abre modal) --}}
                            <button type="button" onclick="openModal('deleteModal-{{ $course->id }}')"
                                class="flex items-center gap-1 px-3 py-1 text-sm text-red-600 hover:text-red-700 border border-red-300 rounded-full shadow-sm transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3" />
                                </svg>
                                Excluir Curso
                            </button>

                            {{-- Formulário de Exclusão --}}
                            <form id="deleteCourseForm" action="{{ route('courses.destroy', $course->id) }}"
                                method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Coluna da Direita (Tabs de Conteúdo) --}}
        <div class="lg:w-2/3">
            <div x-data="{ tab: 'events' }" class="bg-white rounded-2xl shadow-lg p-6">

                {{-- Navegação por abas --}}
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="tab = 'events'"
                            :class="{ 'border-blue-500 text-blue-600 font-semibold': tab === 'events', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'events' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                </rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Eventos
                        </button>
                        <button @click="tab = 'posts'"
                            :class="{ 'border-blue-500 text-blue-600 font-semibold': tab === 'posts', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'posts' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Posts
                        </button>
                    </nav>
                </div>

                {{-- Conteúdo da aba "Eventos" --}}
                <div x-show="tab === 'events'">
                    <h2 class="text-xl font-bold text-stone-800 mb-4">Eventos do Curso</h2>
                    @if (auth()->user()->user_type === 'coordinator' && auth()->user()->id === $course->courseCoordinator->user_id)
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('events.create', ['course_id' => $course->id]) }}"
                                class="bg-gray-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                                + Criar Evento
                            </a>
                        </div>
                    @endif

                    {{-- Lista de Eventos --}}
                    @if ($course->events->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($course->events->sortByDesc('event_scheduled_at') as $event)
                                <a href="{{ route('events.show', $event->id) }}"
                                    class="bg-white rounded-2xl shadow-md border border-gray-200 hover:border-blue-500 transition-colors duration-200 overflow-hidden">
                                    @if ($event->event_image)
                                        <div class="w-full h-36">
                                            <img src="{{ asset('storage/' . $event->event_image) }}"
                                                alt="Capa do Evento" class="object-cover w-full h-full">
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h4 class="text-lg font-bold text-stone-800 truncate">{{ $event->event_name }}
                                        </h4>
                                        @if ($event->event_scheduled_at)
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $event->event_scheduled_at->format('d/m/Y') }} às
                                                {{ $event->event_scheduled_at->format('H:i') }}
                                            </p>
                                        @endif
                                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                            {{ $event->event_description }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center text-sm">Nenhum evento foi criado para este curso ainda.
                        </p>
                    @endif
                </div>

                {{-- Conteúdo da aba "Posts" --}}
                <div x-show="tab === 'posts'">
                    @livewire('course-posts', ['course' => $course])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Modal de Exclusão para Curso --}}
<div id="deleteModal-{{ $course->id }}"
    class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-md shadow-md w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4 text-red-600">Confirmar Exclusão</h2>
        <p>Tem certeza que deseja excluir este curso? Esta ação não poderá ser desfeita.</p>
        <div class="mt-6 flex justify-end space-x-2">
            <button onclick="closeModal('deleteModal-{{ $course->id }}')"
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
            <form action="{{ route('courses.destroy', $course->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Confirmar
                    Exclusão</button>
            </form>
        </div>
    </div>
</div>

{{-- Scripts compilados --}}
@vite('resources/js/app.js')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('div[data-course-id]'); // Tenta encontrar o container do botão

        if (container) {
            container.addEventListener('click', function(e) {
                const button = e.target.closest('button');
                if (!button || !button.dataset.courseId) return;

                const courseId = button.dataset.courseId;
                let method, url;

                if (button.id === 'followButton') {
                    // Lógica para SEGUIR
                    method = 'POST';
                    // Assumindo uma rota como: /courses/123/follow
                    url = `/courses/${courseId}/follow`;
                } else if (button.id === 'unfollowButton') {
                    // Lógica para DEIXAR DE SEGUIR
                    method = 'DELETE';
                    // Assumindo uma rota como: /courses/123/unfollow
                    url = `/courses/${courseId}/unfollow`;
                } else {
                    return;
                }

                // Desabilita o botão para prevenir cliques duplos
                button.disabled = true;

                fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Falha na operação. Status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // 1. Atualiza o visual do botão
                        updateButtonState(button, method);

                        // 2. Reabilita o botão
                        button.disabled = false;
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao processar a solicitação. Tente novamente.');
                        button.disabled = false; // Reabilita em caso de erro
                    });
            });
        }

        function updateButtonState(currentButton, currentMethod) {
            if (currentMethod === 'POST') {
                // Se a operação foi POST (Seguir), muda para o estado 'Seguindo'
                currentButton.id = 'unfollowButton';
                currentButton.textContent = '✔ Seguindo';
                currentButton.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'text-white');
                currentButton.classList.add('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
            } else if (currentMethod === 'DELETE') {
                // Se a operação foi DELETE (Deixar de Seguir), muda para o estado 'Seguir'
                currentButton.id = 'followButton';
                currentButton.textContent = '+ Seguir';
                currentButton.classList.remove('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
                currentButton.classList.add('bg-blue-600', 'hover:bg-blue-700', 'text-white');
            }
        }
    });
</script>
