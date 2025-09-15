<x-app-layout>
    <div class="relative bg-white shadow-xl rounded-lg overflow-hidden max-w-4xl mx-auto my-8">

        {{-- Banner --}}
        <div class="relative h-56 bg-gradient-to-r from-gray-300 to-gray-400">
            <img src="{{ $user->user_banner_url }}" alt="Banner do Usuário"
                 class="object-cover w-full h-full">
            @if(auth()->id() === $user->id)
                <label for="bannerUpload"
                       class="absolute top-4 right-4 bg-white/70 backdrop-blur-sm text-sm px-4 py-2 rounded-full shadow-md cursor-pointer hover:bg-white transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-5.454 2.553a2 2 0 00-2.924 2.924l5.454 5.454a2 2 0 002.924-2.924l-5.454-5.454z"/>
                    </svg>
                    Trocar Banner
                </label>
                <form id="bannerForm" method="POST" action="{{ route('profile.updateBanner') }}"
                      enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="user_banner" id="bannerUpload" onchange="document.getElementById('bannerForm').submit()">
                </form>
            @endif
        </div>

        {{-- Avatar e Nome --}}
        <div class="px-6 -mt-16 flex items-end space-x-6 pb-6 border-b border-gray-200">
            <div class="relative w-32 h-32 rounded-full border-6 border-white bg-gray-300 shadow-lg">
                <img src="{{ $user->user_icon_url }}" alt="Avatar"
                     class="w-full h-full rounded-full object-cover">
                @if(auth()->id() === $user->id)
                    <label for="photoUpload"
                           class="absolute bottom-0 right-0 bg-white/70 backdrop-blur-sm text-sm p-2 rounded-full shadow-md cursor-pointer hover:bg-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-5.454 2.553a2 2 0 00-2.924 2.924l5.454 5.454a2 2 0 002.924-2.924l-5.454-5.454z"/>
                        </svg>
                    </label>
                    <form id="photoForm" method="POST" action="{{ route('profile.updatePhoto') }}"
                          enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" name="user_icon" id="photoUpload" onchange="document.getElementById('photoForm').submit()">
                    </form>
                @endif
            </div>

            <div class="flex-1 mt-6">
                <h2 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    @php
                        $userTypes = ['coordinator' => 'Coordenador', 'user' => 'Usuário', 'admin' => 'Administrador'];
                    @endphp
                    {{ $userTypes[$user->user_type] ?? ucfirst($user->user_type) }}
                </p>
            </div>
        </div>

        {{-- Seções do Perfil com Abas --}}
        <div class="px-6 py-4" x-data="{ activeTab: 'biography' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'biography'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'biography', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'biography'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                        Biografia
                    </button>
                    <button @click="activeTab = 'savedEvents'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'savedEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'savedEvents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                        Eventos Salvos
                    </button>
                    @if($user->user_type === 'coordinator')
                        <button @click="activeTab = 'createdEvents'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'createdEvents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'createdEvents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                            Meus Eventos
                        </button>
                    @endif
                </nav>
            </div>

            <div class="mt-4">
                {{-- Biografia --}}
                <div x-show="activeTab === 'biography'">
                    <div class="px-2 py-4" x-data="{ editing: false, bio: @js(old('bio', $user->bio)), original: @js($user->bio) }">
                        <h3 class="text-sm font-semibold mb-2">Biografia</h3>
                        <div x-show="!editing" @click="editing = true" class="cursor-pointer text-sm text-gray-700 min-h-[3rem] whitespace-pre-line p-2 border border-dashed rounded hover:bg-gray-50 transition-colors duration-200">
                            <span x-text="bio || 'Clique para adicionar uma biografia...' "></span>
                        </div>
                        <form method="POST" action="{{ route('profile.updateBio') }}" x-show="editing" @click.away="editing = false" x-transition>
                            @csrf
                            <textarea name="bio" rows="4" x-model="bio"
                                      class="w-full border rounded p-2 text-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500"
                                      placeholder="Escreva sua biografia aqui..."></textarea>
                            <div class="mt-2 text-right">
                                <button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm rounded-lg shadow-md transition-colors duration-200">
                                    Salvar Biografia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Eventos Salvos --}}
                <div x-show="activeTab === 'savedEvents'">
                    <h3 class="text-lg font-semibold mb-4">Seus Eventos Salvos</h3>
                    @if($savedEvents->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-sm">Você ainda não salvou nenhum evento.</p>
                            <a href="{{ route('events.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 transition-colors">
                                Explore eventos agora!
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($savedEvents as $event)
                                <a href="{{ route('events.show', $event) }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                                    <div class="h-32 bg-gray-200 flex items-center justify-center overflow-hidden">
                                        <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('default-event-image.jpg') }}"
                                             alt="{{ $event->event_name }}"
                                             class="object-cover w-full h-full">
                                    </div>
                                    <div class="p-4">
                                        <p class="font-bold text-gray-800">{{ $event->event_name }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</p>
                                        <div class="mt-3 text-right">
                                            {{-- Botão para "des-salvar" o evento --}}
                                            <form action="{{ route('events.unsave', $event) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                                    Remover dos Salvos
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Eventos Criados (apenas para coordenadores) --}}
                @if($user->user_type === 'coordinator')
                    <div x-show="activeTab === 'createdEvents'">
                        <h3 class="text-lg font-semibold mb-4">Eventos que você criou</h3>
                        @if($createdEvents->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-gray-500 text-sm">Você ainda não criou nenhum evento.</p>
                                <a href="{{ route('coordinator.events.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 transition-colors">
                                    Crie seu primeiro evento!
                                </a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($createdEvents as $event)
                                    <a href="{{ route('events.show', $event) }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                                        <div class="h-32 bg-gray-200 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $event->event_image ? asset('storage/' . $event->event_image) : asset('default-event-image.jpg') }}"
                                                 alt="{{ $event->event_name }}"
                                                 class="object-cover w-full h-full">
                                        </div>
                                        <div class="p-4">
                                            <p class="font-bold text-gray-800">{{ $event->event_name }}</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ $event->event_scheduled_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Informações adicionais --}}
        <div class="px-6 py-4 text-sm text-gray-600 border-t border-gray-200">
            <p class="mt-1"><strong>Membro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>
</x-app-layout>