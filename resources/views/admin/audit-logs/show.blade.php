@extends('layouts.admin')

@section('title', 'Audit Log Details')
@section('page-title', 'Audit Log Details')

@section('content')
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Logs
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Log Entry Details</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Timestamp</label>
                        <div class="fs-6">{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">User</label>
                        <div class="fs-6">
                            <strong>{{ $auditLog->user->name ?? 'Unknown' }}</strong>
                            <br>
                            <small class="text-muted">{{ $auditLog->user->email ?? '' }}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">IP Address</label>
                        <div class="fs-6">
                            <code>{{ $auditLog->ip_address }}</code>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Action</label>
                        <div class="fs-6">
                            <code>{{ $auditLog->action }}</code>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <div class="fs-6">
                            @if($auditLog->status === 'success')
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Failure</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">User Agent</label>
                        <div class="fs-6">
                            <small class="text-muted">{{ $auditLog->user_agent }}</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($auditLog->details)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="mb-3">Additional Details</h6>
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0"><code>{{ json_encode($auditLog->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
