@extends('layouts.admin')

@section('title', 'User Management')

@section('page-title', 'User Management')

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #1e3a5f, #2b4c7c);">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['active'] ?? 0) }}</div>
            <div class="stat-label">Active Users</div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #b45309, #d97706);">
                <i class="bi bi-person-exclamation"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['inactive'] ?? 0) }}</div>
            <div class="stat-label">Inactive Users</div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #64748b, #94a3b8);">
                <i class="bi bi-archive"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['archived'] ?? 0) }}</div>
            <div class="stat-label">Archived Users</div>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="d-flex gap-2 flex-wrap">
        <select class="form-select form-select-sm" style="width: 180px;" id="filterInstitution">
            <option value="">All Institutions</option>
            @foreach($institutions as $institution)
                <option value="{{ $institution->id }}" {{ request('institution') == $institution->id ? 'selected' : '' }}>
                    {{ $institution->name }}
                </option>
            @endforeach
        </select>
        
        <select class="form-select form-select-sm" style="width: 150px;" id="filterStatus">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
        
        <select class="form-select form-select-sm" style="width: 150px;" id="filterRole">
            <option value="">All Roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
        
        <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
            <i class="bi bi-x-circle me-1"></i>Clear
        </button>
    </div>
    
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" name="search" class="form-control" placeholder="Search users..." 
                       value="{{ request('search') }}">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New User
        </a>
    </div>
</div>

