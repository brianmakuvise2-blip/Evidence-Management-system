@extends('layouts.admin')

@section('title', 'Acknowledge Receipt')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-hand-paper"></i> Acknowledge Receipt of Evidence
                    </h5>
                </div>

                <div class="card-body">
                    {{-- Transfer Summary --}}
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Transfer Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Evidence:</strong> {{ $transfer->evidence->exhibit_number }}<br>
                                <strong>Title:</strong> {{ $transfer->evidence->title }}<br>
                                <strong>From:</strong> {{ $transfer->evidence->institution->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Approved By:</strong> {{ $transfer->supervisorApprover->name }}<br>
                                <strong>Approved At:</strong> {{ $transfer->approved_at->format('M d, Y H:i:s') }}<br>
                                <strong>Transfer Reference:</strong> {{ $transfer->transfer_reference }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded">
                        <strong>Transfer Reason:</strong>
                        <p class="mb-0 mt-2">{{ $transfer->transfer_reason }}</p>
                    </div>

                    {{-- Acknowledgment Form --}}
                    <form action="{{ route('transfers.acknowledge', $transfer) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="condition_verification" class="form-label">
                                <i class="fas fa-check-square"></i> Evidence Condition <span class="text-danger">*</span>
                            </label>
                            <select name="condition_verification" id="condition_verification" class="form-select @error('condition_verification') is-invalid @enderror" required>
                                <option value="">-- Select Condition Status --</option>
                                <option value="intact">Intact - No Issues</option>
                                <option value="damaged">Damaged - Issues Noted</option>
                                <option value="compromised">Compromised - Evidence Integrity Affected</option>
                            </select>
                            @error('condition_verification')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Verify the condition of the evidence upon receipt. Damaged or compromised conditions will be recorded in the custody history.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="acknowledgment_notes" class="form-label">
                                <i class="fas fa-pencil"></i> Receipt Notes (Optional)
                            </label>
                            <textarea name="acknowledgment_notes" id="acknowledgment_notes" class="form-control" rows="4" placeholder="Record any relevant observations, special handling requirements, or other notes..."></textarea>
                            <small class="text-muted">These notes will be permanently recorded in the chain of custody</small>
                        </div>

                        <div class="mb-3 p-3 bg-warning bg-opacity-10 rounded">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="confirm" required>
                                <label class="form-check-label" for="confirm">
                                    I confirm that I have received this evidence in the condition stated above and digitally acknowledge receipt. This action is legally binding and will be permanently recorded.
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-secondary">
                            <small>
                                <strong>Digital Signature:</strong> This acknowledgment will be digitally signed and timestamps recorded. 
                                You acknowledge that you received this evidence and accept responsibility for its custody at this time.
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-hand-paper"></i> Acknowledge Receipt
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
