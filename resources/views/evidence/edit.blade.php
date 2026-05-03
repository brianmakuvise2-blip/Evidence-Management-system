@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-pencil-square"></i> Edit Evidence
            </h1>
            <p class="text-muted">Update the evidence details</p>
        </div>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('evidence.update', $evidence) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Basic Information Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Record Reference --}}
                        <div class="mb-3">
                            <label for="case_reference" class="form-label">Record Reference <small class="text-muted">(Optional)</small></label>
                            <input type="text" id="case_reference" name="case_reference" class="form-control @error('case_reference') is-invalid @enderror"
                                   placeholder="e.g., REC-2026-001" value="{{ old('case_reference', $evidence->case_reference) }}">
                            @error('case_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Evidence Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                                   placeholder="Brief description of the evidence" value="{{ old('title', $evidence->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Detailed Description</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" placeholder="Provide detailed information about the evidence...">{{ old('description', $evidence->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Row: Evidence Type and Collection Date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="evidence_type" class="form-label">Evidence Type <span class="text-danger">*</span></label>
                                    <select id="evidence_type" name="evidence_type" class="form-select @error('evidence_type') is-invalid @enderror" required>
                                        <option value="">Select Evidence Type</option>
                                        @foreach($evidenceTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('evidence_type', $evidence->evidence_type) === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('evidence_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="collected_date" class="form-label">Collection Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="collected_date" name="collected_date" class="form-control @error('collected_date') is-invalid @enderror"
                                           value="{{ old('collected_date', $evidence->collected_date?->format('Y-m-d\TH:i')) }}" required>
                                    @error('collected_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Digital Upload Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-earmark"></i> Digital Evidence
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($evidence->file_path)
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle"></i>
                                <strong>Current File:</strong> {{ $evidence->file_type }} ({{ number_format($evidence->file_size / 1024, 2) }} KB)
                                <br>
                                <a href="{{ $evidence->getFileUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-download"></i> Download Current File
                                </a>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="file" class="form-label">
                                Upload New Evidence File <small class="text-muted">(Max 100MB, Optional)</small>
                            </label>
                            <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror">
                            <small class="text-muted">
                                Leave empty to keep the current file. Upload a new file to replace the existing one.
                            </small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Institutional Information Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-building"></i> Institutional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Row: Institution and Department --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="institution_id" class="form-label">Institution <span class="text-danger">*</span></label>
                                    <select id="institution_id" name="institution_id" class="form-select @error('institution_id') is-invalid @enderror" required>
                                        <option value="">Select Institution</option>
                                        @foreach($institutions as $institution)
                                            <option value="{{ $institution->id }}" {{ old('institution_id', $evidence->institution_id) == $institution->id ? 'selected' : '' }}>
                                                {{ $institution->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('institution_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                        <option value="">Select Department</option>
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="d-grid gap-2 d-sm-flex">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                    <a href="{{ route('evidence.show', $evidence) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Sidebar Info --}}
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-clock-history"></i> Edit History
                    </h6>
                    <dl class="row mb-0 small">
                        <dt class="col-6">Created:</dt>
                        <dd class="col-6">{{ $evidence->created_at?->format('M d, Y') }}</dd>
                        <dt class="col-6">Last Updated:</dt>
                        <dd class="col-6">{{ $evidence->updated_at?->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            @if($evidence->status === 'verified')
                <div class="alert alert-warning">
                    <i class="bi bi-shield-check"></i>
                    <strong>Note:</strong> This evidence has been verified and is locked from further editing.
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Important:</strong> Once evidence is verified by an administrator, it cannot be edited.
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Load departments when institution changes
    document.getElementById('institution_id').addEventListener('change', function() {
        let institutionId = this.value;
        let departmentSelect = document.getElementById('department_id');
        
        if (!institutionId) {
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            return;
        }

        // Fetch departments for selected institution
        fetch(`/api/institutions/${institutionId}/departments`)
            .then(response => response.json())
            .then(data => {
                let currentDeptId = '{{ $evidence->department_id }}';
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                data.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    if (dept.id == currentDeptId) {
                        option.selected = true;
                    }
                    departmentSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    });

    // Trigger change event on page load
    if (document.getElementById('institution_id').value) {
        document.getElementById('institution_id').dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection
