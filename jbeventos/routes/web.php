<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoordinatorController;

//Rotas Públicas
Route::get('/', function () {
    return view('home');
});

//Rotas dos eventos
Route::resource('events', EventController::class);

//Rotas dos Cursos
Route::resource('courses', CourseController::class);

//Rotas dos Coordenadores
Route::resource('coordinators', CoordinatorController::class);



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
