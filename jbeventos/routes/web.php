<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\eventController;

Route::get('/', function () {
    return view('home');
});


//Rotas dos eventos
Route::get('/events', [eventController::class,'index'])->name('events.index');
Route::get('/events/new', [eventController::class,'create']);

