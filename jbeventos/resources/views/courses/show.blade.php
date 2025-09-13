<x-app-layout>
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ activeTab: 'overview' }">
            <div class="bg-white shadow-lg rounded-3xl overflow-hidden border border-gray-200">

                {{-- Banner do curso --}}
                <div class="relative h-64 sm:h-80 bg-gray-200">
                    <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                        alt="Banner do Curso" class="object-cover w-full h-full">

                    @if(auth()->user()->user_type === 'admin')
                        <form method="POST" action="{{ route('courses.updateBanner', $course->id) }}" enctype="multipart/form-data"
                            class="absolute top-4 right-4 z-10">
                            @csrf
                            @method('PUT')
                            <label class="bg-white/80 backdrop-blur text-sm px-4 py-2 rounded-full shadow-md cursor-pointer hover:bg-white transition-colors">
                                Trocar Banner
                                <input type="file" name="course_banner" id="bannerUpload" class="hidden" onchange="this.form.submit()">
                            </label>
                        </form>
                    @endif
                </div>

                <div class="px-6 -mt-16 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    {{-- Ícone do curso + edição --}}
                    <div class="flex-shrink-0 relative">
                        <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('images/default-icon.png') }}"
                            alt="Ícone do Curso"
                            class="w-32 h-32 rounded-full border-4 border-white object-cover bg-gray-300 shadow-lg">

                        @if(auth()->user()->user_type === 'admin')
                            <form method="POST" action="{{ route('courses.updateIcon', $course->id) }}" enctype="multipart/form-data"
                                class="absolute bottom-0 right-0">
                                @csrf
                                @method('PUT')
                                <label class="bg-white/80 backdrop-blur text-xs px-2 py-1 rounded-full shadow-md cursor-pointer hover:bg-white transition-colors">
                                    Editar
                                    <input type="file" name="course_icon" id="iconUpload" class="hidden" onchange="this.form.submit()">
                                </label>
                            </form>
                        @endif
                    </div>

                    {{-- Info do curso + ações sociais --}}
                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-4 pt-4">
                        {{-- Nome e coordenador --}}
                        <div>
                            <h2 class="text-3xl font-bold text-stone-800">{{ $course->course_name }}</h2>
                            <p class="text-md text-gray-500 mt-1">
                                Coordenador:
                                @if($course->courseCoordinator?->userAccount)
                                    <a href="{{ route('profile.view', $course->courseCoordinator->userAccount->id) }}"
                                       class="text-blue-500 hover:underline font-semibold">
                                        {{ $course->courseCoordinator->userAccount->name }}
                                    </a>
                                @else
                                    Nenhum coordenador definido
                                @endif
                            </p>
                        </div>

                        {{-- Botão seguir / deixar de seguir --}}
                        @auth
                            @if(auth()->user()->followedCourses->contains($course->id))
                                <form action="{{ route('courses.unfollow', $course->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-6 py-2 rounded-full shadow-sm transition">
                                        ✔ Seguindo
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('courses.follow', $course->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-full shadow-sm transition">
                                        + Seguir
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Navegação por abas --}}
                <div class="mt-8 px-6 border-b border-gray-200">
                    <div class="flex gap-4 sm:gap-6">
                        <button @click="activeTab = 'overview'" :class="{'border-blue-500 text-blue-600 font-bold': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'overview'}"
                                class="py-4 px-1 border-b-2 transition-colors duration-200 text-lg">
                            Visão Geral
                        </button>
                        <button @click="activeTab = 'posts'" :class="{'border-blue-500 text-blue-600 font-bold': activeTab === 'posts', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'posts'}"
                                class="py-4 px-1 border-b-2 transition-colors duration-200 text-lg">
                            Posts
                        </button>
                        <button @click="activeTab = 'events'" :class="{'border-blue-500 text-blue-600 font-bold': activeTab === 'events', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'events'}"
                                class="py-4 px-1 border-b-2 transition-colors duration-200 text-lg">
                            Eventos
                        </button>
                    </div>
                </div>

                {{-- Conteúdo das abas --}}
                <div class="p-6">

                    {{-- Aba: Visão Geral --}}
                    <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="md:col-span-2 space-y-8">
                                {{-- Descrição do Curso --}}
                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                    <h3 class="text-xl font-bold text-stone-700 mb-3">Sobre o Curso</h3>
                                    <div class="text-gray-700"
                                        x-data="{ editing: false, description: @js(old('course_description', $course->course_description)), original: @js($course->course_description) }">
                                        <p x-show="!editing" @click="editing = {{ auth()->user()->user_type === 'admin' ? 'true' : 'false' }}" class="cursor-pointer whitespace-pre-line">
                                            <span x-text="description || 'Clique para adicionar uma descrição...'"></span>
                                        </p>
                                        @if(auth()->user()->user_type === 'admin')
                                            <form method="POST" action="{{ route('courses.updateDescription', $course->id) }}"
                                                x-show="editing" @click.away="editing = false" x-transition>
                                                @csrf
                                                @method('PUT')
                                                <textarea name="course_description" rows="4" x-model="description" class="w-full border rounded p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Digite a descrição do curso..."></textarea>
                                                <div class="mt-2 text-right">
                                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 text-sm rounded-full">
                                                        Salvar
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Posts Recentes (usar botão para ir para aba Posts) --}}
                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                    <h3 class="text-xl font-bold text-stone-700 mb-4">Últimas Atualizações</h3>
                                    <p class="text-gray-500 text-sm">Clique em "Posts" para ver todas as atualizações do curso.</p>
                                    <div class="mt-6 text-center">
                                        <button @click="activeTab = 'posts'" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full font-medium transition-colors text-sm">
                                            Ver Todos os Posts
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Coluna lateral (Estatísticas e Eventos) --}}
                            <div class="md:col-span-1 space-y-8">
                                {{-- Estatísticas --}}
                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                    <div class="flex items-center gap-4 mb-4">
                                        <span class="text-2xl font-bold text-stone-800">{{ $course->followers()->count() }}</span>
                                        <span class="text-gray-600">Seguidores</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-2xl font-bold text-stone-800">{{ $course->events->count() }}</span>
                                        <span class="text-gray-600">Eventos Criados</span>
                                    </div>
                                </div>

                                {{-- Eventos Recentes --}}
                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                    <h3 class="text-xl font-bold text-stone-700 mb-4">Eventos Recentes</h3>
                                    <div class="space-y-4">
                                        @forelse($course->events->sortByDesc('event_scheduled_at')->take(3) as $event)
                                            <a href="{{ route('events.show', $event->id) }}" class="block p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition">
                                                <p class="text-sm font-semibold text-stone-800 truncate">{{ $event->event_name }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Data: {{ $event->event_scheduled_at ? $event->event_scheduled_at->format('d/m/Y') : 'Não agendado' }}
                                                </p>
                                            </a>
                                        @empty
                                            <p class="text-gray-500 text-sm">Nenhum evento recente.</p>
                                        @endforelse
                                    </div>
                                    <div class="mt-6 text-center">
                                        <button @click="activeTab = 'events'" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full font-medium transition-colors text-sm">
                                            Ver Todos os Eventos
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Aba: Posts --}}
                    <div x-show="activeTab === 'posts'" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100">

                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                            <h3 class="text-xl font-bold text-stone-700 mb-4">Posts e Atualizações do Curso</h3>

                            {{-- Componente Livewire com wire:key para forçar renderização única --}}
                            @livewire('course-posts', ['course' => $course], key('course-posts-' . $course->id))
                        </div>
                    </div>


                    {{-- Aba: Eventos --}}
                    <div x-show="activeTab === 'events'" x-cloak x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100">
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                            <h3 class="text-xl font-bold text-stone-700 mb-4">Todos os Eventos do Curso</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @forelse($course->events as $event)
                                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-0 overflow-hidden border border-gray-200">
                                        <div class="h-36 bg-gray-200">
                                            <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('images/default-event.jpg') }}"
                                                alt="Imagem do evento" class="w-full h-full object-cover">
                                        </div>
                                        <div class="p-4">
                                            <h4 class="text-lg font-bold truncate text-stone-800">{{ $event->event_name }}</h4>
                                            <p class="text-sm text-gray-600 line-clamp-2 mt-1">{{ $event->event_description }}</p>
                                            @isset($event->event_scheduled_at)
                                                <p class="text-xs text-gray-500 mt-2">
                                                    Data: {{ $event->event_scheduled_at->format('d/m/Y') }}
                                                </p>
                                            @endisset
                                            <a href="{{ route('events.show', $event->id) }}"
                                               class="mt-3 inline-block text-blue-500 hover:underline font-medium text-sm">
                                                Ver detalhes →
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center py-10">
                                        <p class="text-gray-500 text-lg">Nenhum evento cadastrado para este curso.</p>
                                        @if($course->courseCoordinator && auth()->user()->id === $course->courseCoordinator->userAccount->id)
                                            <div class="mt-4">
                                                <a href="{{ route('events.create', $course->id) }}"
                                                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full font-medium transition-colors text-sm">
                                                   Criar Novo Evento
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Botão excluir (só admin) --}}
                @if(auth()->user()->user_type === 'admin')
                    <div class="px-6 py-4 text-right border-t border-gray-200">
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir este curso? Esta ação é irreversível.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-6 py-2 rounded-full hover:bg-red-700 transition">
                                Excluir Curso
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
