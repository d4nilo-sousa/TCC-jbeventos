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

        // Verifica se o usuário está logado, tem função de coordenador e está com senha temporária
        if ($user && $user->coordinatorRole && $user->coordinatorRole->temporary_password) {
            // Permite acesso somente para as rotas de editar e atualizar a senha
            if ($request->routeIs('coordinator.password.edit') || $request->routeIs('coordinator.password.update')) {
                return $next($request);
            }

            // Caso o usuário tente acessar outra rota, redireciona para a página de editar senha
            return redirect()->route('coordinator.password.edit');
        }

        // Se não tiver senha temporária, deixa a requisição seguir normalmente
        return $next($request);
    }
}
