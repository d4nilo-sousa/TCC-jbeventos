<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;


//rotas públicas
Route::get('/', function () {
    return view('home');
});


//Rotas dos eventos
Route::get('/events', [EventController::class,'index'])->name('events.index'); //exibe a lista de eventos
Route::get('/events/{$id}/', [EventController::class,'show'])->name('events.show');
Route::get('/events/new', [EventController::class,'create'])->name('events.create'); //exibe o formulário de eventos
Route::post('/events', [EventController::class, 'store'])->name('events.store'); //Enviar o formulário para salvar no banco
Route::get('/eventos/{id}/edit', [EventController::class, 'edit'])->name('events.edit'); //editar evento   
Route::put('/eventos/{id}', [EventController::class, 'update'])->name('events.update'); //editar evento
Route::delete('/eventos/{id}', [EventoController::class, 'destroy']); //excluir evento


//rota dos cursos
Route::get('/courses',[CourseController::class,'index'])->name('courses.index'); //exibe os cursos cadastrados
Route::get('/courses/new',[CourseController::class,'create'])->name('courses.create'); //exibe o form de cadastro de curso 


