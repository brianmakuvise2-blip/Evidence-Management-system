<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #1a1a2e; color: #fff; padding: 16px 20px; margin: -20px -20px 20px; }
    .header h1 { margin: 0 0 4px; font-size: 18px; letter-spacing: 1px; }
    .header p  { margin: 0; font-size: 10px; opacity: 0.8; }
    h2 { font-size: 12px; margin: 20px 0 8px; padding-bottom: 4px; border-bottom: 2px solid #1a1a2e; color: #1a1a2e; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    thead tr { background: #1a1a2e; color: #fff; }
    thead th { padding: 7px 10px; text-align: left; font-size: 9px; letter-spacing: 0.5px; text-transform: uppercase; }
    tbody tr:nth-child(even) { background: #f8f9ff; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 7px 10px; border-bottom: 1px solid #e8ecf0; }
    .value-cell { font-weight: bold; text-align: right; }
    .footer { margin-top: 20px; font-size: 8px; color: #888; border-top: 1px solid #ddd; padding-top: 8px; text-align: center; }
    .badge { padding: 2px 7px; border-radius: 10px; font-size: 8px; }
    .badge-success { color: #0a5c36; background: #d1fae5; }
    .badge-danger  { color: #7f1d1d; background: #fee2e2; }
    .badge-info    { color: #1e3a5f; background: #dbeafe; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ $title }} Report</h1>
    <p>Evidence Management System &mdash; Generated {{ now()->format('d F Y, H:i:s') }}</p>
</div>

@if($reportType === 'overview')
    <h2>System Overview</h2>
    <table>
        <thead><tr><th>Metric</th><th>Value</th></tr></thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td>{{ $row[0] }}</td>
                <td class="value-cell">{{ $row[1] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

@elseif($reportType === 'activity')
    <h2>Recent Activity (Last 100 Records)</h2>
    <table>
        <thead>
            <tr>
                <th style="width:18%">Date &amp; Time</th>
                <th style="width:20%">User</th>
                <th style="width:20%">Action</th>
                <th style="width:30%">Description</th>
                <th style="width:12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row[0] }}</td>
                <td>{{ $row[1] }}</td>
                <td>{{ str_replace('_', ' ', $row[2]) }}</td>
                <td>{{ $row[3] }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($row[4]) === 'success' ? 'success' : (strtolower($row[4]) === 'failure' ? 'danger' : 'info') }}">
                        {{ $row[4] }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:16px;color:#888;">No activity recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

@else
    <h2>{{ $title }} — Status Breakdown</h2>
    <table>
        <thead><tr><th>Status</th><th style="text-align:right">Count</th></tr></thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row[0] }}</td>
                <td class="value-cell">{{ $row[1] }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align:center;padding:16px;color:#888;">No data available.</td></tr>
            @endforelse
        </tbody>
    </table>
@endif

<div class="footer">
    Evidence Management System &bull; Confidential &bull; Page {PAGENO} of {nbpg}
</div>
</body>
</html>
