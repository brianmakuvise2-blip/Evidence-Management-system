@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role Permissions')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-shield-lock me-2"></i>
                    Edit: <span class="font-monospace text-primary">{{ $role->name }}</span>
                </h5>
            </div>
            <div class="card-body">
                @php
                    $protected = ['super-admin','system-administrator','administrator'];
                    $isProtected = in_array($role->name, $protected);
                @endphp

                @if($isProtected)
                    <div class="alert alert-warning">
                        <i class="bi bi-lock-fill me-2"></i>
                        This is a core system role. Its permissions cannot be modified.
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.roles.update', $role) }}" {{ $isProtected ? 'onsubmit=return false' : '' }}>
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Permissions</label>
                        <div class="border rounded p-3" style="max-height:400px; overflow-y:auto;">
                            <div class="row g-2">
                                @foreach($permissions as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]" value="{{ $permission->name }}"
                                               id="perm_{{ $permission->id }}"
                                               {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                               {{ $isProtected ? 'disabled' : '' }}>
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
                        @if(!$isProtected)
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Permissions
                            </button>
                        @endif
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
