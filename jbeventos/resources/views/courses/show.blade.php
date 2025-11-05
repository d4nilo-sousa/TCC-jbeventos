<x-app-layout>
    {{-- Container principal com layout de duas colunas (da fix_course_index_page) --}}
    <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col xl:flex-row gap-8">
        {{-- =============================== --}}
        {{-- COLUNA ESQUERDA — Informações (Mantida) --}}
        {{-- =============================== --}}
        <div class="xl:w-1/3 lg:w-2/5 space-y-6 xl:sticky xl:top-8 self-start">
            @php
                $previousUrl = url()->previous();
                $isFromExplore = str_contains($previousUrl, '/explore'); // verifica se veio do explore
            @endphp

            @if ($isFromExplore)
                <a href="{{ route('explore.index') }}"
                    class="text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 font-medium text-base mb-2">
                    <i class="ph-fill ph-arrow-left text-xl"></i> Voltar ao Explorar
                </a>
            @else
                <a href="{{ route('courses.index') }}"
                    class="text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 font-medium text-base mb-2">
                    <i class="ph-fill ph-arrow-left text-xl"></i> Voltar à Lista de Cursos
                </a>
            @endif

            {{-- ✅ Mensagem de sucesso específica do curso --}}
            @if (session('success_course'))
                <div id="success-course-message"
                    class="p-4 bg-green-50 border border-green-300 text-green-700 rounded-xl shadow-sm flex items-center gap-2 mb-4">
                    <i class="ph ph-info text-lg"></i>
                    <span>{{ session('success_course') }}</span>
                </div>

                <script>
                    // Faz a mensagem do curso sumir depois de 4 segundos
                    setTimeout(() => {
                        const msg = document.getElementById('success-course-message');
                        if (msg) {
                            msg.classList.add('opacity-0', 'translate-y-2'); // animação suave
                            setTimeout(() => msg.remove(), 500); // remove do DOM após sumir
                        }
                    }, 4000);
                </script>
            @endif



            {{-- Card de Informações (ampliado) --}}
            <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100">
                <div class="relative mb-10">
                    <div class="w-full h-40 bg-gray-200 rounded-xl overflow-hidden group">
                        <img id="courseBannerImg"
                            src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                            alt="Banner do Curso" class="object-cover w-full h-full">

                        @if (auth()->user()->user_type === 'admin')
                            <form id="bannerForm" enctype="multipart/form-data"
                                class="absolute top-3 right-3 transition-opacity">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_banner" id="bannerUpload" class="hidden">
                                <button type="button" onclick="document.getElementById('bannerUpload').click()"
                                    class="bg-white/90 backdrop-blur-sm px-3 py-1 text-xs rounded-full shadow hover:bg-gray-100 transition font-medium flex items-center gap-1">
                                    <i class="ph-bold ph-image-square text-sm"></i> Trocar Banner
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Ícone --}}
                    <div class="relative">
                        @if ($course->course_icon)
                            <img id="courseIconImg" src="{{ asset('storage/' . $course->course_icon) }}"
                                alt="Ícone do Curso"
                                class="w-28 h-28 rounded-full border-4 border-white absolute -bottom-12 left-6 object-cover shadow-md">
                        @else
                            <div id="courseIconImg"
                                class="w-28 h-28 rounded-full border-4 border-white absolute -bottom-12 left-6 shadow-md
        bg-gray-200 flex items-center justify-center">
                                <i class="ph ph-book-open text-5xl text-red-600"></i>
                            </div>
                        @endif

                        @if (auth()->user()->user_type === 'admin')
                            <form id="iconForm" enctype="multipart/form-data" class="absolute -bottom-10 left-32">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_icon" id="iconUpload" class="hidden">
                                <button type="button" onclick="document.getElementById('iconUpload').click()"
                                    class="bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow-md hover:bg-red-600 transition flex items-center gap-1">
                                    <i class="ph-bold ph-pencil-simple text-sm"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Dados do Curso --}}
                <div class="mt-14 space-y-3">
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
                <div x-data="{ isEditing: false, description: '{{ addslashes($course->course_description) }}' }" class="mt-5 border-t border-gray-200 pt-4"
                    @description-updated.window="description = $event.detail; isEditing = false">

                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-stone-800">Descrição</h3>
                        @if (auth()->user()->user_type === 'admin')
                            <button x-show="!isEditing" @click="isEditing = true"
                                class="text-gray-400 hover:text-red-500 transition">
                                <i class="ph-bold ph-pencil-simple text-base"></i>
                            </button>
                        @endif
                    </div>

                    <div x-show="!isEditing" class="text-sm text-gray-700 leading-relaxed break-words"
                        x-text="description || '(Sem descrição no momento)'">
                    </div>

                    @if (auth()->user()->user_type === 'admin')
                        <form x-show="isEditing" id="descriptionForm">
                            @csrf
                            @method('PUT')
                            <textarea x-model="description" name="course_description" rows="4"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm shadow-sm"></textarea>
                            <div class="mt-3 flex gap-2 justify-end">
                                <button type="button"
                                    @click="isEditing = false; description = '{{ addslashes($course->course_description) }}'"
                                    class="px-3 py-1 text-sm text-gray-700 border border-gray-300 rounded-full hover:bg-gray-100 transition">
                                    Cancelar
                                </button>
                                <button type="button" id="saveDescriptionBtn"
                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded-full hover:bg-red-700 transition">
                                    Salvar
                                </button>
                            </div>
                        </form>
                    @endif
                </div>


                {{-- ========================================================== --}}
                {{-- CORREÇÃO AQUI: Envolver os botões em um div com separador --}}
                {{-- ========================================================== --}}
                @if (auth()->user()->user_type === 'admin')
                    <div class="mt-5 border-t border-gray-200 pt-4">
                        {{-- BOTÕES Editar e Excluir Curso --}}
                        <div class="flex gap-3">
                            <a href="{{ route('courses.edit', $course->id) }}"
                                class="flex-1 bg-gray-600 hover:bg-gray-900 text-white text-sm font-semibold py-2 rounded-full text-center transition">
                                <i class="ph-bold ph-pencil-simple mr-1"></i> Editar Curso
                            </a>
                            <button onclick="openModal('deleteModal-{{ $course->id }}')"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 rounded-full transition">
                                <i class="ph-bold ph-trash mr-1"></i> Excluir Curso
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- =============================== --}}
        {{-- COLUNA DIREITA — Conteúdos (Da fix_course_index_page, substituindo as abas) --}}
        {{-- =============================== --}}
        <div class="xl:w-2/3 lg:w-3/5 space-y-10">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-stone-800 flex items-center gap-2">
                            <i class="ph ph-calendar-blank text-red-600 text-2xl"></i> Eventos
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
                                            class="object-cover w-36 h-36 rounded-l-xl">
                                    @else
                                        <div
                                            class="flex flex-col items-center justify-center w-36 h-36 bg-gray-100 rounded-l-xl text-red-500">
                                            {{-- w-36 h-36 em vez de w-28 h-28 --}}
                                            <i class="ph-bold ph-calendar-blank text-4xl"></i>
                                            <p class="mt-1 text-xs text-red-500 text-center leading-tight">
                                                {{-- text-xs em vez de text-[11px] --}}
                                                Sem Imagem
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Conteúdo do card --}}
                                    <div class="p-5 flex-1 flex flex-col justify-center"> {{-- p-5 em vez de p-4 --}}
                                        <h4
                                            class="text-lg font-semibold text-stone-800 line-clamp-2 group-hover:text-red-600 transition">
                                            {{-- text-lg em vez de text-base --}}
                                            {{ $event->event_name }}
                                        </h4>
                                        @if ($event->event_scheduled_at)
                                            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                                {{-- text-sm em vez de text-xs --}}
                                                <i class="ph-fill ph-clock-clockwise text-red-600 text-base"></i>
                                                {{-- text-base em vez de text-sm para o ícone --}}
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
                    <h2 class="text-2xl font-bold text-stone-800 mb-4 flex items-center gap-2">
                        <i class="ph ph-article text-red-600 text-2xl"></i> Posts
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
        <p class="text-gray-700">Tem certeza que deseja excluir o curso <strong>"{{ $course->course_name }}"</strong>?
            Esta ação é irreversível.</p>
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
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>

