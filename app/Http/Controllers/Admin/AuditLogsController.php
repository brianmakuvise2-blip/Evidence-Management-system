<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use App\Services\PdfService;
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
     * Export audit logs as PDF.
     */
    public function export(Request $request)
    {
        $query = UserActivityLog::with('user');

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
        $filters   = $request->only(['user_id', 'action', 'status', 'date_from', 'date_to']);
        $filename  = 'audit-logs-' . now()->format('Y-m-d-His') . '.pdf';

        $filterDesc = collect($filters)->filter()->map(fn($v, $k) => "$k: $v")->implode(' | ');
        $subtitle   = 'Generated ' . now()->format('d M Y, H:i') . ($filterDesc ? '  |  ' . $filterDesc : '');

        $columns = [
            ['label' => 'Timestamp',   'width' => 90],
            ['label' => 'User',        'width' => 85],
            ['label' => 'Action',      'width' => 95],
            ['label' => 'Status',      'width' => 55],
            ['label' => 'IP Address',  'width' => 72],
            ['label' => 'Details',     'width' => 133],
        ];

        $rows = $auditLogs->map(fn($log) => [
            $log->created_at->format('Y-m-d H:i:s'),
            $log->user->name ?? 'Unknown',
            str_replace('_', ' ', $log->action),
            ucfirst($log->status),
            $log->ip_address ?? '—',
            is_array($log->details)
                ? collect($log->details)->map(fn($v, $k) => "$k: $v")->implode(', ')
                : (string) $log->details,
        ])->toArray();

        $pdf = (new PdfService())->build('Audit Logs Report', $subtitle, $columns, $rows, landscape: true);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
