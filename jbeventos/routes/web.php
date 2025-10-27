<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\CoordinatorPasswordController;
use App\Http\Controllers\CoordinatorDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseFollowController;
use App\Http\Controllers\EventReactionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ImageController;

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

    //rota do feed
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');


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
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        //nova rota para exportar pdf
        Route::post('/dashboard/export-pdf', [AdminDashboardController::class, 'exportPdf'])
            ->name('admin.dashboard.export.pdf');

        // CRUD de coordenadores
        Route::resource('coordinators', CoordinatorController::class)->except(['show']);

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
        Route::get('/dashboard', [CoordinatorDashboardController::class, 'index'])->name('coordinator.dashboard');

        // Nova rota para exportar o dashboard para PDF
        Route::post('/dashboard/export-pdf', [CoordinatorDashboardController::class, 'exportPdf'])
            ->name('coordinator.dashboard.export.pdf');

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
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Cursos e Eventos (Acesso Público para todos usuários autenticados)
    |--------------------------------------------------------------------------
    */
    Route::resource('courses', CourseController::class)->only(['index', 'show']);
    Route::get('events', [EventController::class, 'index'])->name('events.index');

    //Rota JSON para calendário de eventos
    Route::get('/events/calendar-feed', [EventController::class, 'calendarEvents'])->name('events.calendar-feed');

    // Show com middleware
    Route::get('events/{event}', [EventController::class, 'show'])
        ->middleware('checkEventVisibility')
        ->name('events.show');

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

    // Rotas para alterar a cor do banner
    Route::post('/perfil/update-banner-color', [ProfileController::class, 'updateBannerColor'])->name('profile.updateBannerColor');

    // Rotas para alterar a foto de perfil
    Route::post('/perfil/update-default-photo', [ProfileController::class, 'updateDefaultPhoto'])->name('profile.updateDefaultPhoto');

    //Rotas para adicionar e remover eventos na view de perfil
    Route::post('/perfil/events/{event}/save', [ProfileController::class, 'saveEvent'])->name('events.save');
    Route::delete('/perfil/events/{event}/unsave', [ProfileController::class, 'unsaveEvent'])->name('events.unsave');

    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show'); // Rota para exibir a tela de chat

    //Rota para a tela de exploração
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');


    /*
    |--------------------------------------------------------------------------
    | Rotas para seguir e deixar de seguir cursos
    |--------------------------------------------------------------------------
    */
    Route::post('/courses/{course}/follow', [CourseFollowController::class, 'follow'])->name('courses.follow');
    Route::delete('/courses/{course}/unfollow', [CourseFollowController::class, 'unfollow'])->name('courses.unfollow');

    Route::get('/courses/{course}/followers-count', [CourseFollowController::class, 'followersCount'])
        ->name('courses.followersCount');

    Route::delete('/events/{event_id}/cover', [ImageController::class, 'removeCoverImage'])
        ->name('events.remove_cover');

    Route::delete('/event-images/{id}', [ImageController::class, 'destroyEventImage'])
        ->name('event-images.destroy');

   // web.php:
// Certifique-se de que a rota de delete do curso está assim:
Route::delete('/courses/{id}/image/{type}', [App\Http\Controllers\ImageController::class, 'destroyCourseImage'])
->where('type', 'icon|banner')
->name('courses.destroy_image');

    // Para excluir imagem de evento
    Route::delete('/event-images/{id}', [ImageController::class, 'destroyEventImage']);
});