{{-- Script seguir/deixar de seguir (mantive igual) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SEGUIR / DEIXAR DE SEGUIR ---
        const container = document.querySelector('div[data-course-id]');
        const followersCountSpan = document.getElementById('followersCount');
        const followersPluralSpan = document.getElementById('followersText');

        function updatePlural(count) {
            if (!followersPluralSpan) return;
            if (count === 0) {
                followersPluralSpan.textContent = 'Nenhum seguidor';
            } else {
                followersPluralSpan.textContent = count === 1 ? 'Seguidor' : 'Seguidores';
            }
        }

        function toggleButton(btn, isFollowing) {
            const baseClasses =
                'mt-3 text-sm font-medium px-5 py-2 rounded-full shadow-md transition flex items-center gap-1';
            if (isFollowing) {
                btn.id = 'unfollowButton';
                btn.innerHTML = '<i class="ph-fill ph-heart text-white text-base"></i> Seguindo';
                btn.className = `${baseClasses} bg-red-600 hover:bg-red-700 text-white`;
            } else {
                btn.id = 'followButton';
                btn.innerHTML = '<i class="ph-bold ph-heart text-red-600 text-base"></i> Seguir';
                btn.className = `${baseClasses} bg-gray-100 hover:bg-red-500 hover:text-white text-gray-700`;
            }
        }

        if (container) {
            container.addEventListener('click', async e => {
                const button = e.target.closest('button');
                if (!button || !button.dataset.courseId) return;

                const courseId = button.dataset.courseId;
                const isFollowAction = button.id === 'followButton';

                let url, method;
                if (isFollowAction) {
                    url = `/courses/${courseId}/follow`;
                    method = 'POST';
                } else {
                    url = `/courses/${courseId}/unfollow`;
                    method = 'DELETE';
                }

                button.disabled = true;
                const original = button.innerHTML;
                button.innerHTML =
                    '<i class="ph-bold ph-circle-notch animate-spin text-sm"></i> Processando';

                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    });

                    if (!res.ok) {
                        let errorMessage = 'Erro no servidor.';
                        try {
                            const errorData = await res.json();
                            errorMessage = errorData.message || errorMessage;
                        } catch {}
                        throw new Error(errorMessage);
                    }

                    const data = await res.json();
                    followersCountSpan.textContent = data.followers_count;
                    updatePlural(data.followers_count);
                    toggleButton(button, isFollowAction);

                } catch (err) {
                    console.error('Erro:', err);
                    alert('Erro ao processar solicitação: ' + err.message);
                    button.innerHTML = original;
                } finally {
                    button.disabled = false;
                }
            });
        }

        if (followersCountSpan) {
            updatePlural(parseInt(followersCountSpan.textContent.trim()));
        }

        // --- FUNÇÃO GENÉRICA PARA UPLOAD DE IMAGEM ---
        function setupImageUpload(inputId, formId, imgId, uploadUrl, methodOverride = null) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                // Validação de tipo e tamanho
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    alert('Formato de imagem inválido! Use JPEG, PNG, GIF ou WEBP.');
                    this.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    alert('Imagem muito grande! Tamanho máximo de 2MB.');
                    this.value = '';
                    return;
                }

                const form = document.getElementById(formId);
                const formData = new FormData(form);

                const headers = {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                };
                if (methodOverride) {
                    headers['X-HTTP-Method-Override'] = methodOverride;
                }

                fetch(uploadUrl, {
                        method: 'POST',
                        headers: headers,
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        const img = document.getElementById(imgId);
                        if (img) {
                            const key = imgId.includes('Icon') ? 'icon_url' : 'banner_url';
                            img.src = data[key] + '?t=' + new Date().getTime();
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao atualizar a imagem.');
                    });
            });
        }

        // --- CONFIGURAÇÃO DE UPLOADS ---
        setupImageUpload('iconUpload', 'iconForm', 'courseIconImg',
            '{{ route('courses.updateIcon', $course->id) }}');
        setupImageUpload('bannerUpload', 'bannerForm', 'courseBannerImg',
            '{{ route('courses.updateBanner', $course->id) }}', 'PUT');

        // --- SALVAR DESCRIÇÃO VIA AJAX ---
        const saveBtn = document.getElementById('saveDescriptionBtn');
        const form = document.getElementById('descriptionForm');

        if (saveBtn && form) {
            saveBtn.addEventListener('click', function() {
                const url = '{{ route('courses.updateDescription', $course->id) }}';
                const formData = new FormData(form);

                saveBtn.disabled = true;
                const originalText = saveBtn.textContent;
                saveBtn.textContent = 'Salvando...';

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'X-HTTP-Method-Override': 'PUT',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Erro ao salvar a descrição.');
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Dispara um evento customizado para Alpine.js atualizar a descrição
                            form.dispatchEvent(new CustomEvent('description-updated', {
                                detail: data.course_description,
                                bubbles: true
                            }));
                        } else {
                            alert(data.message || 'Erro ao salvar a descrição.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert(err.message);
                    })
                    .finally(() => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = originalText;
                    });
            });
        }
    });
