<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\DeleteExpiredEvents;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Define os arquivos de rotas utilizados pela aplicação
        web: __DIR__.'/../routes/web.php',           // Rotas web (interface do usuário)
        api: __DIR__.'/../routes/api.php',           // Rotas da API
        commands: __DIR__.'/../routes/console.php',  // Comandos Artisan personalizados
        health: '/up',                               // Rota de verificação de integridade (health check)
    )

    // Registra middlewares personalizados
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'checkUserType' => \App\Http\Middleware\CheckUserType::class, // Middleware para verificar o tipo de usuário
            'forcePasswordChange' => \App\Http\Middleware\ForcePasswordChange::class, // Middleware para forçar troca de senha
            'checkEventVisibility' => \App\Http\Middleware\CheckEventVisibility::class, // Middleware que controla o acesso a eventos de acordo com sua visibilidade e permissões do usuário
        ]);
    })

    // Registra comandos personalizados
    ->withCommands([
        DeleteExpiredEvents::class, // Comando Artisan para deletar eventos expirados
    ])

    // Agendamento de comandos
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Executa o comando todo segundo
        $schedule->command('app:delete-expired-events')->everySecond();
    })

    // Configuração de tratamento de exceções
    ->withExceptions(function (Exceptions $exceptions) {
        // Pode ser usado para registrar handlers personalizados
    })

    ->create(); // Finaliza a criação da aplicação
