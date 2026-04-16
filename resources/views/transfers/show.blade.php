@extends('layouts.admin')

@section('title', 'Transfer Request Details')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3>
                <i class="fas fa-file-alt"></i> Transfer Request
                <span class="badge {{ $transfer->getStatusBadgeClass() }}">
                    {{ \App\Models\TransferRequest::getStatuses()[$transfer->status] }}
                </span>
            </h3>
        </div>
        <div class="col-md-4 text-end">
            <span class="d-inline-block">
                <strong>Reference:</strong> {{ $transfer->transfer_reference }}
            </span>
        </div>
    </div>

    {{-- Main Details --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Request Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Evidence:</strong><br>
                        <a href="{{ route('evidence.show', $transfer->evidence) }}">
                            {{ $transfer->evidence->exhibit_number }} - {{ $transfer->evidence->title }}
                        </a>
                    </div>
                    <div class="mb-3">
                        <strong>Requested By:</strong><br>
                        {{ $transfer->requestedBy->name }}<br>
                        <small class="text-muted">{{ $transfer->requestedBy->email }}</small>
                    </div>
                    <div class="mb-3">
                        <strong>Requested At:</strong><br>
                        {{ $transfer->requested_at->format('M d, Y H:i:s') }}
                    </div>
                    <div class="mb-3">
                        <strong>Reason:</strong><br>
                        {{ $transfer->transfer_reason }}
                    </div>
                    <div class="mb-0">
                        <strong>Urgency:</strong><br>
                        <span class="badge bg-{{ $transfer->urgency_level === 'critical' ? 'danger' : ($transfer->urgency_level === 'high' ? 'warning' : 'info') }}">
                            {{ \App\Models\TransferRequest::getUrgencyLevels()[$transfer->urgency_level] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-exchange-alt"></i> Transfer Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>From Institution:</strong><br>
                        {{ $transfer->requestedBy->institution->name }}
                    </div>
                    <div class="mb-3">
                        <strong>To Institution:</strong><br>
                        {{ $transfer->destinationInstitution->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Receiving Officer:</strong><br>
                        {{ $transfer->receivingOfficer->name ?? 'Not assigned' }}<br>
                        @if($transfer->receivingOfficer)
                            <small class="text-muted">{{ $transfer->receivingOfficer->email }}</small>
                        @endif
                    </div>
                    @if($transfer->status !== 'pending')
                        <div class="mb-3">
                            <strong>Approver:</strong><br>
                            {{ $transfer->supervisorApprover?->name ?? 'N/A' }}
                        </div>
                    @endif
                    @if($transfer->status === 'completed')
                        <div class="mb-0">
                            <strong>Completed At:</strong><br>
                            {{ $transfer->completed_at->format('M d, Y H:i:s') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Workflow Timeline --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Workflow Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ in_array($transfer->status, ['approved', 'in_transit', 'acknowledged', 'completed']) ? 'completed' : ($transfer->status === 'rejected' ? 'rejected' : 'active') }}">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6>Request Submitted</h6>
                                <p class="mb-0 text-muted">{{ $transfer->requested_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        </div>

                        <div class="timeline-item {{ in_array($transfer->status, ['approved', 'in_transit', 'acknowledged', 'completed']) ? 'completed' : ($transfer->status === 'rejected' ? 'rejected' : '') }}">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6>Supervisor Review</h6>
                                @if($transfer->status === 'rejected')
                                    <p class="text-danger mb-1">Rejected</p>
                                    <p class="mb-0 text-muted">{{ $transfer->rejected_at?->format('M d, Y H:i:s') ?? 'N/A' }}</p>
                                    @if($transfer->rejection_reason)
                                        <p class="mb-0"><strong>Reason:</strong> {{ $transfer->rejection_reason }}</p>
                                    @endif
                                @elseif(in_array($transfer->status, ['approved', 'in_transit', 'acknowledged', 'completed']))
                                    <p class="text-success mb-1">Approved</p>
                                    <p class="mb-0 text-muted">{{ $transfer->approved_at->format('M d, Y H:i:s') }}</p>
                                    @if($transfer->supervisorApprover)
                                        <p class="mb-0"><strong>By:</strong> {{ $transfer->supervisorApprover->name }}</p>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">Pending Review</p>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ in_array($transfer->status, ['in_transit', 'acknowledged', 'completed']) ? 'completed' : '' }}">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6>In Transit</h6>
                                @if(in_array($transfer->status, ['in_transit', 'acknowledged', 'completed']))
                                    <p class="mb-0 text-muted">{{ $transfer->in_transit_at->format('M d, Y H:i:s') }}</p>
                                @else
                                    <p class="text-muted mb-0">Awaiting Approval</p>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ in_array($transfer->status, ['acknowledged', 'completed']) ? 'completed' : '' }}">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6>Receipt Acknowledged</h6>
                                @if(in_array($transfer->status, ['acknowledged', 'completed']))
                                    <p class="mb-0 text-muted">{{ $transfer->acknowledged_at->format('M d, Y H:i:s') }}</p>
                                    @if($transfer->acknowledgedBy)
                                        <p class="mb-0"><strong>By:</strong> {{ $transfer->acknowledgedBy->name }}</p>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">Awaiting Receiver</p>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $transfer->status === 'completed' ? 'completed' : '' }}">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6>Transfer Complete</h6>
                                @if($transfer->status === 'completed')
                                    <p class="text-success mb-0">Completed</p>
                                    <p class="mb-0 text-muted">{{ $transfer->completed_at->format('M d, Y H:i:s') }}</p>
                                @else
                                    <p class="text-muted mb-0">In Progress</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @can('approve-transfer')
        @if($transfer->status === 'pending')
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <strong>Action Required:</strong> This transfer request requires supervisor approval.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('transfers.approval-form', $transfer) }}" class="btn btn-success">
                            <i class="fas fa-check"></i> Approve Transfer
                        </a>
                        <a href="{{ route('transfers.rejection-form', $transfer) }}" class="btn btn-danger">
                            <i class="fas fa-times"></i> Reject Transfer
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @can('acknowledge-receipt')
        @if($transfer->status === 'in_transit' && $transfer->receiving_officer_id === Auth::id())
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <strong>Action Required:</strong> Please acknowledge receipt of this evidence.
                    </div>
                    <a href="{{ route('transfers.acknowledgment-form', $transfer) }}" class="btn btn-primary">
                        <i class="fas fa-hand-paper"></i> Acknowledge Receipt
                    </a>
                </div>
            </div>
        @endif
    @endcan

    {{-- Custody History --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-link"></i> Full Custody History</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Transferred At</th>
                                <th>Reason</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($custodyHistory as $index => $record)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $record->fromUser?->name ?? 'Unknown' }}</strong><br>
                                        <small class="text-muted">{{ $record->fromInstitution?->name ?? 'Unknown' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $record->toUser?->name ?? 'Unknown' }}</strong><br>
                                        <small class="text-muted">{{ $record->toInstitution?->name ?? 'Unknown' }}</small>
                                    </td>
                                    <td>{{ $record->transferred_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                                    <td>{{ $record->transfer_reason ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $record->transfer_reference ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        No custody history recorded yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($custodyHistory->count() > 0)
                    <div class="card-footer">
                        <a href="{{ route('custody-history', $transfer->evidence) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> View Full History
                        </a>
                        @can('disclose-evidence')
                            <a href="{{ route('custody-history-export', $transfer->evidence) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="fas fa-download"></i> Export History
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Transfers
        </a>
    </div>
</div>

<style>
    .timeline {
        position: relative;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
    }

    .timeline-item:not(:last-child) .timeline-point::after {
        content: '';
        position: absolute;
        width: 2px;
        height: 40px;
        background-color: #dee2e6;
        left: 50%;
        top: 30px;
        transform: translateX(-50%);
    }

    .timeline-point {
        width: 20px;
        height: 20px;
        background-color: #e9ecef;
        border: 3px solid #fff;
        border-radius: 50%;
        position: relative;
        margin-right: 15px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .timeline-item.completed .timeline-point {
        background-color: #28a745;
        box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
    }

    .timeline-item.rejected .timeline-point {
        background-color: #dc3545;
        box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
    }

    .timeline-item.active .timeline-point {
        background-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    .timeline-content h6 {
        margin-bottom: 0.25rem;
        font-weight: 600;
    }
</style>
@endsection
