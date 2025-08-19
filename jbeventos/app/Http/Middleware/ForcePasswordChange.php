<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) 
    {
        $user = auth()->user();

        // Se o usuário está logado e possui função de coordenador
        if ($user && $user->coordinatorRole) {
            $hasTemporaryPassword = $user->coordinatorRole->temporary_password;

            // Se o coordenador tem senha temporária
            if ($hasTemporaryPassword) {
                // Permite acessar apenas as rotas de alteração de senha
                if ($request->routeIs('coordinator.password.edit') || $request->routeIs('coordinator.password.update')) {
                    return $next($request);
                }

                // Caso acesse qualquer outra rota, redireciona para a tela de alteração de senha
                return redirect()->route('coordinator.password.edit');
            }

            // Se não tem senha temporária, mas tenta acessar a tela de alteração de senha, redireciona para o dashboard
            if (!$hasTemporaryPassword && ($request->routeIs('coordinator.password.edit') || $request->routeIs('coordinator.password.update'))) {
                return redirect()->route('coordinator.dashboard');
            }
        }

        // Caso não se enquadre nas condições acima, continua o fluxo normalmente
        return $next($request);
    }
}
