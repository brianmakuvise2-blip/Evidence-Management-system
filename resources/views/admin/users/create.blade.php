@extends('layouts.admin')

@section('title', 'Create New User')

@section('page-title', 'Create New User')

@section('page-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}" id="userForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-12">
                            <h6 class="mb-3 text-primary">Basic Information</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                   name="employee_id" value="{{ old('employee_id') }}">
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Badge Number</label>
                            <input type="text" class="form-control @error('badge_number') is-invalid @enderror" 
                                   name="badge_number" value="{{ old('badge_number') }}">
                            @error('badge_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <hr class="my-3">
                            <h6 class="mb-3 text-primary">Institution & Department</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Institution <span class="text-danger">*</span></label>
                            <select class="form-select @error('institution_id') is-invalid @enderror" 
                                    name="institution_id" id="institution_id" required>
                                <option value="">Select Institution</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }} ({{ $institution->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('institution_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department_id') is-invalid @enderror" 
                                    name="department_id" id="department_id" required>
                                <option value="">Select Department</option>
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('job_title') is-invalid @enderror" 
                                   name="job_title" value="{{ old('job_title') }}" required>
                            @error('job_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <hr class="my-3">
                            <h6 class="mb-3 text-primary">Contact Information</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone (Work)</label>
                            <input type="text" class="form-control @error('phone_work') is-invalid @enderror" 
                                   name="phone_work" value="{{ old('phone_work') }}">
                            @error('phone_work')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone (Mobile)</label>
                            <input type="text" class="form-control @error('phone_mobile') is-invalid @enderror" 
                                   name="phone_mobile" value="{{ old('phone_mobile') }}">
                            @error('phone_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <hr class="my-3">
                            <h6 class="mb-3 text-primary">Access & Security</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Access Scope <span class="text-danger">*</span></label>
                            <select class="form-select @error('data_access_scope') is-invalid @enderror" 
                                    name="data_access_scope" required>
                                <option value="personal" {{ old('data_access_scope') == 'personal' ? 'selected' : '' }}>Personal Only</option>
                                <option value="department" {{ old('data_access_scope') == 'department' ? 'selected' : '' }}>Department</option>
                                <option value="all" {{ old('data_access_scope') == 'all' ? 'selected' : '' }}>All Access</option>
                            </select>
                            @error('data_access_scope')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Determines what data the user can access</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Roles <span class="text-danger">*</span></label>
                            <select class="form-select @error('roles') is-invalid @enderror" 
                                    name="roles[]" multiple required size="3">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                        
                        <div class="col-md-12">
                            <hr class="my-3">
                            <h6 class="mb-3 text-primary">Password</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required minlength="8">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   name="password_confirmation" required minlength="8">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="reset" class="btn btn-secondary me-2">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Copy this for edit.blade.php as well, just change the action and add PUT method -->
@endsection

@push('scripts')
<script>
    // Load departments when institution changes
    document.getElementById('institution_id').addEventListener('change', function() {
        const institutionId = this.value;
        const departmentSelect = document.getElementById('department_id');
        
        if (institutionId) {
            fetch(`/admin/get-departments?institution_id=${institutionId}`)
                .then(response => response.json())
                .then(data => {
                    departmentSelect.innerHTML = '<option value="">Select Department</option>';
                    if (data.success) {
                        data.data.forEach(dept => {
                            departmentSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                        });
                    }
                });
        } else {
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
        }
    });
    
    // If there's an old value, trigger change to load departments
    @if(old('institution_id'))
        document.getElementById('institution_id').value = '{{ old('institution_id') }}';
        document.getElementById('institution_id').dispatchEvent(new Event('change'));
        
        // Set the old department value after load
        setTimeout(() => {
            document.getElementById('department_id').value = '{{ old('department_id') }}';
        }, 500);
    @endif
</script>
@endpush