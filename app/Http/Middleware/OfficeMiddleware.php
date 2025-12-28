<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OfficeMiddleware
{
    /**
     * Verifica que el usuario pertenezca a al menos una oficina
     * El control fino por documento se hace en Policies
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // No autenticado (auth debe ejecutarse antes)
        if (! $user) {
            abort(401, 'No autenticado');
        }

        // Superadmin ignora contexto de oficina
        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        // Debe pertenecer a alguna oficina
        if (! $user->offices()->exists()) {
            abort(403, 'No pertenece a ninguna oficina');
        }

        return $next($request);
    }
}