</script>

{{-- CSS para destaque do post --}}
<style>
  /* destaque principal */
  .highlight-post {
    position: relative;
    animation: postHighlightFade 2.8s ease-out forwards;
    z-index: 10;
    isolation: isolate;
  }

  /* efeito de brilho + leve zoom e sombra */
  @keyframes postHighlightFade {
    0% {
      box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
      background-color: rgba(254, 226, 226, 0);
      transform: scale(1.02);
    }
    15% {
      background-color: rgba(254, 226, 226, 0.4);
      box-shadow: 0 8px 30px -10px rgba(239, 68, 68, 0.25);
    }
    40% {
      background-color: rgba(254, 226, 226, 0.2);
      transform: scale(1.01);
    }
    70% {
      background-color: rgba(254, 226, 226, 0.1);
      box-shadow: 0 4px 15px -8px rgba(239, 68, 68, 0.1);
    }
    100% {
      background-color: transparent;
      box-shadow: none;
      transform: scale(1);
    }
  }

  /* borda pulsante */
  .highlight-post::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 1rem;
    border: 2px solid rgba(239, 68, 68, 0.35);
    pointer-events: none;
    animation: borderGlow 2.5s ease-out forwards;
    z-index: -1;
  }

  @keyframes borderGlow {
    0% {
      opacity: 0;
      transform: scale(0.96);
      box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
    }
    20% {
      opacity: 1;
      box-shadow: 0 0 20px 5px rgba(239, 68, 68, 0.25);
    }
    60% {
      opacity: 0.8;
      box-shadow: 0 0 10px 3px rgba(239, 68, 68, 0.15);
    }
    100% {
      opacity: 0;
      transform: scale(1);
      box-shadow: none;
    }
  }
