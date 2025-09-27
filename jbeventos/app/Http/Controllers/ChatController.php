<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ChatController extends Controller
{
    public function show(User $user){
        return view('chat.show', ['otherUser' => $user]);
    }
}
