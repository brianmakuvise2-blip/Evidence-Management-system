<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 20px; }
    .header { background: #1a1a2e; color: #fff; padding: 16px 20px; margin: -20px -20px 20px; }
    .header h1 { margin: 0 0 4px; font-size: 18px; letter-spacing: 1px; }
    .header p  { margin: 0; font-size: 10px; opacity: 0.8; }
    .evidence-info { background: #f0f4ff; border-left: 4px solid #1a1a2e; padding: 10px 14px; margin-bottom: 18px; border-radius: 0 6px 6px 0; }
    .evidence-info h2 { margin: 0 0 6px; font-size: 13px; }
    .evidence-info p  { margin: 2px 0; font-size: 9px; color: #444; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1a1a2e; color: #fff; }
    thead th { padding: 7px 8px; text-align: left; font-size: 9px; letter-spacing: 0.5px; text-transform: uppercase; }
    tbody tr:nth-child(even) { background: #f8f9ff; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 7px 8px; border-bottom: 1px solid #e8ecf0; vertical-align: top; }
    .transfer-num { font-weight: bold; color: #1a1a2e; text-align: center; }
    .ref { font-size: 8px; color: #888; font-family: monospace; }
    .footer { margin-top: 20px; font-size: 8px; color: #888; border-top: 1px solid #ddd; padding-top: 8px; text-align: center; }
    .chain-arrow { color: #999; text-align: center; font-size: 12px; }
</style>
</head>
<body>
<div class="header">
    <h1>Chain of Custody Report</h1>
    <p>Evidence Management System &mdash; Generated {{ now()->format('d F Y, H:i:s') }}</p>
</div>

<div class="evidence-info">
    <h2>Exhibit {{ $evidence->exhibit_number }} &mdash; {{ $evidence->title }}</h2>
    <p><strong>Case Reference:</strong> {{ $evidence->case_reference ?? 'N/A' }}</p>
    <p><strong>Evidence Type:</strong> {{ ucfirst($evidence->evidence_type ?? 'N/A') }}</p>
    <p><strong>Current Status:</strong> {{ ucfirst($evidence->status) }}</p>
    <p><strong>Institution:</strong> {{ $evidence->institution->name ?? 'N/A' }}</p>
</div>

<table>
    <thead>
        <tr>
            <th style="width:4%">#</th>
            <th style="width:13%">Date &amp; Time</th>
            <th style="width:18%">From Institution</th>
            <th style="width:15%">From Officer</th>
            <th style="width:18%">To Institution</th>
            <th style="width:15%">To Officer</th>
            <th style="width:17%">Reason / Reference</th>
        </tr>
    </thead>
    <tbody>
        @forelse($custodyHistory as $index => $record)
        <tr>
            <td class="transfer-num">{{ $index + 1 }}</td>
            <td>{{ $record->transferred_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
            <td>{{ $record->fromInstitution?->name ?? 'Unknown' }}</td>
            <td>{{ $record->fromUser?->name ?? 'Unknown' }}</td>
            <td>{{ $record->toInstitution?->name ?? 'Unknown' }}</td>
            <td>{{ $record->toUser?->name ?? 'Unknown' }}</td>
            <td>
                {{ $record->transfer_reason ?? 'N/A' }}
                @if($record->transfer_reference)
                    <br><span class="ref">{{ $record->transfer_reference }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:20px;color:#888;">No custody transfers recorded for this evidence.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Evidence Management System &bull; Confidential &bull; Exhibit {{ $evidence->exhibit_number }} &bull; Page {PAGENO} of {nbpg}
</div>
</body>
</html>
