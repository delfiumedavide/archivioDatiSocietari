<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            ActivityLog::create([
                'user_id' => $request->user()?->id,
                'action' => $this->getAction($request),
                'description' => $request->method() . ' ' . $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'created_at' => now(),
            ]);
        }

        return $response;
    }

    private function getAction(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'viewed',
        };
    }
}
