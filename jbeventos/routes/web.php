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
Route::resource('events', EventController::class);


//rota dos cursos
Route::get('/courses',[CourseController::class,'index'])->name('courses.index'); //exibe os cursos cadastrados
Route::get('/courses/new',[CourseController::class,'create'])->name('courses.create'); //exibe o form de cadastro de curso 


