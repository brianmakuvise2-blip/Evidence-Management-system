<?php

namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = UserActivityLog::where('user_id', Auth::id());

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(30);

        $actions = UserActivityLog::where('user_id', Auth::id())
            ->distinct()
            ->pluck('action')
            ->sort()
            ->values();

        $stats = [
            'total'   => UserActivityLog::where('user_id', Auth::id())->count(),
            'success' => UserActivityLog::where('user_id', Auth::id())->where('status', 'success')->count(),
            'failure' => UserActivityLog::where('user_id', Auth::id())->where('status', 'failure')->count(),
            'today'   => UserActivityLog::where('user_id', Auth::id())->whereDate('created_at', today())->count(),
        ];

        return view('activity.index', [
            'logs'    => $logs,
            'actions' => $actions,
            'stats'   => $stats,
            'filters' => $request->only(['action', 'status', 'date_from', 'date_to']),
        ]);
    }
}
