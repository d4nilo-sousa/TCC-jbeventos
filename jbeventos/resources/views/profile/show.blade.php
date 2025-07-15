<x-app-layout>
    <div class="relative bg-white shadow rounded-lg overflow-hidden">

        {{-- Banner --}}
        <div class="relative h-48 bg-gray-200">
            <img src="{{ $user->user_banner_url }}" alt="Banner" class="object-cover w-full h-full">
            <form method="POST" action="{{ route('profile.updateBanner') }}" enctype="multipart/form-data" class="absolute top-2 right-2">
                @csrf
                <input type="file" name="user_banner" id="bannerUpload" class="hidden" onchange="this.form.submit()">
                <button type="button" onclick="document.getElementById('bannerUpload').click()" class="bg-white px-3 py-1 text-sm rounded shadow">
                    Trocar Banner
                </button>
            </form>
        </div>

        {{-- Avatar e Nome --}}
        <div class="px-6 -mt-12 flex items-end space-x-4">
            <div class="relative">
                <img src="{{ $user->user_icon_url }}" alt="Avatar" class="w-24 h-24 rounded-full border-4 border-white object-cover bg-gray-300">
                <form method="POST" action="{{ route('profile.updatePhoto') }}" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                    @csrf
                    <input type="file" name="user_icon" id="photoUpload" class="hidden" onchange="this.form.submit()">
                    <button type="button" onclick="document.getElementById('photoUpload').click()" class="bg-white text-xs px-2 py-1 rounded shadow">
                        Editar
                    </button>
                </form>
            </div>
            <div>
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ ucfirst($user->user_type) }}</p>
            </div>
        </div>

       {{-- Biografia --}}
<div class="px-6 py-4" x-data="{ editing: false, bio: @js(old('bio', $user->bio)), original: @js($user->bio) }">
    <h3 class="text-sm font-semibold mb-1">Biografia</h3>

    {{-- Modo de exibição --}}
    <div x-show="!editing" @click="editing = true" class="cursor-pointer text-sm text-gray-700 min-h-[3rem] whitespace-pre-line">
        <span x-text="bio || 'Clique para adicionar uma biografia...'"></span>
    </div>

    {{-- Formulário de edição --}}
    <form method="POST" action="{{ route('profile.updateBio') }}" x-show="editing" @click.away="editing = false" x-transition>
        @csrf
        <textarea name="bio" rows="4"
            x-model="bio"
            class="w-full border rounded p-2 text-sm"
            placeholder="Digite sua biografia..."></textarea>

        <div class="mt-2 text-right" x-show="bio !== original">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 text-sm rounded">
                Salvar
            </button>
        </div>
    </form>
</div>


        {{-- Informações adicionais --}}
        <div class="px-6 py-2 text-sm text-gray-600 border-t">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Telefone:</strong> {{ $user->phone_number ?? 'Não informado' }}</p>
            <p><strong>Criado em:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>
</x-app-layout>
