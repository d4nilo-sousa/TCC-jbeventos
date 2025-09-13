<x-app-layout>
    {{-- Container principal --}}
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Cabeçalho do Curso --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden relative">

            {{-- Banner do curso --}}
            <div class="relative h-48 bg-gray-200">
                <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                     alt="Banner do Curso" class="object-cover w-full h-full">

                @if(auth()->user()->user_type === 'admin')
                    <form method="POST" action="{{ route('courses.updateBanner', $course->id) }}" enctype="multipart/form-data"
                          class="absolute top-3 right-3">
                        @csrf
                        @method('PUT')
                        <input type="file" name="course_banner" id="bannerUpload" class="hidden"
                               onchange="this.form.submit()">
                        <button type="button"
                                onclick="document.getElementById('bannerUpload').click()"
                                class="bg-white px-3 py-1 text-sm rounded shadow hover:bg-gray-100 transition">
                            Trocar Banner
                        </button>
                    </form>
                @endif
            </div>

            {{-- Info do curso --}}
            <div class="px-6 -mt-12 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">

                {{-- Ícone --}}
                <div class="flex-shrink-0 relative">
                    <img src="{{ $course->course_icon ? asset('storage/' . $course->course_icon) : asset('images/default-icon.png') }}"
                         alt="Ícone do Curso"
                         class="w-24 h-24 rounded-full border-4 border-white object-cover bg-gray-300">

                    @if(auth()->user()->user_type === 'admin')
                        <form method="POST" action="{{ route('courses.updateIcon', $course->id) }}" enctype="multipart/form-data"
                              class="absolute bottom-0 right-0">
                            @csrf
                            @method('PUT')
                            <input type="file" name="course_icon" id="iconUpload" class="hidden"
                                   onchange="this.form.submit()">
                            <button type="button"
                                    onclick="document.getElementById('iconUpload').click()"
                                    class="bg-white text-xs px-2 py-1 rounded shadow hover:bg-gray-100 transition">
                                Editar
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Nome, coordenador e seguir --}}
                <div class="flex-1 flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-stone-800">{{ $course->course_name }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Coordenador:
                            @if($course->courseCoordinator?->userAccount)
                                <a href="{{ route('profile.view', $course->courseCoordinator->userAccount->id) }}"
                                   class="text-blue-500 hover:underline">
                                    {{ $course->courseCoordinator->userAccount->name }}
                                </a>
                            @else
                                Nenhum coordenador definido
                            @endif
                        </p>
                    </div>

                    {{-- Botão seguir / deixar de seguir --}}
                    @auth
                        <div>
                            @if(auth()->user()->followedCourses->contains($course->id))
                                <form action="{{ route('courses.unfollow', $course->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-1.5 rounded-full shadow transition">
                                        ✔ Seguindo
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('courses.follow', $course->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-1.5 rounded-full shadow transition">
                                        + Seguir
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>

            {{-- Descrição --}}
            <div class="px-6 py-4 text-gray-700 border-t mt-4"
                 x-data="{ editing: false, description: @js(old('course_description', $course->course_description)), original: @js($course->course_description) }">
                <h3 class="text-sm font-semibold mb-1">Descrição do Curso</h3>
                <div x-show="!editing"
                     @click="editing = {{ auth()->user()->user_type === 'admin' ? 'true' : 'false' }}"
                     class="cursor-pointer text-sm text-gray-700 min-h-[3rem] whitespace-pre-line">
                    <span x-text="description || 'Clique para adicionar uma descrição...'"></span>
                </div>

                @if(auth()->user()->user_type === 'admin')
                    <form method="POST" action="{{ route('courses.updateDescription', $course->id) }}"
                          x-show="editing" @click.away="editing = false" x-transition class="mt-2">
                        @csrf
                        @method('PUT')
                        <textarea name="course_description" rows="4"
                                  x-model="description"
                                  class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400"
                                  placeholder="Digite a descrição do curso..."></textarea>
                        <div class="mt-2 text-right" x-show="description !== original">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 text-sm rounded shadow">
                                Salvar
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Abas --}}
        <div x-data="{ activeTab: 'overview' }" class="bg-white rounded-2xl shadow p-6 mt-6">
            <div class="flex gap-6 border-b border-gray-200 mb-6">
                <button :class="{'border-b-2 border-blue-600 font-semibold': activeTab === 'overview'}"
                        @click="activeTab = 'overview'"
                        class="pb-2 text-gray-600 hover:text-gray-800 transition">
                    Visão Geral
                </button>
                <button :class="{'border-b-2 border-blue-600 font-semibold': activeTab === 'posts'}"
                        @click="activeTab = 'posts'"
                        class="pb-2 text-gray-600 hover:text-gray-800 transition">
                    Posts
                </button>
                <button :class="{'border-b-2 border-blue-600 font-semibold': activeTab === 'events'}"
                        @click="activeTab = 'events'"
                        class="pb-2 text-gray-600 hover:text-gray-800 transition">
                    Eventos
                </button>
            </div>

            {{-- Conteúdo das abas --}}
            <div>
                {{-- Visão Geral: últimos 2 posts --}}
                <div x-show="activeTab === 'overview'" x-cloak class="space-y-6">
                    @livewire('course-posts', ['course' => $course, 'overview' => true], key('course-posts-overview-' . $course->id))
                </div>

                {{-- Posts completos --}}
                <div x-show="activeTab === 'posts'" x-cloak class="space-y-6">
                    @livewire('course-posts', ['course' => $course], key('course-posts-' . $course->id))
                </div>

                {{-- Eventos --}}
                <div x-show="activeTab === 'events'" x-cloak class="space-y-6">
                    @if($course->events->isNotEmpty())
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($course->events as $event)
                                <div class="bg-white rounded-xl shadow p-4 border border-gray-200 hover:shadow-md transition">
                                    <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('images/default-event.jpg') }}"
                                         class="w-full h-32 object-cover rounded-lg mb-3">
                                    <h4 class="font-bold text-stone-800 truncate">{{ $event->event_name }}</h4>
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $event->event_description }}</p>
                                    @isset($event->event_scheduled_at)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Data: {{ $event->event_scheduled_at->format('d/m/Y') }}
                                        </p>
                                    @endisset
                                    <a href="{{ route('events.show', $event->id) }}"
                                       class="mt-2 inline-block text-blue-500 hover:underline text-sm">Ver detalhes →</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Nenhum evento cadastrado para este curso.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
