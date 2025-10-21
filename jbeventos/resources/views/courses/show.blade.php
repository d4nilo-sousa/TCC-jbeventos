<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col xl:flex-row gap-8">

        {{-- =============================== --}}
        {{-- COLUNA ESQUERDA — Informações --}}
        {{-- =============================== --}}
        <div class="xl:w-1/3 lg:w-2/5 space-y-6 xl:sticky xl:top-8 self-start">

            {{-- Card de Informações (ampliado) --}}
            <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
                {{-- Banner --}}
                <div class="relative mb-10">
                    <div class="w-full h-40 bg-gray-200 rounded-xl overflow-hidden group">
                        <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                             alt="Banner do Curso" class="object-cover w-full h-full">

                        @if (auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateBanner', $course->id) }}"
                                  enctype="multipart/form-data"
                                  class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_banner" id="bannerUpload" class="hidden"
                                       onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('bannerUpload').click()"
                                        class="bg-white/90 backdrop-blur-sm px-3 py-1 text-xs rounded-full shadow hover:bg-gray-100 transition font-medium flex items-center gap-1">
                                    <i class="ph-bold ph-image-square text-sm"></i> Trocar Banner
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Ícone --}}
                    <div class="relative">
                        <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('images/default-icon.png') }}"
                             alt="Ícone do Curso"
                             class="w-28 h-28 rounded-full border-4 border-white absolute -bottom-12 left-6 object-cover shadow-md">

                        @if (auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateIcon', $course->id) }}"
                                  enctype="multipart/form-data" class="absolute -bottom-10 left-32">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_icon" id="iconUpload" class="hidden"
                                       onchange="this.form.submit()">
                                <button type="button" onclick="document.getElementById('iconUpload').click()"
                                        class="bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow-md hover:bg-red-600 transition flex items-center gap-1">
                                    <i class="ph-bold ph-pencil-simple text-sm"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Dados do Curso --}}
                <div class="mt-10 space-y-3">
                    <h1 class="text-3xl font-extrabold text-stone-800 leading-tight">{{ $course->course_name }}</h1>

                    {{-- Coordenador --}}
                    <p class="text-sm text-gray-600 flex items-center gap-1">
                        <i class="ph-fill ph-crown text-red-500 text-lg"></i>
                        <strong>Coordenador:</strong>
                        @if ($course->courseCoordinator?->userAccount)
                            <a href="{{ route('profile.view', $course->courseCoordinator->userAccount->id) }}"
                               class="text-red-600 hover:underline font-medium">
                                {{ $course->courseCoordinator->userAccount->name }}
                            </a>
                        @else
                            <span class="font-medium text-gray-500">Não definido</span>
                        @endif
                    </p>

                    {{-- Seguidores --}}
                    @php $followersCount = $course->followers()->count(); @endphp
                    <p class="text-sm text-gray-500 flex items-center gap-1">
                        <i class="ph-fill ph-users text-red-500 text-lg"></i>
                        <span class="font-bold" id="followersCount">{{ $followersCount }}</span>
                        <span id="followersText">
                            {{ $followersCount === 0 ? 'Nenhum seguidor' : ($followersCount === 1 ? 'Seguidor' : 'Seguidores') }}
                        </span>
                    </p>

                    {{-- Botão Seguir --}}
                    @auth
                        <div data-course-id="{{ $course->id }}">
                            @if (auth()->user()->followedCourses->contains($course->id))
                                <button id="unfollowButton" data-course-id="{{ $course->id }}"
                                    class="mt-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2 rounded-full shadow-md transition flex items-center gap-1">
                                    <i class="ph-fill ph-heart text-white text-base"></i> Seguindo
                                </button>
                            @else
                                <button id="followButton" data-course-id="{{ $course->id }}"
                                    class="mt-3 bg-gray-100 hover:bg-red-500 hover:text-white text-gray-700 text-sm font-medium px-5 py-2 rounded-full shadow-md transition flex items-center gap-1">
                                    <i class="ph-bold ph-heart text-red-600 text-base"></i> Seguir
                                </button>
                            @endif
                        </div>
                    @endauth
                </div>

                {{-- Descrição --}}
                <div x-data="{ isEditing: false, description: '{{ addslashes($course->course_description) }}' }"
                     class="mt-8 border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-stone-800">Descrição</h3>
                        @if (auth()->user()->user_type === 'admin')
                            <button x-show="!isEditing" @click="isEditing = true"
                                    class="text-gray-400 hover:text-red-500 transition">
                                <i class="ph-bold ph-pencil-simple text-base"></i>
                            </button>
                        @endif
                    </div>

                    <div x-show="!isEditing" class="text-sm text-gray-700 leading-relaxed">
                        @if ($course->course_description)
                            {{ $course->course_description }}
                        @else
                            <em class="text-gray-400">(Sem descrição no momento)</em>
                        @endif
                    </div>

                    @if (auth()->user()->user_type === 'admin')
                        <form x-show="isEditing" action="{{ route('courses.updateDescription', $course->id) }}"
                              method="POST" @submit.prevent="$el.submit()">
                            @csrf
                            @method('PUT')
                            <textarea x-model="description" name="course_description" rows="4"
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm shadow-sm"></textarea>
                            <div class="mt-3 flex gap-2 justify-end">
                                <button type="button"
                                    @click="isEditing = false; description = '{{ addslashes($course->course_description) }}'"
                                    class="px-3 py-1 text-sm text-gray-700 border border-gray-300 rounded-full hover:bg-gray-100 transition">Cancelar</button>
                                <button type="submit"
                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded-full hover:bg-red-700 transition">Salvar</button>
                            </div>
                        </form>
                    @endif
                </div>

                {{-- BOTÕES Editar e Excluir Curso --}}
                @if (auth()->user()->user_type === 'admin')
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('courses.edit', $course->id) }}"
                           class="flex-1 bg-gray-600 hover:bg-gray-900 text-white text-sm font-semibold py-2 rounded-full text-center transition">
                            <i class="ph-bold ph-pencil-simple mr-1"></i> Editar Curso
                        </a>
                        <button onclick="openModal('deleteModal-{{ $course->id }}')"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 rounded-full transition">
                            <i class="ph-bold ph-trash mr-1"></i> Excluir Curso
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- =============================== --}}
        {{-- COLUNA DIREITA — Conteúdos --}}
        {{-- =============================== --}}
        <div class="xl:w-2/3 lg:w-3/5 space-y-10">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                {{-- EVENTOS --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-stone-800 flex items-center gap-2">
                            <i class="ph ph-calendar-blank text-red-600 text-lg"></i> Eventos
                        </h2>
                    </div>

                    @if ($course->events->isNotEmpty())
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($course->events->sortByDesc('event_scheduled_at') as $event)
                            <a href="{{ route('events.show', $event->id) }}"
                            class="flex bg-white rounded-xl shadow-sm border border-gray-100 hover:border-red-400 transition overflow-hidden group">
                                {{-- Imagem do evento --}}
                                @if ($event->event_image)
                                    <img src="{{ asset('storage/' . $event->event_image) }}" 
                                        alt="{{ $event->event_name }}"
                                        class="object-cover w-28 h-28 rounded-l-xl">
                                @else
                                    <div class="flex flex-col items-center justify-center w-28 h-28 bg-gray-100 rounded-l-xl text-red-500">
                                        <i class="ph-bold ph-calendar-blank text-3xl"></i>
                                        <p class="mt-1 text-[11px] text-gray-500 text-center leading-tight">
                                            Sem imagem
                                        </p>
                                    </div>
                                @endif

                                {{-- Conteúdo do card --}}
                                <div class="p-4 flex-1 flex flex-col justify-center">
                                    <h4 class="text-base font-semibold text-stone-800 line-clamp-2 group-hover:text-red-600 transition">
                                        {{ $event->event_name }}
                                    </h4>
                                    @if ($event->event_scheduled_at)
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <i class="ph-fill ph-clock-clockwise text-red-600 text-sm"></i>
                                            {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @endforeach

                        </div>
                    @else
                        <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-gray-100">
                            <i class="ph-bold ph-calendar-x text-5xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Nenhum evento criado ainda.</p>
                        </div>
                    @endif
                </div>

                {{-- POSTS --}}
                <div>
                    <h2 class="text-xl font-bold text-stone-800 mb-4 flex items-center gap-2">
                        <i class="ph ph-article text-red-600 text-lg"></i> Posts
                    </h2>
                    @livewire('course-posts', ['course' => $course])
                </div>
            </div>
        </div>
    </div>

    {{-- Botão flutuante Criar Evento --}}
    @if (auth()->user()->user_type === 'coordinator' && auth()->user()->id === $course->courseCoordinator?->user_id)
        <a href="{{ route('events.create', ['course_id' => $course->id]) }}"
           class="fixed bottom-8 right-8 bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-3 rounded-full shadow-lg transition flex items-center gap-2 group"
           title="Criar evento">
            <i class="ph-bold ph-plus text-lg"></i>
            <span class="hidden sm:inline-block group-hover:inline-block transition">Criar Evento</span>
        </a>
    @endif
</x-app-layout>

{{-- =============================== --}}
{{-- MODAL E SCRIPTS --}}
{{-- =============================== --}}
<div id="deleteModal-{{ $course->id }}"
     class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation();">
        <h2 class="text-xl font-bold mb-4 text-red-600 flex items-center gap-2">
            <i class="ph-bold ph-warning-circle text-2xl"></i> Confirmar Exclusão
        </h2>
        <p class="text-gray-700">Tem certeza que deseja excluir o curso <strong>"{{ $course->course_name }}"</strong>? Esta ação é irreversível.</p>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closeModal('deleteModal-{{ $course->id }}')"
                    class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 font-medium transition">Cancelar</button>
            <form action="{{ route('courses.destroy', $course->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-full hover:bg-red-700 font-medium transition">Confirmar</button>
            </form>
        </div>
    </div>
</div>

@vite('resources/js/app.js')
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>

{{-- Script seguir/deixar de seguir --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('div[data-course-id]');
    const followersCountSpan = document.getElementById('followersCount');
    const followersPluralSpan = document.getElementById('followersText');

    function updatePlural(count) {
        if (!followersPluralSpan) return;
        followersPluralSpan.textContent = count === 1 ? 'Seguidor' : 'Seguidores';
    }

    if (container) {
        container.addEventListener('click', async e => {
            const button = e.target.closest('button');
            if (!button || !button.dataset.courseId) return;

            const courseId = button.dataset.courseId;
            const isFollow = button.id === 'followButton';
            const url = `/courses/${courseId}/${isFollow ? 'follow' : 'unfollow'}`;
            const method = isFollow ? 'POST' : 'DELETE';

            button.disabled = true;
            const original = button.innerHTML;
            button.innerHTML = '<i class="ph-bold ph-circle-notch animate-spin"></i> Processando';

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Erro');
                const data = await res.json();
                followersCountSpan.textContent = data.followers_count;
                updatePlural(data.followers_count);
                toggleButton(button, isFollow);
            } catch (err) {
                alert('Erro ao processar solicitação.');
                button.innerHTML = original;
            } finally {
                button.disabled = false;
            }
        });
    }

    function toggleButton(btn, followed) {
        if (followed) {
            btn.id = 'unfollowButton';
            btn.innerHTML = '<i class="ph-fill ph-heart text-white"></i> Seguindo';
            btn.className = 'mt-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-1.5 rounded-full shadow-md transition';
        } else {
            btn.id = 'followButton';
            btn.innerHTML = '<i class="ph-bold ph-heart text-red-600"></i> Seguir';
            btn.className = 'mt-2 bg-gray-100 hover:bg-red-500 hover:text-white text-gray-700 text-sm font-medium px-4 py-1.5 rounded-full shadow-md transition';
        }
    }

    updatePlural(parseInt(followersCountSpan.textContent.trim()));
});
</script>