<!-- Users Table -->
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th width="40">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                </th>
                <th width="200">User</th>
                <th width="180">Institution</th>
                <th width="180">Department</th>
                <th width="180">Role / Access</th>
                <th width="100">Status</th>
                <th width="140">Last Login</th>
                <th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-2" style="width: 32px; height: 32px; background: var(--primary); font-size: 0.875rem; flex-shrink: 0;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div style="overflow: hidden;">
                            <div class="fw-semibold text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                            <div class="small text-secondary text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="text-truncate d-block" title="{{ $user->institution->name ?? '' }}">
                        {{ $user->institution->name ?? '—' }}
                    </span>
                </td>
                <td>
                    <span class="text-truncate d-block" title="{{ $user->department->name ?? '' }}">
                        {{ $user->department->name ?? '—' }}
                    </span>
                </td>
                <td>
                    @foreach($user->roles as $role)
                        <span class="badge bg-info d-inline-block mb-1" title="{{ ucfirst($role->name) }}">
                            {{ ucfirst($role->name) }}
                        </span>
                    @endforeach
                    <div class="small text-secondary text-truncate" title="Scope: {{ ucfirst($user->data_access_scope) }}">
                        Scope: {{ ucfirst($user->data_access_scope) }}
                    </div>
                </td>
                <td>
                    @php
                        $statusColors = [
                            'active' => 'success',
                            'inactive' => 'warning',
                            'suspended' => 'danger',
                            'archived' => 'secondary'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$user->account_status] }}">
                        {{ ucfirst($user->account_status) }}
                    </span>
                    @if($user->mfa_enabled)
                        <i class="bi bi-shield-check text-success ms-1" title="MFA Enabled"></i>
                    @endif
                </td>
                <td>
                    @if($user->last_login_at)
                        <div>{{ $user->last_login_at->format('M j, Y') }}</div>
                        <div class="small text-secondary">{{ $user->last_login_at->format('H:i') }}</div>
                    @else
                        <span class="text-secondary">Never</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" style="gap: 2px;">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary btn-sm" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-outline-warning btn-sm" title="Reset Password"
                                onclick="resetPassword({{ $user->id }})">
                            <i class="bi bi-key"></i>
                        </button>
                        @if($user->account_status === 'active')
                            <button type="button" class="btn btn-outline-danger btn-sm" title="Archive"
                                    onclick="archiveUser({{ $user->id }})">
                                <i class="bi bi-archive"></i>
                            </button>
                        @elseif($user->account_status === 'archived')
                            <button type="button" class="btn btn-outline-success btn-sm" title="Reactivate"
                                    onclick="reactivateUser({{ $user->id }})">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: var(--gray-400);"></i>
                    <h5 class="mt-3 text-secondary">No users found</h5>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-lg me-2"></i>Add your first user
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Bulk Actions & Pagination -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-4">
    <div class="bulk-actions mb-3 mb-md-0" style="display: none;">
        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary"><span id="selectedCount">0</span> users selected</span>
            <select class="form-select form-select-sm" style="width: 150px;" id="bulkStatus">
                <option value="">Select action</option>
                <option value="active">Set Active</option>
                <option value="inactive">Set Inactive</option>
                <option value="suspended">Suspend</option>
                <option value="archived">Archive</option>
            </select>
            <button class="btn btn-primary btn-sm" onclick="bulkUpdate()">Apply</button>
            <button class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">Cancel</button>
        </div>
    </div>
    
    <div>
        {{ $users->withQueryString()->links() }}
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-secondary mb-3">Enter a new password for this user.</p>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archive User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="archiveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-secondary mb-3">Are you sure you want to archive this user? They will lose access to the system.</p>
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Enter reason for archiving..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Archive User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Bulk selection
    let selectedUsers = [];
    
    document.getElementById('selectAll')?.addEventListener('change', function(e) {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = e.target.checked;
            if (e.target.checked) {
                if (!selectedUsers.includes(cb.value)) selectedUsers.push(cb.value);
            } else {
                selectedUsers = [];
            }
        });
        updateBulkActions();
    });
    
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                if (!selectedUsers.includes(this.value)) selectedUsers.push(this.value);
            } else {
                selectedUsers = selectedUsers.filter(id => id !== this.value);
                document.getElementById('selectAll').checked = false;
            }
            updateBulkActions();
        });
    });
    
    function updateBulkActions() {
        const bulkActions = document.querySelector('.bulk-actions');
        const selectedCount = document.getElementById('selectedCount');
        
        if (selectedUsers.length > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = selectedUsers.length;
        } else {
            bulkActions.style.display = 'none';
        }
    }
    
    function clearSelection() {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        selectedUsers = [];
        updateBulkActions();
    }
    
    function resetPassword(userId) {
        const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        document.getElementById('resetPasswordForm').action = `/admin/users/${userId}/reset-password`;
        modal.show();
    }
    
    function archiveUser(userId) {
        const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
        document.getElementById('archiveForm').action = `/admin/users/${userId}/archive`;
        modal.show();
    }
    
    function reactivateUser(userId) {
        if (confirm('Are you sure you want to reactivate this user?')) {
            fetch(`/admin/users/${userId}/reactivate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(response => {
                if (response.ok) window.location.reload();
            });
        }
    }
    
    function bulkUpdate() {
        const status = document.getElementById('bulkStatus').value;
        if (!status) {
            alert('Please select an action');
            return;
        }
        
        fetch('/admin/users/bulk-update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                user_ids: selectedUsers,
                status: status
            })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  window.location.reload();
              } else {
                  alert(data.message);
              }
          });
    }
    
    // Filters
    document.getElementById('filterInstitution')?.addEventListener('change', applyFilters);
    document.getElementById('filterStatus')?.addEventListener('change', applyFilters);
    document.getElementById('filterRole')?.addEventListener('change', applyFilters);
    
    function applyFilters() {
        const params = new URLSearchParams(window.location.search);
        
        ['institution', 'status', 'role'].forEach(filter => {
            const value = document.getElementById(`filter${filter.charAt(0).toUpperCase() + filter.slice(1)}`).value;
            if (value) params.set(filter, value);
            else params.delete(filter);
        });
        
        window.location.href = window.location.pathname + '?' + params.toString();
    }
    
    function resetFilters() {
        window.location.href = '{{ route('admin.users.index') }}';
    }
</script>
@endpush