@extends('layouts.admin')

@section('title', 'System Admin Dashboard')
@section('page-title', 'System Admin Dashboard')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value">{{ $data['totalUsers'] }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-value">{{ $data['activeUsers'] }}</div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="stat-value">{{ $data['inactiveUsers'] }}</div>
                <div class="stat-label">Inactive Users</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="stat-value">{{ $data['totalRoles'] }}</div>
                <div class="stat-label">System Roles</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Operational Metrics -->
        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #38b2ac 0%, #0bc5ea 100%);">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="stat-value">{{ $data['totalEvidence'] }}</div>
                <div class="stat-label">Evidence Records</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ffb703 0%, #fb8500 100%);">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="stat-value">{{ $data['pendingTransfers'] }}</div>
                <div class="stat-label">Pending Transfers</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ff4d6d 0%, #ff758f 100%);">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="stat-value">{{ $data['overdueTransfers'] }}</div>
                <div class="stat-label">Overdue Requests</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #38a169 0%, #48bb78 100%);">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div class="stat-value">{{ $data['bundleStatusCounts'][\App\Models\CourtBundle::STATUS_APPROVED] ?? 0 }}</div>
                <div class="stat-label">Approved Bundles</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- System Actions -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-gear me-2"></i>System Configuration</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Manage system-wide settings and configurations</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-shield-check me-2"></i>Manage Roles
                        </button>
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-sliders me-2"></i>System Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-people-fill me-2"></i>User Management</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Create and manage user accounts</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Create User
                        </a>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-archive me-2"></i>View Archives
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit & Security -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-shield-check me-2"></i>Audit & Security</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Monitor system security and activity logs</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-clock-history me-2"></i>View Audit Logs
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-graph-up me-2"></i>Security Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history me-2"></i>Recent System Activity</h5>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date/Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['recentLogs'] as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->user->name ?? 'System' }}</strong>
                                    </td>
                                    <td>
                                        <code>{{ $log->action }}</code>
                                    </td>
                                    <td class="text-truncate">{{ $log->description }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No activity logs yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Your Information -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-person-circle me-2"></i>Your Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $user->name }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $user->email }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Role:</strong>
                        </div>
                        <div class="col-sm-8">
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Department:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $user->department->name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Last Login:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'First login' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
