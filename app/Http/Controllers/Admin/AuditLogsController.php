<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        // Apply filters
        $query = UserActivityLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get paginated results
        $auditLogs = $query->latest()->paginate(50);

        // Get list of actions for filter dropdown
        $actions = UserActivityLog::distinct()
            ->pluck('action')
            ->sort();

        return view('admin.audit-logs.index', [
            'auditLogs' => $auditLogs,
            'actions' => $actions,
            'filters' => $request->only(['user_id', 'action', 'status', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Display the specified audit log.
     */
    public function show(UserActivityLog $auditLog)
    {
        $auditLog->load('user');

        return view('admin.audit-logs.show', [
            'auditLog' => $auditLog,
        ]);
    }

    /**
     * Export audit logs to CSV.
     */
    public function export(Request $request)
    {
        $query = UserActivityLog::with('user');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

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

        $auditLogs = $query->latest()->get();

        // Create CSV
        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
        );

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Timestamp', 'User', 'Action', 'Status', 'IP Address', 'Details']);

        foreach ($auditLogs as $log) {
            fputcsv($handle, [
                $log->created_at,
                $log->user->name ?? 'Unknown',
                $log->action,
                $log->status,
                $log->ip_address,
                json_encode($log->details),
            ]);
        }

        fclose($handle);

        return response()->stream(
            function () use ($handle) {
                // Stream is already written
            },
            200,
            $headers
        );
    }
}
