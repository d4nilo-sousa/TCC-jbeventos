<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-8">

        {{-- ===================================================================================== --}}
        {{-- COLUNA DA ESQUERDA (Informações do Curso) | Fixo/Sticky no Desktop --}}
        {{-- ===================================================================================== --}}
        <div class="lg:w-1/3 space-y-6 lg:sticky lg:top-8 self-start">

            {{-- Card de Informações do Curso --}}
            <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100">
                <div class="relative mb-8">
                    {{-- Banner e Ícone --}}
                    <div class="w-full h-32 bg-gray-200 rounded-xl overflow-hidden relative group">
                        <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                            alt="Banner do Curso" class="object-cover w-full h-full">
                        
                        {{-- Botão Trocar Banner (Admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateBanner', $course->id) }}"
                                enctype="multipart/form-data"
                                class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_banner" id="bannerUpload" class="hidden"
                                    onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('bannerUpload').click()"
                                    class="bg-white px-3 py-1 text-xs rounded-full shadow-lg hover:bg-gray-100 transition flex items-center gap-1 font-medium">
                                    <i class="ph-bold ph-image-square text-sm"></i>
                                    Trocar Banner
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="relative">
                        <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('images/default-icon.png') }}"
                            alt="Ícone do Curso"
                            class="w-24 h-24 rounded-full border-4 border-white absolute -bottom-10 left-4 object-cover shadow-md">

                        {{-- Botão Editar Ícone (Admin) --}}
                        @if (auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateIcon', $course->id) }}"
                                enctype="multipart/form-data" class="absolute -bottom-10 left-20">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_icon" id="iconUpload" class="hidden"
                                    onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('iconUpload').click()"
                                    class="bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow-md hover:bg-red-600 transition flex items-center gap-1 border-2 border-white">
                                    <i class="ph-bold ph-pencil-simple text-sm"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-10">
                    <div class="flex items-start justify-between">
                        <h1 class="text-2xl font-extrabold text-stone-800 leading-snug">{{ $course->course_name }}</h1>

                        {{-- Botão de Seguir/Deixar de Seguir --}}
                        @auth
                            <div data-course-id="{{ $course->id }}" class="flex-shrink-0 ml-4">
                                @if (auth()->user()->followedCourses->contains($course->id))
                                    <button type="button"
                                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-1.5 rounded-full shadow-lg transition flex items-center gap-1"
                                        id="unfollowButton" data-course-id="{{ $course->id }}">
                                        <i class="ph-fill ph-heart text-white text-base"></i> Seguindo
                                    </button>
                                @else
                                    <button type="button"
                                        class="bg-gray-200 hover:bg-red-500 hover:text-white text-gray-700 text-sm font-medium px-4 py-1.5 rounded-full shadow-lg transition flex items-center gap-1"
                                        id="followButton" data-course-id="{{ $course->id }}">
                                        <i class="ph-bold ph-heart text-red-600 hover:text-white text-base"></i> Seguir
                                    </button>
                                @endif
                            </div>
                        @endauth
                    </div>

                    {{-- Contagem de Membros --}}
                    @php
                        $followersCount = $course->followers()->count();
                    @endphp
                    <p class="text-sm text-gray-500 mt-2 flex items-center gap-1">
                        <i class="ph-fill ph-users text-red-500 text-lg"></i>
                        <span class="font-bold" id="followersCount">{{ $followersCount }}</span>
                        <span
                            id="followersText">{{ $followersCount === 0 ? 'Nenhum seguidor' : ($followersCount === 1 ? 'Seguidor' : 'Seguidores') }}</span>
                    </p>

                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                        <i class="ph-fill ph-crown text-red-500 text-lg"></i>
                        <strong class="font-semibold">Coordenador:</strong>
                        @if ($course->courseCoordinator?->userAccount)
                            <a href="{{ route('profile.view', $course->courseCoordinator->userAccount->id) }}"
                                class="text-red-500 hover:underline font-medium">
                                {{ $course->courseCoordinator->userAccount->name }}
                            </a>
                        @else
                            <span class="font-medium">Não definido</span>
                        @endif
                    </p>

                    {{-- Campo de Descrição (com Alpine.js para edição inline) --}}
                    <div x-data="{ isEditing: false, description: '{{ addslashes($course->course_description) }}' }" class="mt-6">
                        <h3 class="text-base font-bold text-stone-800 mb-3 border-b border-gray-200 pb-2 flex items-center justify-between">
                            Descrição do Curso
                            @if (auth()->user()->user_type === 'admin')
                                <button x-show="!isEditing" @click="isEditing = true"
                                    class="text-gray-400 hover:text-red-500 transition">
                                    <i class="ph-bold ph-pencil-simple text-base"></i>
                                </button>
                            @endif
                        </h3>

                        {{-- Visualização da Descrição --}}
                        <div x-show="!isEditing" class="prose max-w-none text-gray-700 text-sm leading-relaxed">
                            <p class="text-sm text-gray-700">
                                @if ($course->course_description)
                                    {{ $course->course_description }}
                                @else
                                    <em class="text-gray-400">(Sem descrição no momento)</em>
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
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm shadow-sm"></textarea>
                                <div class="mt-3 flex gap-2 justify-end">
                                    <button type="button"
                                        @click="isEditing = false; description = '{{ addslashes($course->course_description) }}'"
                                        class="px-3 py-1 text-sm text-gray-700 border border-gray-300 rounded-full hover:bg-gray-100 transition shadow-sm">Cancelar</button>
                                    <button type="submit"
                                        class="px-3 py-1 text-sm text-white bg-red-600 rounded-full hover:bg-red-700 transition shadow-md">Salvar</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    {{-- Container de Ações do Admin (Edit/Delete Course) --}}
                    @if (auth()->user()->user_type === 'admin')
                        <div class="mt-6 border-t border-gray-200 pt-4 flex justify-end gap-3">
                            {{-- Botão Editar Curso --}}
                            <a href="{{ route('courses.edit', $course->id) }}"
                                class="flex items-center gap-1 px-4 py-1.5 text-sm text-black hover:text-red-600 border border-black rounded-full shadow-sm transition-colors duration-200 font-medium">
                                <i class="ph-bold ph-note-pencil text-base"></i>
                                Editar Dados
                            </a>

                            {{-- Botão Excluir Curso (abre modal) --}}
                            <button type="button" onclick="openModal('deleteModal-{{ $course->id }}')"
                                class="flex items-center gap-1 px-4 py-1.5 text-sm text-red-600 hover:text-white border border-red-300 hover:bg-red-600 rounded-full shadow-sm transition-colors duration-200 font-medium">
                                <i class="ph-bold ph-trash text-base"></i>
                                Excluir Curso
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- ===================================================================================== --}}
        {{-- COLUNA DA DIREITA (Eventos e Posts) | Layout de Grade em 2 Colunas (XL) --}}
        {{-- ===================================================================================== --}}
        <div class="lg:w-2/3">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                
                {{-- COLUNA CENTRAL (EVENTOS) --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-stone-800 flex items-center gap-2">
                            <i class="ph-bold ph-calendar-blank text-red-600"></i> Próximos Eventos
                        </h2>
                        @if (auth()->user()->user_type === 'coordinator' && auth()->user()->id === $course->courseCoordinator?->user_id)
                            <a href="{{ route('events.create', ['course_id' => $course->id]) }}"
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-full shadow-md transition-colors duration-200 text-sm flex items-center gap-1">
                                <i class="ph-bold ph-plus text-base"></i> Criar Evento
                            </a>
                        @endif
                    </div>

                    {{-- Lista de Eventos --}}
                    @if ($course->events->isNotEmpty())
                        <div class="space-y-4">
                            @foreach ($course->events->sortByDesc('event_scheduled_at') as $event)
                                <a href="{{ route('events.show', $event->id) }}"
                                    class="block bg-white rounded-xl shadow-lg border border-gray-100 hover:border-red-500 transition-colors duration-200 overflow-hidden group">
                                    <div class="flex">
                                        {{-- Imagem ou Placeholder --}}
                                        @if ($event->event_image)
                                            <div class="w-2/5 h-32 flex-shrink-0">
                                                <img src="{{ asset('storage/' . $event->event_image) }}"
                                                    alt="Capa do Evento" class="object-cover w-full h-full">
                                            </div>
                                        @else
                                            <div
                                                class="w-2/5 h-32 flex-shrink-0 flex flex-col items-center justify-center text-red-500 bg-gray-50 dark:text-red-400 border-r border-gray-200">
                                                <i class="ph-bold ph-calendar-blank text-3xl"></i>
                                                <p class="mt-1 text-xs text-gray-500">Sem Imagem</p>
                                            </div>
                                        @endif

                                        <div class="p-4 space-y-1 w-3/5">
                                            <h4 class="text-base font-bold text-stone-800 line-clamp-2 group-hover:text-red-600 transition">
                                                {{ $event->event_name }}
                                            </h4>
                                            
                                            {{-- Data e Hora --}}
                                            @if ($event->event_scheduled_at)
                                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                                    <i class="ph-fill ph-clock-clockwise text-red-600 text-base flex-shrink-0"></i>
                                                    <span>{{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}</span>
                                                </div>
                                            @endif

                                            {{-- Localização --}}
                                            @if ($event->event_location)
                                                <div class="flex items-center gap-2 text-sm text-gray-700">
                                                    <i class="ph-fill ph-map-pin text-red-600 text-base flex-shrink-0"></i>
                                                    <span class="truncate text-xs">{{ $event->event_location }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-xl shadow-lg p-6 text-center border border-gray-100">
                            <i class="ph-bold ph-calendar-x text-5xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Nenhum evento foi criado para este curso ainda.</p>
                        </div>
                    @endif
                </div>

                {{-- COLUNA LATERAL (POSTS/MURAL) --}}
                <div>
                    <h2 class="text-2xl font-bold text-stone-800 mb-4 flex items-center gap-2">
                        <i class="ph-bold ph-file-text text-red-600"></i> Mural de Posts
                    </h2>
                    
                    {{-- Lista de Posts (via Livewire) --}}
                    {{-- O componente Livewire lida com a criação e listagem dos posts --}}
                    @livewire('course-posts', ['course' => $course])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- ===================================================================================== --}}
{{-- MODAL E SCRIPTS (Sem Alteração, mantendo a funcionalidade) --}}
{{-- ===================================================================================== --}}

{{-- Modal de Exclusão para Curso --}}
<div id="deleteModal-{{ $course->id }}"
    class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 ease-in-out scale-95" onclick="event.stopPropagation();">
        <h2 class="text-xl font-bold mb-4 text-red-600 flex items-center gap-2">
            <i class="ph-bold ph-warning-circle text-2xl"></i> Confirmar Exclusão
        </h2>
        <p class="text-gray-700">Tem certeza que deseja excluir o curso <strong>"{{ $course->course_name }}"</strong>? Esta ação é **irreversível**.</p>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closeModal('deleteModal-{{ $course->id }}')"
                class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 font-medium transition">Cancelar</button>
            <form action="{{ route('courses.destroy', $course->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-sm bg-red-600 text-white rounded-full hover:bg-red-700 font-medium transition">Confirmar Exclusão</button>
            </form>
        </div>
    </div>
</div>

{{-- Scripts e Funções do Modal --}}
@vite('resources/js/app.js')
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>


{{-- Script para Seguir/Deixar de Seguir Curso --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('div[data-course-id]');
        const followersCountSpan = document.getElementById('followersCount');
        const followersPluralSpan = document.getElementById('followersText');

        function updatePlural(count) {
            if (!followersPluralSpan) return;
            count = parseInt(count);
            if (count === 1) { 
                followersPluralSpan.textContent = 'Seguidor';
            } else {
                followersPluralSpan.textContent = 'Seguidores';
            }
        }
        
        if (container) {
            container.addEventListener('click', function(e) {
                const button = e.target.closest('button');
                if (!button || !button.dataset.courseId) return;

                const courseId = button.dataset.courseId;
                let method, url;

                if (button.id === 'followButton') {
                    method = 'POST';
                    url = `/courses/${courseId}/follow`;
                } else if (button.id === 'unfollowButton') {
                    method = 'DELETE';
                    url = `/courses/${courseId}/unfollow`;
                } else {
                    return;
                }

                button.disabled = true;
                
                // Adiciona um efeito de loading temporário
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="ph-bold ph-circle-notch animate-spin"></i> Processando';

                fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                        updateButtonState(button, method);
                        if (data.followers_count !== undefined && followersCountSpan) {
                            const newCount = data.followers_count;
                            followersCountSpan.textContent = newCount;
                            updatePlural(newCount);
                        }
                        button.disabled = false;
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao processar a solicitação. Tente novamente.');
                        button.innerHTML = originalContent; // Volta ao estado original em caso de erro
                        button.disabled = false;
                    });
            });
        }

        function updateButtonState(currentButton, currentMethod) {
            if (currentMethod === 'POST') {
                currentButton.id = 'unfollowButton';
                currentButton.innerHTML = '<i class="ph-fill ph-heart text-white text-base"></i> Seguindo';
                currentButton.classList.remove('bg-gray-200', 'hover:bg-red-500', 'text-gray-700'); 
                currentButton.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white');
            } else if (currentMethod === 'DELETE') {
                currentButton.id = 'followButton';
                currentButton.innerHTML = '<i class="ph-bold ph-heart text-red-600 text-base"></i> Seguir';
                currentButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'text-white');
                currentButton.classList.add('bg-gray-200', 'hover:bg-red-500', 'hover:text-white', 'text-gray-700');
            }
            currentButton.disabled = false;
        }
        
        // Inicializa o plural
        if (followersCountSpan) {
            updatePlural(parseInt(followersCountSpan.textContent.trim()));
        }
    });
</script>