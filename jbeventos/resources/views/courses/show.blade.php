<x-app-layout>
    <div class="relative bg-white shadow rounded-lg overflow-hidden">

        {{-- Banner do curso --}}
        <div class="relative h-48 bg-gray-200">
            <img src="{{ $course->course_banner ? asset('storage/' . $course->course_banner) : asset('images/default-banner.jpg') }}"
                 alt="Banner do Curso" class="object-cover w-full h-full">

            @if(auth()->user()->user_type === 'admin')
                <form method="POST" action="{{ route('courses.updateBanner', $course->id) }}" enctype="multipart/form-data"
                      class="absolute top-2 right-2">
                    @csrf
                    @method('PUT')
                    <input type="file" name="course_banner" id="bannerUpload" class="hidden"
                           onchange="this.form.submit()">
                    <button type="button"
                            onclick="document.getElementById('bannerUpload').click()"
                            class="bg-white px-3 py-1 text-sm rounded shadow">
                        Trocar Banner
                    </button>
                </form>
            @endif
        </div>

        {{-- Ícone, nome do curso, coordenador, seguidores e botão de seguir --}}
        <div class="px-6 -mt-12 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">

            {{-- Ícone do curso + edição --}}
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
                                class="bg-white text-xs px-2 py-1 rounded shadow">
                            Editar
                        </button>
                    </form>
                @endif
            </div>

            {{-- Info do curso + ações sociais --}}
            <div class="flex-1 flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-4">

                {{-- Nome e coordenador --}}
                <div>
                    <h2 class="text-xl font-bold">{{ $course->course_name }}</h2>
                    <p class="text-sm text-gray-500">
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

                {{-- Área social: botão de seguir + dropdown de seguidores --}}
                <div class="flex items-center gap-4">

                    {{-- Dropdown de seguidores --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="text-sm text-gray-600 hover:underline focus:outline-none">
                            {{ $course->followers()->count() }} seguidores
                        </button>

                        <div x-show="open" @click.away="open = false"
                             class="absolute z-10 mt-2 w-64 bg-white border rounded shadow-md max-h-60 overflow-y-auto">
                            @forelse($course->followers as $follower)
                                <div class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50">
                                    <img src="{{ $follower->user_icon ? asset('storage/' . $follower->user_icon) : asset('images/default-icon.png') }}"
                                         class="w-8 h-8 rounded-full object-cover" alt="{{ $follower->name }}">
                                    <span class="text-sm text-gray-800">{{ $follower->name }}</span>
                                </div>
                            @empty
                                <p class="px-4 py-2 text-sm text-gray-500">Nenhum seguidor ainda.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Botão seguir / deixar de seguir --}}
                    @auth
                        @if(auth()->user()->followedCourses->contains($course->id))
                            <form action="{{ route('courses.unfollow', $course->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-1.5 rounded-full shadow-sm transition">
                                    ✔ Seguindo
                                </button>
                            </form>
                        @else
                            <form action="{{ route('courses.follow', $course->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-1.5 rounded-full shadow-sm transition">
                                    + Seguir
                                </button>
                            </form>
                        @endif
                    @endauth

                </div>
            </div>
        </div>

        {{-- Descrição (Edição inline igual à bio do usuário) --}}
        <div class="px-6 py-4 text-gray-700"
             x-data="{ editing: false, description: @js(old('course_description', $course->course_description)), original: @js($course->course_description) }">
            <h3 class="text-sm font-semibold mb-1">Descrição do Curso</h3>

            <div x-show="!editing"
                 @click="editing = {{ auth()->user()->user_type === 'admin' ? 'true' : 'false' }}"
                 class="cursor-pointer text-sm text-gray-700 min-h-[3rem] whitespace-pre-line">
                <span x-text="description || 'Clique para adicionar uma descrição...'"></span>
            </div>

            @if(auth()->user()->user_type === 'admin')
                <form method="POST" action="{{ route('courses.updateDescription', $course->id) }}"
                      x-show="editing" @click.away="editing = false" x-transition>
                    @csrf
                    @method('PUT')
                    <textarea name="course_description" rows="4"
                              x-model="description"
                              class="w-full border rounded p-2 text-sm"
                              placeholder="Digite a descrição do curso..."></textarea>
                    <div class="mt-2 text-right" x-show="description !== original">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 text-sm rounded">
                            Salvar
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Listagem de eventos do curso --}}
        <div class="px-6 py-6 border-t mt-4">
            <h3 class="text-lg font-semibold mb-4">Eventos deste curso</h3>

            @if($course->events->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($course->events as $event)
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition p-0 overflow-hidden">
                            {{-- Imagem do evento --}}
                            <div class="h-32 bg-gray-200">
                                <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('images/default-event.jpg') }}"
                                     alt="Imagem do evento"
                                     class="w-full h-full object-cover">
                            </div>

                            {{-- Conteúdo do card --}}
                            <div class="p-4">
                                <h4 class="text-md font-bold truncate">{{ $event->event_name }}</h4>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $event->event_description }}</p>

                                @isset($event->event_scheduled_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Data: {{ $event->event_scheduled_at->format('d/m/Y') }}
                                    </p>
                                @endisset

                                <a href="{{ route('events.show', $event->id) }}"
                                   class="mt-3 inline-block text-blue-500 hover:underline text-sm">
                                    Ver detalhes →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Nenhum evento cadastrado para este curso.</p>
            @endif
        </div>

        {{-- Botão excluir (só admin) --}}
        @if(auth()->user()->user_type === 'admin')
            <div class="px-6 py-4">
                <form action="{{ route('courses.destroy', $course->id) }}" method="POST"
                      onsubmit="return confirm('Tem certeza que deseja excluir este curso?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Excluir Curso
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
