<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !$request->user()->hasRole(...$roles)) {
            abort(403, 'Non hai i permessi per accedere a questa sezione.');
        }

        return $next($request);
    }
}
