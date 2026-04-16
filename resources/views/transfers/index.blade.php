@extends('layouts.admin')

@section('title', 'Transfer Requests')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">
                <i class="fas fa-exchange-alt"></i> Transfer Requests
            </h3>
        </div>
        <div class="col-md-4 text-end">
            @can('request-transfer')
                <a href="{{ route('transfers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Request Transfer
                </a>
            @endcan
        </div>
    </div>

    {{-- Status filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        @foreach(\App\Models\TransferRequest::getStatuses() as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Transfers table --}}
    @if($transfers->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Reference</th>
                            <th>Evidence</th>
                            <th>Requested By</th>
                            <th>Destination</th>
                            <th>Urgency</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr>
                                <td>
                                    <strong>{{ $transfer->transfer_reference }}</strong>
                                </td>
                                <td>
                                    @if($transfer->evidence)
                                        <a href="{{ route('evidence.show', $transfer->evidence) }}">
                                            {{ $transfer->evidence->exhibit_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $transfer->requestedBy?->name ?? 'Unknown' }}
                                    <br>
                                    <small class="text-muted">{{ $transfer->requestedBy?->institution->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    {{ $transfer->destinationInstitution?->name ?? 'Unknown' }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $transfer->urgency_level === 'critical' ? 'danger' : ($transfer->urgency_level === 'high' ? 'warning' : 'info') }}">
                                        {{ \App\Models\TransferRequest::getUrgencyLevels()[$transfer->urgency_level] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $transfer->getStatusBadgeClass() }}">
                                        {{ \App\Models\TransferRequest::getStatuses()[$transfer->status] }}
                                    </span>
                                </td>
                                <td>
                                    {{ $transfer->created_at->format('M d, Y H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('approve-transfer')
                                        @if($transfer->status === 'pending')
                                            <a href="{{ route('transfers.approval-form', $transfer) }}" class="btn btn-sm btn-success" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="{{ route('transfers.rejection-form', $transfer) }}" class="btn btn-sm btn-danger" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    @endcan
                                    @can('acknowledge-receipt')
                                        @if($transfer->status === 'in_transit' && $transfer->receiving_officer_id === Auth::id())
                                            <a href="{{ route('transfers.acknowledgment-form', $transfer) }}" class="btn btn-sm btn-warning" title="Acknowledge Receipt">
                                                <i class="fas fa-hand-paper"></i>
                                            </a>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $transfers->links() }}
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <h5>No Transfer Requests Found</h5>
            <p class="mb-0">No transfer requests match your current filters.</p>
        </div>
    @endif
</div>
@endsection
