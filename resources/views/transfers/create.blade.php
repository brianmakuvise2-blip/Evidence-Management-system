@extends('layouts.admin')

@section('title', 'Request Evidence Transfer')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-arrow-right"></i> Request Evidence Transfer
                    </h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <strong>Errors:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('transfers.store') }}" method="POST">
                        @csrf

                        {{-- Evidence Selection --}}
                        <div class="mb-3">
                            <label for="evidence_id" class="form-label">
                                <i class="fas fa-box"></i> Evidence to Transfer <span class="text-danger">*</span>
                            </label>
                            <select name="evidence_id" id="evidence_id" class="form-select @error('evidence_id') is-invalid @enderror" required>
                                <option value="">-- Select Evidence --</option>
                                @if($evidence)
                                    <option value="{{ $evidence->id }}" selected>
                                        {{ $evidence->exhibit_number }} - {{ $evidence->title }}
                                    </option>
                                @else
                                    @forelse(\App\Models\Evidence::where('institution_id', Auth::user()->institution_id)
                                        ->whereIn('status', ['verified', 'stored', 'transferred'])
                                        ->get() as $ev)
                                        <option value="{{ $ev->id }}">
                                            {{ $ev->exhibit_number }} - {{ $ev->title }}
                                        </option>
                                    @empty
                                        <option disabled>No eligible evidence available</option>
                                    @endforelse
                                @endif
                            </select>
                            @error('evidence_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Only verified, stored, or previously transferred evidence can be transferred
                            </small>
                        </div>

                        {{-- Destination Institution --}}
                        <div class="mb-3">
                            <label for="destination_institution_id" class="form-label">
                                <i class="fas fa-building"></i> Destination Institution <span class="text-danger">*</span>
                            </label>
                            <select name="destination_institution_id" id="destination_institution_id" class="form-select @error('destination_institution_id') is-invalid @enderror" required onchange="loadReceivingOfficers(null)">
                                <option value="">-- Select Destination Institution --</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('destination_institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destination_institution_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Receiving Officer --}}
                        <div class="mb-3">
                            <label for="receiving_officer_id" class="form-label">
                                <i class="fas fa-user-tie"></i> Receiving Officer <span class="text-danger">*</span>
                            </label>
                            <select name="receiving_officer_id" id="receiving_officer_id" class="form-select @error('receiving_officer_id') is-invalid @enderror" required>
                                <option value="">-- Select Receiving Officer --</option>
                            </select>
                            @error('receiving_officer_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Select an officer from the destination institution
                            </small>
                        </div>

                        {{-- Transfer Reason --}}
                        <div class="mb-3">
                            <label for="transfer_reason" class="form-label">
                                <i class="fas fa-comment"></i> Reason for Transfer <span class="text-danger">*</span>
                            </label>
                            <textarea name="transfer_reason" id="transfer_reason" class="form-control @error('transfer_reason') is-invalid @enderror" rows="3" required placeholder="Provide reason for transferring this evidence...">{{ old('transfer_reason') }}</textarea>
                            @error('transfer_reason')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 10 characters required</small>
                        </div>

                        {{-- Urgency Level --}}
                        <div class="mb-3">
                            <label for="urgency_level" class="form-label">
                                <i class="fas fa-exclamation-triangle"></i> Urgency Level <span class="text-danger">*</span>
                            </label>
                            <select name="urgency_level" id="urgency_level" class="form-select @error('urgency_level') is-invalid @enderror" required>
                                @foreach(\App\Models\TransferRequest::getUrgencyLevels() as $value => $label)
                                    <option value="{{ $value }}" {{ old('urgency_level', 'medium') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('urgency_level')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Transfer Request
                            </button>
                            <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadReceivingOfficers(preselectedOfficerId) {
    const institutionId = document.getElementById('destination_institution_id').value;
    const selectElement = document.getElementById('receiving_officer_id');

    if (!institutionId) {
        selectElement.innerHTML = '<option value="">-- Select Receiving Officer --</option>';
        return;
    }

    selectElement.innerHTML = '<option value="">Loading officers...</option>';

    fetch(`/api/institutions/${institutionId}/officers`)
        .then(response => response.json())
        .then(data => {
            selectElement.innerHTML = '<option value="">-- Select Receiving Officer --</option>';
            if (data.length === 0) {
                selectElement.innerHTML = '<option value="" disabled>No active officers at this institution</option>';
                return;
            }
            data.forEach(officer => {
                const option = document.createElement('option');
                option.value = officer.id;
                option.textContent = `${officer.name} (${officer.department})`;
                if (preselectedOfficerId && officer.id == preselectedOfficerId) {
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            selectElement.innerHTML = '<option value="">Error loading officers</option>';
        });
}

// Restore selections when form returns with validation errors
@if(old('destination_institution_id'))
    document.addEventListener('DOMContentLoaded', function () {
        const institutionSelect = document.getElementById('destination_institution_id');
        institutionSelect.value = '{{ old('destination_institution_id') }}';
        loadReceivingOfficers('{{ old('receiving_officer_id') }}');
    });
@endif
</script>
@endsection
