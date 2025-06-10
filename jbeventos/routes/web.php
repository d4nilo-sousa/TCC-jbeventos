<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\eventController;

//rotas públicas
Route::get('/', function () {
    return view('home');
});


//Rotas dos eventos
Route::get('/events', [eventController::class,'index'])->name('events.index'); //exibe a lista de eventos
Route::get('/events/{$id}/', [eventController::class,'show'])->name('events.show');




