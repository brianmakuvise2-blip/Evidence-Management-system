@extends('layouts.admin')

@section('title', 'Approve Transfer Request')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle"></i> Approve Transfer Request
                    </h5>
                </div>

                <div class="card-body">
                    {{-- Transfer Summary --}}
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Transfer Summary</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Evidence:</strong> {{ $transfer->evidence->exhibit_number }}<br>
                                <strong>From:</strong> {{ $transfer->requestedBy->institution->name }}<br>
                                <strong>Requested By:</strong> {{ $transfer->requestedBy->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>To:</strong> {{ $transfer->destinationInstitution->name }}<br>
                                <strong>Receiving Officer:</strong> {{ $transfer->receivingOfficer->name }}<br>
                                <strong>Urgency:</strong> 
                                <span class="badge bg-{{ $transfer->urgency_level === 'critical' ? 'danger' : ($transfer->urgency_level === 'high' ? 'warning' : 'info') }}">
                                    {{ \App\Models\TransferRequest::getUrgencyLevels()[$transfer->urgency_level] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded">
                        <strong>Reason for Transfer:</strong>
                        <p class="mb-0 mt-2">{{ $transfer->transfer_reason }}</p>
                    </div>

                    {{-- Approval Form --}}
                    <form action="{{ route('transfers.approve', $transfer) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="approval_notes" class="form-label">
                                <i class="fas fa-pencil"></i> Approval Notes (Optional)
                            </label>
                            <textarea name="approval_notes" id="approval_notes" class="form-control" rows="4" placeholder="Add any notes about this approval..."></textarea>
                            <small class="text-muted">Internal notes that will be recorded in the custody history</small>
                        </div>

                        <div class="mb-3 p-3 bg-warning bg-opacity-10 rounded">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="confirm" required>
                                <label class="form-check-label" for="confirm">
                                    I confirm that this transfer has been verified and approve it to proceed.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve Transfer
                            </button>
                            <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
