<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->input('user_id'), fn ($q, $v) => $q->where('user_id', $v))
            ->when($request->input('action'), fn ($q, $v) => $q->where('action', $v))
            ->when($request->input('date_from'), fn ($q, $v) => $q->where('created_at', '>=', $v))
            ->when($request->input('date_to'), fn ($q, $v) => $q->where('created_at', '<=', $v . ' 23:59:59'))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')->get();

        return view('activity-log.index', compact('logs', 'users'));
    }
}
