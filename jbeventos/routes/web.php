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
Route::get('/events/new', [eventController::class,'create'])->name('events.create'); //exibe o formulário de eventos
Route::post('/events', [eventController::class, 'store'])->name('events.store'); //Enviar o formulário para salvar no banco
Route::get('/eventos/{id}/edit', [eventController::class, 'edit'])->name('events.edit'); //editar evento   
Route::put('/eventos/{id}', [eventController::class, 'update'])->name('events.update'); //editar evento
Route::delete('/eventos/{id}', [EventoController::class, 'destroy']); //excluir evento


//rota dos cursos
Route::get('/courses',[CourseController::class,'index'])->name('courses.index'); //exibe os cursos cadastrados
Route::get('/courses/new',[CourseController::class,'create'])->name('courses.create'); //exibe o form de cadastro de curso 



//Rotas do admin
Route::get('/coordinators',[AdminController::class,'index'])->name('admin.index'); //exibe os cursos cadastrados