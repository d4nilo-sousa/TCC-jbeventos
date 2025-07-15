<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $type): Response
    {
        // Pega o usuário autenticado no sistema
        $user = auth()->user();

        // Se não houver usuário autenticado ou o tipo do usuário for diferente do esperado,
        // aborta a requisição com erro 403 (proibido) e mensagem "Acesso não autorizado"
        if (!$user || $user->user_type !== $type) {
            abort(403, 'Acesso não autorizado');
        }

        // Se passou na verificação, segue com a requisição para o próximo middleware ou controlador
        return $next($request);
    }
}
