<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/events',[EventController::class,'index']);
Route::get('/events/new',[EventController::class,'create']);