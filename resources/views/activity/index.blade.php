@extends('layouts.admin')

@section('title', 'My Activity')
@section('page-title', 'My Activity')

@section('content')

{{-- Stats --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #1e3a5f, #2b4c7c);">
                <i class="bi bi-activity"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <div class="stat-label">Total Actions</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['success']) }}</div>
            <div class="stat-label">Successful</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['failure']) }}</div>
            <div class="stat-label">Failed</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                <i class="bi bi-calendar-day"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['today']) }}</div>
            <div class="stat-label">Today</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('my-activity.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-medium">Action</label>
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ ($filters['action'] ?? '') === $action ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="success" {{ ($filters['status'] ?? '') === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failure" {{ ($filters['status'] ?? '') === 'failure' ? 'selected' : '' }}>Failure</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('my-activity.index') }}" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Log table --}}
<div class="table-container">
    @if($logs->count() > 0)
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    @php
                        $details  = is_array($log->details) ? $log->details : [];
                        $desc     = $details['description'] ?? '';
                        $isOk     = $log->status === 'success';
                    @endphp
                    <tr>
                        <td class="text-nowrap">
                            <span class="fw-medium">{{ $log->created_at->format('d M Y') }}</span><br>
                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill"
                                  style="background: #e0e7ff; color: #3730a3; font-weight: 500; font-size: 0.78rem;">
                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td>
                            @if($desc)
                                {{ $desc }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($isOk)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle me-1"></i>Success
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle me-1"></i>Failed
                                </span>
                            @endif
                        </td>
                        <td>
                            <code class="text-muted" style="font-size: 0.8rem;">{{ $log->ip_address ?? '—' }}</code>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center px-3 pb-3">
            <small class="text-muted">
                Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} entries
            </small>
            {{ $logs->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-clock-history" style="font-size: 3rem; color: #94a3b8;"></i>
            <p class="mt-3 text-muted fw-medium">No activity recorded yet</p>
            <p class="text-muted" style="font-size: 0.875rem;">
                Your actions in the system — uploading evidence, transfers, logins — will appear here.
            </p>
        </div>
    @endif
</div>

@endsection
