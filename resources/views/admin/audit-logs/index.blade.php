@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
    <div class="row mb-4">
        <div class="col-md-6">
            <h3>Activity Log</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('audit-logs.export', request()->query()) }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">-- All Actions --</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ ($filters['action'] ?? '') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- All Statuses --</option>
                        <option value="success" {{ ($filters['status'] ?? '') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failure" {{ ($filters['status'] ?? '') == 'failure' ? 'selected' : '' }}>Failure</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary flex-grow-1">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="table-container">
        @if($auditLogs->count() > 0)
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditLogs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $log->user->name ?? 'Unknown' }}</strong>
                                <br>
                                <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <code>{{ $log->action }}</code>
                            </td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge bg-success">Success</span>
                                @else
                                    <span class="badge bg-danger">Failure</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                            <td>
                                <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted small">
                    Showing {{ $auditLogs->firstItem() }} to {{ $auditLogs->lastItem() }} of {{ $auditLogs->total() }} results
                </div>
                {{ $auditLogs->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No audit logs found matching your criteria.</p>
            </div>
        @endif
    </div>
@endsection
