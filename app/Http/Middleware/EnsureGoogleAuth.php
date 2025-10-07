<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGoogleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado via Google
        if (!$request->user() || !$request->user()->google_id) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Autenticação via Google necessária.'
            ], 403);
        }

        return $next($request);
    }
}
