<div>
    <div style="height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px;">
        @foreach($messages as $msg)
            <div><strong>{{ $msg['user'] }}</strong>: {{ $msg['message'] }}</div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" style="margin-top: 10px;">
        <input type="text" wire:model.defer="message" placeholder="Digite sua mensagem" style="width: 80%;" />
        <button type="submit">Enviar</button>
    </form>
</div>

<script>
    let userId = @json(auth()->id());
    let otherUserId = @json($otherUser->id);

    let ids = [userId, otherUserId].sort();

    window.Echo.private(`chat.${ids[0]}.${ids[1]}`)
        .listen('MessageSent', (e) => {
            Livewire.emit('messageReceived', {
                user: e.user.name,
                message: e.message
            });
        });
</script>
