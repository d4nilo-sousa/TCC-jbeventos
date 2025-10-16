<x-app-layout>
    {{-- Container Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ activeTab: 'biography' }">
            
            {{-- Coluna PRINCIPAL (Conteúdo das Abas e Formulários) --}}
            <div class="lg:col-span-2 bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-100">

                {{-- BANNER e AVATAR --}}
                <div class="relative">
                    {{-- Banner --}}
                    <div class="relative h-56 bg-gray-200"
                        style="{{ preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner_url) ? 'background-color: ' . $user->user_banner_url : '' }}">

                        @if(!preg_match('/^#[a-f0-9]{6}$/i', $user->user_banner_url))
                            <img src="{{ $user->user_banner_url }}" alt="Banner do Usuário" class="object-cover w-full h-full">
                        @endif
                    </div>

                    {{-- Avatar, Nome e Tipo do Usuário --}}
                    <div class="px-6 -mt-16 flex items-end space-x-6 pb-6 border-b border-gray-200 relative z-10">
                        {{-- Bloco do Avatar --}}
                        <div class="flex flex-col items-center">
                            <div class="relative w-36 h-36 rounded-full border-6 border-white bg-gray-300 shadow-xl">
                                <img src="{{ $user->user_icon_url }}" alt="Avatar"
                                    class="w-full h-full rounded-full object-cover">
                            </div>
                        </div>

                        {{-- Nome e Tipo do Usuário --}}
                        <div class="flex-1  mt-16 pb-2">
                            <h2 class="text-4xl font-extrabold text-gray-900">{{ $user->name }}</h2>
                            <div class="mt-1 flex items-center justify-between">
                                <div>
                                    @php
                                        $userTypeData = [
                                            'coordinator' => ['label' => 'Coordenador', 'color' => 'bg-red-500', 'icon' => 'ph-chalkboard-teacher'],
                                            'user' => ['label' => 'Usuário Comum', 'color' => 'bg-gray-500', 'icon' => 'ph-user'],
                                            'admin' => ['label' => 'Administrador', 'color' => 'bg-red-500', 'icon' => 'ph-crown'],
                                        ];
                                        $type = $userTypeData[$user->user_type] ?? ['label' => ucfirst($user->user_type), 'color' => 'bg-red-500', 'icon' => 'ph-person'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white {{ $type['color'] }}">
                                        <i class="ph {{ $type['icon'] }} text-sm mr-1"></i>
                                        {{ $type['label'] }}
                                    </span>
                                </div>

                                {{-- Botão de Conversar (apenas se for outro usuário) --}}
                                @if(auth()->check() && auth()->id() !== $user->id)
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
                            <button @click="activeTab = 'biography'" :class="{'border-red-500 text-red-600': activeTab === 'biography', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'biography'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                <i class="ph ph-notepad text-lg mr-2"></i>
                                Biografia
                            </button>
                            {{-- Aba Eventos Criados --}}
                            @if($user->user_type === 'coordinator' && $eventsCreated->isNotEmpty())
                                <button @click="activeTab = 'createdEvents'" :class="{'border-red-500 text-red-600': activeTab === 'createdEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'createdEvents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                    <i class="ph ph-calendar-plus text-lg mr-2"></i>
                                    Eventos Criados ({{ $eventsCreated->count() }})
                                </button>
                            @endif
                            {{-- Aba Eventos Participados --}}
                            @if($participatedEvents->isNotEmpty())
                                <button @click="activeTab = 'participatedEvents'" :class="{'border-red-500 text-red-600': activeTab === 'participatedEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'participatedEvents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center">
                                    <i class="ph ph-thumbs-up text-lg mr-2"></i>
                                    Eventos que Participou ({{ $participatedEvents->count() }})
                                </button>
                            @endif
                        </nav>
                    </div>

                    <div class="mt-8">
                        {{-- CONTEÚDO DA ABA: Biografia (apenas leitura) --}}
                        <div x-show="activeTab === 'biography'">
                            <h3 class="text-base font-bold mb-3 flex items-center text-gray-800">
                                <i class="ph ph-info text-xl mr-2"></i> Sobre {{ $user->name }}
                            </h3>
                            <div class="text-sm text-gray-700 min-h-[5rem] whitespace-pre-line bg-gray-50 p-4 rounded-lg border shadow-sm">
                                {{ $user->bio ?? 'Este usuário ainda não escreveu uma biografia.' }}
                            </div>
                        </div>

                        {{-- CONTEÚDO DA ABA: Eventos Criados (se for coordenador) --}}
                        @if($user->user_type === 'coordinator')
                            <div x-show="activeTab === 'createdEvents'">
                                <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                                    <i class="ph ph-rocket-launch text-xl mr-2 text-red-500"></i> Eventos publicados por {{ $user->name }}
                                </h3>
                                @if($eventsCreated->isEmpty())
                                    <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                                        <i class="ph ph-package text-4xl text-gray-400"></i>
                                        <p class="text-gray-500 text-sm mt-2">{{ $user->name }} ainda não criou nenhum evento.</p>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($eventsCreated as $event)
                                            <a href="{{ route('events.show', $event) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                                                <div class="h-36 bg-gray-200 flex items-center justify-center overflow-hidden">
                                                    <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('default-event-image.jpg') }}"
                                                            alt="{{ $event->event_name }}"
                                                            class="object-cover w-full h-full">
                                                </div>
                                                <div class="p-4">
                                                    <p class="font-bold text-gray-900 line-clamp-2">{{ $event->event_name }}</p>
                                                    <p class="text-xs text-red-600 mt-2 flex items-center">
                                                        <i class="ph ph-calendar text-sm mr-1"></i>
                                                        {{ $event->event_scheduled_at->format('d/m/Y') }} às {{ $event->event_scheduled_at->format('H:i') }}
                                                    </p>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        {{-- CONTEÚDO DA ABA: Eventos Interagidos --}}
                        <div x-show="activeTab === 'participatedEvents'">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                                <i class="ph ph-activity text-xl mr-2 text-yellow-500"></i> Eventos que {{ $user->name }} Interagiu
                            </h3>
                            @if($participatedEvents->isEmpty())
                                <div class="text-center py-10 border border-dashed rounded-lg bg-gray-50">
                                    <i class="ph ph-smiley-sad text-4xl text-gray-400"></i>
                                    <p class="text-gray-500 text-sm mt-2">{{ $user->name }} não interagiu em nenhum evento público.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> {{-- Reduzido para 2 colunas para caber em lg:col-span-2 --}}
                                    @foreach($participatedEvents as $event)
                                        <a href="{{ route('events.show', $event) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                                            <div class="h-36 bg-gray-200 flex items-center justify-center overflow-hidden">
                                                <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('default-event-image.jpg') }}"
                                                        alt="{{ $event->event_name }}"
                                                        class="object-cover w-full h-full">
                                            </div>
                                            <div class="p-4">
                                                <p class="font-bold text-gray-900 line-clamp-2">{{ $event->event_name }}</p>
                                                <p class="text-xs text-red-600 mt-2 flex items-center">
                                                    <i class="ph ph-calendar text-sm mr-1"></i>
                                                    {{ $event->event_scheduled_at->format('d/m/Y') }} às {{ $event->event_scheduled_at->format('H:i') }}
                                                </p>
                                            </div>
                                        </a>
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
                        <i class="ph ph-identification-card text-2xl mr-2 text-red-500"></i> Informações Públicas
                    </h3>

                    <div class="space-y-4 text-sm text-gray-700">
                        
                        {{-- Tipo de Usuário --}}
                        <div class="flex items-center">
                            <i class="ph ph-user-circle text-lg w-5 text-red-500 mr-3"></i>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">Nível</p>
                                @php
                                    // Reutiliza o array de dados de tipo de usuário
                                    $type = $userTypeData[$user->user_type] ?? ['label' => ucfirst($user->user_type), 'color' => 'bg-red-500', 'icon' => 'ph-person'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold text-white {{ $type['color'] }}">
                                    {{ $type['label'] }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Campo: Curso Coordenado (Apenas se for Coordenador e houver um curso) --}}
                        @if($user->user_type === 'coordinator' && $user->coordinated_course_name)
                            <div class="flex items-start">
                                <i class="ph ph-chalkboard-teacher text-lg w-5 text-red-500 mr-3 mt-1"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Coordenador do Curso</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $user->coordinated_course_name }}</p>
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