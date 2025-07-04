<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoordinatorController;

// Redireciona para login ao acessar a raiz
Route::get('/', function () {
    return redirect()->route('login');
});

// Grupo de rotas protegidas por autenticação
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas dos eventos
    Route::resource('events', EventController::class);

    // Rotas dos cursos
    Route::resource('courses', CourseController::class);

    // Rotas dos coordenadores
    Route::resource('coordinators', CoordinatorController::class);
});
