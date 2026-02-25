<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Registra un'azione nel log attività.
     * Centralizza user_id, ip_address e user_agent che sono sempre identici.
     */
    protected function logActivity(
        Request $request,
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $properties = null,
    ): void {
        ActivityLog::create([
            'user_id'     => $request->user()->id,
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'properties'  => $properties,
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 500),
            'created_at'  => now(),
        ]);
    }
}