</style>

<script>
(function() {
  const DEBUG = false;
  function debugLog(...args) { if (DEBUG) console.log('[post-highlight]', ...args); }

  // 🧩 Etapa 1 — impedir scroll automático do hash
  let savedHash = null;
  if (window.location.hash) {
    savedHash = window.location.hash;
    window.history.replaceState(null, '', window.location.pathname + window.location.search);
    debugLog('temporarily removed hash to prevent auto-scroll');
  }

  // 🧭 Após tudo carregar, restaura hash e aplica destaque
  window.addEventListener('load', () => {
    if (savedHash) {
      window.history.replaceState(null, '', window.location.pathname + window.location.search + savedHash);
      setTimeout(() => tryHighlight(savedHash), 600); // espera o layout se estabilizar
    }
  });

  function tryHighlight(selector) {
    const el = document.querySelector(selector);
    if (!el) {
      debugLog('element not found for', selector);
      return;
    }

    // scroll suave e centralizado
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // animação
    el.classList.remove('highlight-post');
    void el.offsetWidth;
    el.classList.add('highlight-post');

    setTimeout(() => el.classList.remove('highlight-post'), 4000);
  }

  // Se Livewire atualizar a lista, tenta novamente
  document.addEventListener('livewire:load', () => {
    if (savedHash) setTimeout(() => tryHighlight(savedHash), 700);
    if (window.Livewire && Livewire.hook) {
      Livewire.hook('message.processed', () => {
        if (savedHash) tryHighlight(savedHash);
      });
    }
  });
})();
</script>
