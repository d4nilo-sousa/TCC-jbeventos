<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoordinatorController;

// Redireciona para a rota de login ao acessar a raiz do site
Route::get('/', function () {
    return redirect()->route('login');
});

// Grupo de rotas protegidas por autenticação e verificação
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // Redireciona o usuário autenticado para o dashboard correspondente ao seu tipo de conta
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->user_type) {
            'admin' => redirect()->route('admin.dashboard'),
            'coordinator' => redirect()->route('coordinator.dashboard'),
            'user' => redirect()->route('user.dashboard'),
            default => abort(403), // Retorna erro 403 se o tipo de usuário for inválido
        };
    })->name('dashboard');

    // Rotas exclusivas do painel do Administrador
    Route::prefix('admin')->middleware('checkUserType:admin')->group(function () {

        // Exibe a view do dashboard do administrador
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // CRUD dos Coordenadores (somente para admin)
        Route::resource('coordinators', CoordinatorController::class);

        // CRUD dos Cursos (somente para admin)
        Route::resource('courses', CourseController::class);
    });

    // Rotas exclusivas do painel do Coordenador
    Route::prefix('coordinator')->middleware('checkUserType:coordinator')->group(function () {

        // Exibe a view do dashboard do coordenador
        Route::get('/dashboard', function () {
            return view('coordinator.dashboard');
        })->name('coordinator.dashboard');

        // CRUD dos Eventos (somente para coordenador)
        Route::resource('events', EventController::class);
    });

    // Rotas exclusivas do painel do Usuário comum
    Route::prefix('user')->middleware('checkUserType:user')->group(function () {

        // Exibe a view do dashboard do usuário
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        })->name('user.dashboard');
    });

    // Rotas públicas para cursos: apenas exibição de lista e detalhes
    Route::resource('courses', CourseController::class)->only(['index', 'show']);

    // Rotas públicas para eventos: apenas exibição de lista e detalhes
    Route::resource('events', EventController::class)->only(['index', 'show']);
});
