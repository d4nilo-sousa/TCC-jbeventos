<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEventVisibility
{
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        if (!$event) {
            abort(404, 'Evento não encontrado');
        }

        return $next($request);
    }
}
