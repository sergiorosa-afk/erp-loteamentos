<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permite acesso apenas a admins e apresentadores.
 * Bloqueia viewers genéricos da tela de apresentação.
 */
class EnsureApresentador
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isAdmin() && ! $user->isApresentador())) {
            abort(403, 'Acesso exclusivo para apresentadores.');
        }

        return $next($request);
    }
}
