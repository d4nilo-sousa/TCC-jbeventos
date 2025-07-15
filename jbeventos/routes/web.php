<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CoordinatorPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventReactionController;

// Ao acessar a raiz do site, redireciona para a rota de login
Route::get('/', function () {
    return redirect()->route('login');
});

// Grupo de rotas protegidas por autenticação, sessão ativa e e-mail verificado
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // Redireciona o usuário autenticado para o dashboard conforme seu tipo de usuário
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->user_type) {
            'admin' => redirect()->route('admin.dashboard'),
            'coordinator' => redirect()->route('coordinator.dashboard'),
            'user' => redirect()->route('user.dashboard'),
            default => abort(403), // Se o tipo de usuário não for válido, retorna erro 403
        };
    })->name('dashboard');

    // Rotas para o painel do Administrador
    Route::prefix('admin')->middleware('checkUserType:admin')->group(function () {
        // Exibe o dashboard do administrador
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // CRUD completo para gerenciar coordenadores (só admin pode acessar)
        Route::resource('coordinators', CoordinatorController::class);

        // CRUD completo para gerenciar cursos (só admin pode acessar)
        Route::resource('courses', CourseController::class);
    });

    // Rotas para o painel do Coordenador
    Route::prefix('coordinator')->middleware('checkUserType:coordinator', 'forcePasswordChange:true')->group(function () {
        // Dashboard do coordenador
        Route::get('/dashboard', function () {
            return view('coordinator.dashboard');
        })->name('coordinator.dashboard');

        // CRUD completo para gerenciar eventos
        Route::resource('events', EventController::class);

        // Rotas para editar e atualizar a senha do coordenador
        Route::get('password/edit', [CoordinatorPasswordController::class, 'edit'])->name('coordinator.password.edit');
        Route::put('password', [CoordinatorPasswordController::class, 'update'])->name('coordinator.password.update');
    });

    // Rotas para o painel do Usuário comum
    Route::prefix('user')->middleware('checkUserType:user')->group(function () {
        // Exibe o dashboard do usuário comum
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        })->name('user.dashboard');
    });

    // Rotas públicas para cursos (somente listagem e detalhes)
    Route::resource('courses', CourseController::class)->only(['index', 'show']);

    // Rotas públicas para eventos (somente listagem e detalhes)
    Route::resource('events', EventController::class)->only(['index', 'show']);

    // ✅ Nova rota para o painel de Configurações (aproveitando os forms do Jetstream)
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    // Rotas do Perfil personalizado (foto, banner, bio)
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/perfil/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::post('/perfil/update-banner', [ProfileController::class, 'updateBanner'])->name('profile.updateBanner');
    Route::post('/perfil/update-bio', [ProfileController::class, 'updateBio'])->name('profile.updateBio');

    Route::post('/events/{event}/react', [EventReactionController::class, 'react'])->name('events.react');
});
