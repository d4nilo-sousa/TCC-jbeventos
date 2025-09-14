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
                        <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}" alt="Banner do Curso" class="object-cover w-full h-full">

                        {{-- Botão para Trocar o Banner --}}
                        @if(auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateBanner', $course->id) }}" enctype="multipart/form-data"
                                class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_banner" id="bannerUpload" class="hidden"
                                       onchange="this.form.submit()">
                                <button type="button"
                                        onclick="document.getElementById('bannerUpload').click()"
                                        class="bg-white px-3 py-1 text-sm rounded-full shadow-md hover:bg-gray-100 transition flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.9a2 2 0 001.664-1.11l.888-1.776A2 2 0 0110.112 3h3.776a2 2 0 011.664 1.11l.888 1.776A2 2 0 0018.1 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Trocar Banner
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="relative">
                        <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('images/default-icon.png') }}" alt="Ícone do Curso" class="w-24 h-24 rounded-full border-4 border-white absolute -bottom-10 left-4 object-cover">

                        {{-- Botão para Trocar o Ícone (sempre visível para admin) --}}
                        @if(auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateIcon', $course->id) }}" enctype="multipart/form-data"
                                  class="absolute -bottom-10 left-20 transition-opacity duration-300 opacity-100">
                                @csrf
                                @method('PUT')
                                <input type="file" name="course_icon" id="iconUpload" class="hidden"
                                       onchange="this.form.submit()">
                                <button type="button"
                                        onclick="document.getElementById('iconUpload').click()"
                                        class="bg-white text-xs px-2 py-1 rounded-full shadow-md hover:bg-gray-100 transition flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
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
                            <div>
                                @if(auth()->user()->followedCourses->contains($course->id))
                                    <form action="{{ route('courses.unfollow', $course->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-1.5 rounded-full shadow transition">
                                            ✔ Seguindo
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('courses.follow', $course->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-1.5 rounded-full shadow transition">
                                            + Seguir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endauth
                    </div>

                    {{-- Contagem de Membros --}}
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="font-semibold">{{ $course->followers()->count() }}</span>
                        {{ Str::plural('membro', $course->followers()->count()) }}
                    </p>

                    <p class="text-sm text-gray-500 mt-1">
                        <strong class="font-semibold">Coordenador:</strong>
                        @if($course->courseCoordinator?->userAccount)
                            <a href="{{ route('profile.view', $course->courseCoordinator->userAccount->id) }}" class="text-blue-500 hover:underline">
                                {{ $course->courseCoordinator->userAccount->name }}
                            </a>
                        @else
                            Nenhum coordenador definido
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Coluna da Direita (Tabs de Conteúdo) --}}
        <div class="lg:w-2/3">
            <div x-data="{ tab: 'posts' }" class="bg-white rounded-2xl shadow-lg p-6">

                {{-- Navegação por abas --}}
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="tab = 'events'" :class="{ 'border-blue-500 text-blue-600 font-semibold': tab === 'events', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'events' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Eventos
                        </button>
                        <button @click="tab = 'posts'" :class="{ 'border-blue-500 text-blue-600 font-semibold': tab === 'posts', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'posts' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                    @if(auth()->user()->user_type === 'coordinator')
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('events.create', ['course_id' => $course->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                                + Criar Evento
                            </a>
                        </div>
                    @endif

                    {{-- Lista de Eventos --}}
                    @if($course->events->isNotEmpty())
                        <div class="space-y-6">
                            @foreach($course->events->sortByDesc('event_scheduled_at') as $event)
                                <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="bg-blue-100 p-3 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-lg font-bold text-stone-800">{{ $event->event_name }}</h4>
                                            @if ($event->event_scheduled_at)
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ $event->event_scheduled_at->format('d/m/Y') }} às {{ $event->event_scheduled_at->format('H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $event->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    @if($event->event_cover)
                                        <div class="rounded-lg overflow-hidden mb-4">
                                            <img src="{{ asset('storage/' . $event->event_cover) }}" alt="Capa do Evento" class="object-cover w-full h-auto max-h-80">
                                        </div>
                                    @endif

                                    <p class="text-gray-600 text-sm mb-4 whitespace-pre-line">{{ $event->event_description }}</p>

                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        {{-- Link para visualizar o Evento --}}
                                        <a href="{{ route('events.show', $event->id) }}" class="flex items-center gap-1 hover:text-blue-500 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Ver Detalhes</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center text-sm">Nenhum evento foi criado para este curso ainda.</p>
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