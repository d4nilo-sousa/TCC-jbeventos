<x-app-layout>
 <h1>Chat com {{ $otherUser->name }}</h1>

    @livewire('chat', ['otherUser' => $otherUser])

</x-app-layout>