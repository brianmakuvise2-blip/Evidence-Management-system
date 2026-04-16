@extends('layouts.admin')

@section('title', 'Reject Transfer Request')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-ban"></i> Reject Transfer Request
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

                    {{-- Rejection Form --}}
                    <form action="{{ route('transfers.reject', $transfer) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">
                                <i class="fas fa-exclamation-circle"></i> Rejection Reason <span class="text-danger">*</span>
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" rows="4" required placeholder="Explain why this transfer is being rejected...">{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">This reason will be sent to the requesting officer</small>
                        </div>

                        <div class="mb-3">
                            <label for="rejection_correction_notes" class="form-label">
                                <i class="fas fa-lightbulb"></i> Correction Notes (Optional)
                            </label>
                            <textarea name="rejection_correction_notes" id="rejection_correction_notes" class="form-control" rows="3" placeholder="What needs to be corrected or changed..."></textarea>
                            <small class="text-muted">Suggestions to help the requesting officer resubmit</small>
                        </div>

                        <div class="mb-3 p-3 bg-warning bg-opacity-10 rounded">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="confirm" required>
                                <label class="form-check-label" for="confirm">
                                    I confirm that I have reviewed this request and am rejecting it for the stated reason.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-ban"></i> Reject Transfer
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
