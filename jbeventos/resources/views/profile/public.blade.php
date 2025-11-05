<x-app-layout>
    {{-- Container Principal --}}
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-16 space-y-6">
            @php
                $fromTab = request('fromTab'); // vai receber 'coordinators' se veio da aba coordenadores
            @endphp

            @if ($fromTab === 'coordinators')
                <a href="{{ route('explore.index', ['tab' => 'coordinators']) }}"
                    class="text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 font-medium text-base mb-2">
                    <i class="ph-fill ph-arrow-left text-xl"></i> Voltar a Aba de Coordenadores
                </a>
            @else
                <a href="{{ route('explore.index') }}"
                    class="text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 font-medium text-base mb-2">
                    <i class="ph-fill ph-arrow-left text-xl"></i> Voltar ao Explorar
                </a>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ activeTab: 'biography' }">
                {{-- Coluna PRINCIPAL (Conteúdo das Abas e Formulários) --}}
                <div class="lg:col-span-2 bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-100">

                    {{-- BANNER e AVATAR --}}
                    <div class="relative">
                        {{-- Banner --}}
                        @php
                            // Verifica se o banner é uma cor HEX válida
                            $isColor = preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner ?? '');
                            // Verifica se há uma imagem válida no storage
                            $hasImage =
                                !$isColor &&
                                !empty($user->user_banner) &&
                                Storage::disk('public')->exists($user->user_banner);
                            // Cor de fundo: HEX do usuário ou cinza padrão
                            $bgColor = $isColor ? $user->user_banner : '#e5e7eb'; // equivalente a bg-gray-200
                            // Background-image se houver imagem
                            $bgImage = $hasImage ? "url('" . Storage::url($user->user_banner) . "')" : 'none';
                        @endphp

                        <div class="relative h-56 rounded-lg overflow-hidden"
                            style="
                background-color: {{ $bgColor }};
                background-image: {{ $bgImage }};
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
             ">

                            {{-- Imagem apenas para acessibilidade --}}
                            @if ($hasImage)
                                <img src="{{ Storage::url($user->user_banner) }}" alt="Banner do Usuário"
                                    class="sr-only">
                            @endif

                        </div>

                        {{-- Avatar, Nome e Tipo do Usuário --}}
                        <div class="px-6 -mt-6 flex items-end space-x-6 pb-6 border-b border-gray-200 relative z-10">
                            {{-- Bloco do Avatar --}}
                            <div class="flex flex-col items-center">
                                <div
                                    class="relative w-36 h-36 rounded-full border-6 border-white shadow-xl 
    {{ in_array($user->user_icon_default, ['avatar_default_1.svg', 'avatar_default_2.svg']) ? 'bg-gray-200' : 'bg-white' }}">
                                    <img src="{{ $user->user_icon_url }}" alt="Avatar"
                                        class="w-full h-full rounded-full object-cover">
                                </div>
                            </div>

                            {{-- Nome e Tipo do Usuário --}}
                            <div class="flex-1 pb-2">
                                <h2 class="text-4xl font-extrabold text-gray-900">{{ $user->name }}</h2>
                                <div class="mt-1 flex items-center justify-between">
                                    <div class="mt-1">
                                        @php
                                            $userTypeData = [
                                                'coordinator' => [
                                                    'label' => 'Coordenador',
                                                    'color' => 'bg-red-500',
                                                    'icon' => 'ph-chalkboard-teacher',
                                                ],
                                                'user' => [
                                                    'label' => 'Usuário Comum',
                                                    'color' => 'bg-gray-500',
                                                    'icon' => 'ph-user',
                                                ],
                                                'admin' => [
                                                    'label' => 'Administrador',
                                                    'color' => 'bg-red-500',
                                                    'icon' => 'ph-crown',
                                                ],
                                            ];
                                            $type = $userTypeData[$user->user_type] ?? [
                                                'label' => ucfirst($user->user_type),
                                                'color' => 'bg-red-500',
                                                'icon' => 'ph-person',
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white {{ $type['color'] }}">
                                            <i class="ph {{ $type['icon'] }} text-sm mr-1"></i>
                                            {{ $type['label'] }}
                                        </span>
                                    </div>

                                    {{-- Botão de Conversar (apenas se for outro usuário) --}}
                                    @if (auth()->check() && auth()->id() !== $user->id)
                                        <a href="{{ route('chat.show', ['user' => $user->id]) }}"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md
                                                hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75 transition">
                                            <i class="ph ph-chat-circle text-lg mr-2"></i>
                                            Conversar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Seções do Perfil com Abas (Biografia e Eventos) --}}
                    <div class="px-6 py-4">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                {{-- Aba Biografia --}}
                                <button @click="activeTab = 'biography'"
                                    :class="{ 'border-red-500 text-red-600': activeTab === 'biography', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'biography' }"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                    <i class="ph ph-notepad text-lg mr-2"></i>
                                    Biografia
                                </button>
                                {{-- Aba Eventos Administrados --}}
                                @if ($user->user_type === 'coordinator')
                                    <button @click="activeTab = 'createdEvents'"
                                        :class="{ 'border-red-500 text-red-600': activeTab === 'createdEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'createdEvents' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                        <i class="ph ph-calendar-plus text-lg mr-2"></i>
                                        Eventos Administrados ({{ $eventsCreated->count() }})
                                    </button>
                                @endif

                                <button @click="activeTab = 'participatedEvents'"
                                    :class="{ 'border-red-500 text-red-600': activeTab === 'participatedEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'participatedEvents' }"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                    <i class="ph ph-thumbs-up text-lg mr-2"></i>
                                    Eventos Interagidos ({{ $participatedEvents->count() }})
                                </button>
                            </nav>
                        </div>

                        <div class="mt-8">
                            {{-- CONTEÚDO DA ABA: Biografia (apenas leitura) --}}
                            <div x-show="activeTab === 'biography'">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center pb-2">
                                    <i class="ph ph-info text-xl mr-2"></i> Sobre o {{ $user->name }}
                                </h3>

                                <div
                                    class="cursor-default text-sm leading-none text-gray-700 whitespace-normal break-words 
            py-4 px-6 rounded bg-gray-50 hover:bg-gray-100
            transition-colors duration-200 border border-transparent hover:border-red-200 shadow-sm 
            flex items-center justify-center text-center">
                                    <span>
                                        {{ $user->bio ?: 'Este usuário ainda não escreveu uma biografia.' }}
                                    </span>
                                </div>






                            </div>

                            {{-- CONTEÚDO DA ABA: Eventos Criados (se for coordenador) --}}
                            @if ($user->user_type === 'coordinator')
                                <div x-show="activeTab === 'createdEvents'">
                                    <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center pb-2">
                                        <i class="ph ph-rocket-launch text-xl mr-2 text-red-500"></i> Eventos
                                        Administrados
                                        Pelo {{ $user->name }}
                                    </h3>
                                    @if ($eventsCreated->isEmpty())
                                        <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                                            <i class="ph ph-package text-4xl text-gray-400"></i>
                                            <p class="text-gray-500 text-sm mt-2">{{ $user->name }} não
                                                administra
                                                nenhum evento</p>
                                        </div>
                                    @else
                                        <div
                                            class="max-h-[800px] overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                                            @foreach ($eventsCreated as $event)
                                                <div
                                                    class="bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden relative">

                                                    {{-- Link do evento --}}
                                                    <a href="{{ route('events.show', $event) }}" class="block">

                                                        {{-- Imagem / Placeholder --}}
                                                        <div
                                                            class="h-40 bg-gray-200 flex items-center justify-center overflow-hidden">
                                                            @if ($event->event_image)
                                                                <img src="{{ asset('storage/' . $event->event_image) }}"
                                                                    alt="{{ $event->event_name }}"
                                                                    class="w-full h-full object-cover">
                                                            @else
                                                                <div
                                                                    class="flex flex-col items-center justify-center w-full h-full text-red-500">
                                                                    <i class="ph-bold ph-calendar-blank text-6xl"></i>
                                                                    <p class="mt-2 text-sm">Sem Imagem de Capa</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Nome do evento --}}
                                                        <div class="px-6 pt-6 pb-0">
                                                            <p
                                                                class="font-bold text-gray-900 line-clamp-2 break-words mb-0">
                                                                {{ $event->event_name }}
                                                            </p>
                                                        </div>

                                                        {{-- Linha divisória + Data e hora --}}
                                                        <div class="px-6 pb-6 mt-0.5">
                                                            @if ($event->event_scheduled_at)
                                                                <p
                                                                    class="flex items-center gap-1 text-gray-500 mt-2 text-sm">
                                                                    <i
                                                                        class="ph-fill ph-clock-clockwise text-red-600 text-base"></i>
                                                                    {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>

                                    @endif
                                </div>
                            @endif

                            {{-- CONTEÚDO DA ABA: Eventos Interagidos --}}
                            <div x-show="activeTab === 'participatedEvents'">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center pb-2">
                                    <i class="ph ph-activity text-xl mr-2 text-yellow-500"></i> Eventos Interagidos
                                    Pelo {{ $user->name }}
                                </h3>
                                @if ($participatedEvents->isEmpty())
                                    <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                                        <i class="ph ph-smiley-sad text-4xl text-gray-400"></i>
                                        <p class="text-gray-500 text-sm mt-2">{{ $user->name }} não interagiu em
                                            nenhum evento</p>
                                    </div>
                                @else
                                    <div class="max-h-[800px] overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach ($participatedEvents as $event)
                                            <div
                                                class="bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden relative">

                                                {{-- Link do evento --}}
                                                <a href="{{ route('events.show', $event) }}" class="block">

                                                    {{-- Imagem / Placeholder --}}
                                                    <div
                                                        class="h-40 bg-gray-200 flex items-center justify-center overflow-hidden">
                                                        @if ($event->event_image)
                                                            <img src="{{ asset('storage/' . $event->event_image) }}"
                                                                alt="{{ $event->event_name }}"
                                                                class="w-full h-full object-cover">
                                                        @else
                                                            <div
                                                                class="flex flex-col items-center justify-center w-full h-full text-red-500">
                                                                <i class="ph-bold ph-calendar-blank text-6xl"></i>
                                                                <p class="mt-2 text-sm">Sem Imagem de Capa</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Nome do evento --}}
                                                    <div class="px-6 pt-6 pb-0">
                                                        <p
                                                            class="font-bold text-gray-900 line-clamp-2 break-words mb-0">
                                                            {{ $event->event_name }}
                                                        </p>
                                                    </div>

                                                    {{-- Linha divisória + Data e hora --}}
                                                    <div class="px-6 pb-6 mt-0.5">
                                                        @if ($event->event_scheduled_at)
                                                            <p
                                                                class="flex items-center gap-1 text-gray-500 mt-2 text-sm">
                                                                <i
                                                                    class="ph-fill ph-clock-clockwise text-red-600 text-base"></i>
                                                                {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('D [de] MMMM [de] YYYY, [às] HH:mm') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>

                                @endif
                            </div>

                        </div>
                    </div>

                </div>

                {{-- Coluna SECUNDÁRIA (Sidebar com Detalhes Estáticos) --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Card de Informações Básicas (Públicas) --}}
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                        <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                            <i class="ph ph-identification-card text-2xl mr-2 text-red-500"></i> Informações
                            Públicas
                        </h3>

                        <div class="space-y-4 text-sm text-gray-700">

                            {{-- Tipo de Usuário --}}
                            <div class="flex items-center">
                                <i class="ph ph-user-circle text-lg w-5 text-red-500 mr-3"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Nível</p>
                                    @php
                                        // Reutiliza o array de dados de tipo de usuário
                                        $type = $userTypeData[$user->user_type] ?? [
                                            'label' => ucfirst($user->user_type),
                                            'color' => 'bg-red-500',
                                            'icon' => 'ph-person',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold text-white {{ $type['color'] }}">
                                        {{ $type['label'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- CAMPO: Curso Coordenado, Coordenador Geral ou Administrador --}}
                            @if ($user->user_type === 'admin')
                                <div class="flex items-start">
                                    <i class="ph ph-shield-star text-lg w-5 text-red-500 mr-3 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Administrador do Sistema</p>
                                        <p class="text-sm font-medium text-gray-700">Responsável pelo Gerenciamento
                                            do
                                            Sistema</p>
                                    </div>
                                </div>
                            @elseif ($coordinator)
                                <div class="flex items-start">
                                    <i class="ph ph-chalkboard-teacher text-lg w-5 text-red-500 mr-3 mt-1"></i>
                                    <div class="flex-1">
                                        @if ($coordinator->coordinator_type === 'general')
                                            <p class="font-semibold text-gray-900">Coordenador Geral</p>
                                            <p class="text-sm font-medium text-gray-700">Responsável pelos Eventos
                                                Gerais</p>
                                        @elseif($coordinator->coordinator_type === 'course')
                                            <p class="font-semibold text-gray-900">Coordenador de Curso</p>
                                            <p class="text-sm font-medium text-gray-700">
                                                {{ $coordinator->coordinatedCourse?->course_name
                                                    ? 'Responsável pelo curso: ' . $coordinator->coordinatedCourse->course_name
                                                    : 'Não é Responsável por Nenhum Curso' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Membro Desde (Movido para a Sidebar) --}}
                            <div class="flex items-center">
                                <i class="ph ph-calendar-check text-lg w-5 text-red-500 mr-3"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Membro desde</p>
                                    <p>{{ $user->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Remoção dos blocos de Ações Rápidas, Logout e link para Editar Perfil (pois é uma view pública) --}}

                </div>

            </div>
        </div>
</x-app-layout>
