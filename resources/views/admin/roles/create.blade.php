@extends('layouts.admin')

@section('title', 'Create Role')
@section('page-title', 'Create Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-plus me-2"></i>New Role</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Institution <span class="text-muted small">(optional)</span></label>
                        <select name="institution_id" class="form-select @error('institution_id') is-invalid @enderror">
                            <option value="">— System-Wide Role —</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                    {{ $institution->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">If an institution is selected, the role name will be prefixed with the institution identifier.</small>
                        @error('institution_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. evidence-reviewer" required>
                        <small class="text-muted">Lowercase letters and hyphens only. The institution prefix is added automatically.</small>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Permissions</label>
                        <div class="border rounded p-3" style="max-height:350px; overflow-y:auto;">
                            <div class="row g-2">
                                @foreach($permissions as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]" value="{{ $permission->name }}"
                                               id="perm_{{ $permission->id }}"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small font-monospace" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Create Role
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
