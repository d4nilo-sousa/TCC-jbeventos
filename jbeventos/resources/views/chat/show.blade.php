<x-app-layout>
    <div class="relative">
        {{-- Chat --}}
        @livewire('chat', ['otherUser' => $otherUser])
    </div>
</x-app-layout>
