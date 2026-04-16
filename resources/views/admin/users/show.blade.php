@extends('layouts.admin')

@section('title', $user->name)

@section('page-title', 'User Profile')

@section('content')
<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; background: var(--primary); font-size: 2rem;">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h4 class="fw-semibold mb-1">{{ $user->name }}</h4>
                <p class="text-secondary mb-3">{{ $user->job_title }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $user->account_status === 'active' ? 'success' : 'secondary' }} px-3 py-2">
                        {{ ucfirst($user->account_status) }}
                    </span>
                    @if($user->mfa_enabled)
                        <span class="badge bg-info px-3 py-2">
                            <i class="bi bi-shield-check me-1"></i>MFA
                        </span>
                    @endif
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </a>
                    <button class="btn btn-outline-secondary" onclick="resetPassword({{ $user->id }})">
                        <i class="bi bi-key"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded p-2 me-3">
                        <i class="bi bi-envelope text-primary"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Email</div>
                        <div class="fw-medium">{{ $user->email }}</div>
                    </div>
                </div>
                
                @if($user->phone_work)
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded p-2 me-3">
                        <i class="bi bi-telephone text-primary"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Work Phone</div>
                        <div class="fw-medium">{{ $user->phone_work }}</div>
                    </div>
                </div>
                @endif
                
                @if($user->phone_mobile)
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded p-2 me-3">
                        <i class="bi bi-phone text-primary"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Mobile</div>
                        <div class="fw-medium">{{ $user->phone_mobile }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="col-xl-8">
        <!-- Tabs -->
        <ul class="nav nav-tabs border-bottom mb-4" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                    <i class="bi bi-person-badge me-2"></i>Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">
                    <i class="bi bi-clock-history me-2"></i>Activity Log
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button">
                    <i class="bi bi-shield-lock me-2"></i>Permissions
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Details Tab -->
            <div class="tab-pane fade show active" id="details">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="bg-light p-4 rounded">
                                    <h6 class="fw-semibold mb-3">Employment Details</h6>
                                    <table style="width: 100%;">
                                        <tr>
                                            <td class="text-secondary py-2" width="120">Employee ID</td>
                                            <td class="fw-medium">{{ $user->employee_id ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-secondary py-2">Badge Number</td>
                                            <td class="fw-medium">{{ $user->badge_number ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-secondary py-2">Institution</td>
                                            <td class="fw-medium">{{ $user->institution->name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-secondary py-2">Department</td>
                                            <td class="fw-medium">{{ $user->department->name ?? '—' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="bg-light p-4 rounded">
                                    <h6 class="fw-semibold mb-3">System Access</h6>
                                    <table style="width: 100%;">
                                        <tr>
                                            <td class="text-secondary py-2" width="120">Data Scope</td>
                                            <td>
                                                <span class="badge bg-{{ $user->data_access_scope === 'all' ? 'danger' : ($user->data_access_scope === 'department' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($user->data_access_scope) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-secondary py-2">Last Login</td>
                                            <td class="fw-medium">
                                                @if($user->last_login_at)
                                                    {{ $user->last_login_at->format('M j, Y H:i') }}
                                                    <div class="small text-secondary">IP: {{ $user->last_login_ip }}</div>
                                                @else
                                                    Never
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-secondary py-2">Password Changed</td>
                                            <td class="fw-medium">
                                                @if($user->password_changed_at)
                                                    {{ $user->password_changed_at->diffForHumans() }}
                                                @else
                                                    Never
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-secondary py-2">Account Created</td>
                                            <td class="fw-medium">{{ $user->created_at->format('M j, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activity Log Tab -->
            <div class="tab-pane fade" id="activity">
                <div class="card">
                    <div class="card-body">
                        @forelse($user->activityLogs as $log)
                        <div class="d-flex gap-3 mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-light rounded p-2">
                                    <i class="bi bi-{{ $log->action === 'login' ? 'box-arrow-in-right' : 'clock' }} text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</h6>
                                    <small class="text-secondary">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                @if($log->details)
                                    <pre class="small bg-light p-2 rounded mt-2" style="font-size: 0.875rem;">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                                <small class="text-secondary">
                                    <i class="bi bi-globe me-1"></i>{{ $log->ip_address ?? 'Unknown IP' }}
                                </small>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-clock-history" style="font-size: 3rem; color: var(--gray-400);"></i>
                            <p class="mt-3 text-secondary">No activity logs found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Permissions Tab -->
            <div class="tab-pane fade" id="permissions">
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Roles</h6>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach($user->roles as $role)
                                <span class="badge bg-info px-3 py-2">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="fw-semibold mb-3">Direct Permissions</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($user->getAllPermissions() as $permission)
                                <span class="badge bg-light text-dark px-3 py-2">{{ $permission->name }}</span>
                            @empty
                                <p class="text-secondary">No direct permissions assigned</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (same as index) -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                @csrf
                <div class="modal-body">
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
@endsection

@push('scripts')
<script>
    function resetPassword(userId) {
        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }
</script>
@endpush