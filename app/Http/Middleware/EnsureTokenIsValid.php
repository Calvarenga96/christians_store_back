<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('token', $request->cookie('token'))->first();

        if (!$request->cookie('token')) {
            return response()->json('El token es requerido', 401);
        }

        if ($request->cookie('token') !== $user->token) {
            return response()->json('Token inválido', 401);
        }

        if ($user->token === null) {
            return response()->json('Debes registrarte primero', 401);
        }

        return $next($request);
    }
}
