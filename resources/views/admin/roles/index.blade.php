@extends('layouts.admin')

@section('title', 'Role Management')
@section('page-title', 'Role Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Manage roles and permissions per institution.</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create Role
    </a>
</div>

@foreach($rolesByInstitution as $group => $groupRoles)
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-building text-primary"></i>
        <h5 class="mb-0">{{ $group }}</h5>
        <span class="badge bg-secondary ms-auto">{{ count($groupRoles) }} role(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:220px;">Role Name</th>
                    <th>Permissions</th>
                    <th style="width:80px;" class="text-center">Users</th>
                    <th style="width:160px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupRoles as $role)
                <tr>
                    <td>
                        <span class="fw-semibold font-monospace text-primary">{{ $role->name }}</span>
                    </td>
                    <td>
                        @forelse($role->permissions->sortBy('name') as $perm)
                            <span class="badge bg-light text-dark border me-1 mb-1" style="font-size:0.7rem;">{{ $perm->name }}</span>
                        @empty
                            <span class="text-muted small">No permissions assigned</span>
                        @endforelse
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info text-white">{{ $userCounts[$role->id] ?? 0 }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @php
                            $protected = ['super-admin','system-administrator','administrator','source-officer','evidence-officer'];
                        @endphp
                        @if(!in_array($role->name, $protected))
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete role {{ $role->name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

@endsection
