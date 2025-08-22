<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
    if ((int) $user->id === (int) $userId1 || (int) $user->id === (int) $userId2) {
        return ['id' => $user->id, 'name' => $user->name, 'user_icon' => $user->user_icon];
    }
});