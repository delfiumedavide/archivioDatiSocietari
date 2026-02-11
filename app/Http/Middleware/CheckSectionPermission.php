<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionPermission
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        if (!$request->user() || !$request->user()->hasSection($section)) {
            abort(403, 'Non hai i permessi per accedere a questa sezione.');
        }

        return $next($request);
    }
}
