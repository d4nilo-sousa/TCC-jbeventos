<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CoordinatorPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseFollowController;
use App\Http\Controllers\EventReactionController;
use App\Http\Controllers\UserPhoneController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

// Ao acessar a raiz, redireciona para login
Route::get('/', fn() => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (usuário autenticado + sessão ativa + e-mail verificado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Redirecionamento para o dashboard correto baseado no tipo de usuário
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->user_type) {
            'admin' => redirect()->route('admin.dashboard'),
            'coordinator' => redirect()->route('coordinator.dashboard'),
            'user' => redirect()->route('user.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Painel do Administrador
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('checkUserType:admin')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

        // CRUD de coordenadores
        Route::resource('coordinators', CoordinatorController::class);

        // CRUD de cursos
        Route::resource('courses', CourseController::class);

        // ✅ Atualizações rápidas de cursos (somente admin)
        Route::put('/courses/{course}/update-banner', [CourseController::class, 'updateBanner'])->name('courses.updateBanner');
        Route::put('/courses/{course}/update-icon', [CourseController::class, 'updateIcon'])->name('courses.updateIcon');
        Route::put('/courses/{course}/update-description', [CourseController::class, 'updateDescription'])->name('courses.updateDescription');
    });

    /*
    |--------------------------------------------------------------------------
    | Painel do Coordenador
    |--------------------------------------------------------------------------
    */
    Route::prefix('coordinator')->middleware(['checkUserType:coordinator', 'forcePasswordChange:true'])->group(function () {
        Route::get('/dashboard', fn() => view('coordinator.dashboard'))->name('coordinator.dashboard');

        // CRUD de eventos
        Route::resource('events', EventController::class);

        // Alterar senha
        Route::get('password/edit', [CoordinatorPasswordController::class, 'edit'])->name('coordinator.password.edit');
        Route::put('password', [CoordinatorPasswordController::class, 'update'])->name('coordinator.password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Painel do Usuário Comum
    |--------------------------------------------------------------------------
    */
    Route::prefix('user')->middleware('checkUserType:user')->group(function () {
        Route::get('/dashboard', fn() => view('user.dashboard'))->name('user.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Cursos e Eventos (Acesso Público para todos usuários autenticados)
    |--------------------------------------------------------------------------
    */
    Route::resource('courses', CourseController::class)->only(['index', 'show']);
    Route::resource('events', EventController::class)->only(['index', 'show']);

    // ✅ Nova rota para o painel de Configurações (aproveitando os forms do Jetstream)
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    // Rotas do Perfil personalizado (foto, banner, bio)
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/perfil/{user}', [ProfileController::class, 'viewPublicProfile'])->name('profile.view');

    Route::post('/perfil/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::post('/perfil/update-banner', [ProfileController::class, 'updateBanner'])->name('profile.updateBanner');
    Route::post('/perfil/update-bio', [ProfileController::class, 'updateBio'])->name('profile.updateBio');

    // Rotas para a reação de usuários ao evento
    Route::post('/events/{event}/react', [EventReactionController::class, 'react'])->name('events.react');

    // Rotas para o usuário inserir o seu telefone(caso não tenha), para conseguir liberar a funcionalidade de notificação.
    Route::get('phone/edit', [UserPhoneController::class, 'edit'])->name('user.phone.edit');
    Route::put('phone', [UserPhoneController::class, 'update'])->name('user.phone.update');
});

/*
|--------------------------------------------------------------------------
| Rotas para seguir e deixar de seguir cursos
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/follow', [CourseFollowController::class, 'follow'])->name('courses.follow');
    Route::delete('/courses/{course}/unfollow', [CourseFollowController::class, 'unfollow'])->name('courses.unfollow');
});