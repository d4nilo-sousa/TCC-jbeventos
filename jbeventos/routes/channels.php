<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
    $ids = [(int)$userId1, (int)$userId2];
    sort($ids);

    return $user->id === $ids[0] || $user->id === $ids[1];
});
