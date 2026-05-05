<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #1a1a2e; color: #fff; padding: 16px 20px; margin: -20px -20px 20px; }
    .header h1 { margin: 0 0 4px; font-size: 18px; letter-spacing: 1px; }
    .header p { margin: 0; font-size: 10px; opacity: 0.8; }
    .meta { display: flex; gap: 30px; margin-bottom: 16px; font-size: 9px; color: #555; }
    .meta span { background: #f0f4ff; padding: 4px 10px; border-radius: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    thead tr { background: #1a1a2e; color: #fff; }
    thead th { padding: 7px 8px; text-align: left; font-size: 9px; letter-spacing: 0.5px; text-transform: uppercase; }
    tbody tr:nth-child(even) { background: #f8f9ff; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #e8ecf0; vertical-align: top; word-break: break-word; }
    .badge-success { color: #0a5c36; background: #d1fae5; padding: 2px 6px; border-radius: 10px; font-size: 8px; }
    .badge-failure { color: #7f1d1d; background: #fee2e2; padding: 2px 6px; border-radius: 10px; font-size: 8px; }
    .badge-info    { color: #1e3a5f; background: #dbeafe; padding: 2px 6px; border-radius: 10px; font-size: 8px; }
    .badge-warning { color: #7c4a00; background: #fef3c7; padding: 2px 6px; border-radius: 10px; font-size: 8px; }
    .footer { margin-top: 20px; font-size: 8px; color: #888; border-top: 1px solid #ddd; padding-top: 8px; text-align: center; }
</style>
</head>
<body>
<div class="header">
    <h1>Audit Logs Report</h1>
    <p>Evidence Management System &mdash; Generated {{ now()->format('d F Y, H:i:s') }}</p>
</div>

<div class="meta">
    <span>Total Records: {{ $auditLogs->count() }}</span>
    @if($filters['date_from'] ?? null)<span>From: {{ $filters['date_from'] }}</span>@endif
    @if($filters['date_to'] ?? null)<span>To: {{ $filters['date_to'] }}</span>@endif
    @if($filters['action'] ?? null)<span>Action: {{ $filters['action'] }}</span>@endif
    @if($filters['status'] ?? null)<span>Status: {{ ucfirst($filters['status']) }}</span>@endif
</div>

<table>
    <thead>
        <tr>
            <th style="width:14%">Timestamp</th>
            <th style="width:16%">User</th>
            <th style="width:18%">Action</th>
            <th style="width:10%">Status</th>
            <th style="width:13%">IP Address</th>
            <th style="width:29%">Details</th>
        </tr>
    </thead>
    <tbody>
        @forelse($auditLogs as $log)
        <tr>
            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            <td>{{ $log->user->name ?? 'Unknown' }}</td>
            <td>{{ str_replace('_', ' ', $log->action) }}</td>
            <td>
                @php $s = $log->status; @endphp
                <span class="badge-{{ $s === 'success' ? 'success' : ($s === 'failure' ? 'failure' : ($s === 'warning' ? 'warning' : 'info')) }}">
                    {{ ucfirst($s) }}
                </span>
            </td>
            <td>{{ $log->ip_address ?? '—' }}</td>
            <td>{{ is_array($log->details) ? implode(', ', array_map(fn($k,$v) => "$k: $v", array_keys($log->details), $log->details)) : $log->details }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center;padding:20px;color:#888;">No audit logs found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Evidence Management System &bull; Confidential &bull; Page {PAGENO} of {nbpg}
</div>
</body>
</html>
