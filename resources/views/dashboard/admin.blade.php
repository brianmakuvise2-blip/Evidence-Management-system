@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value">{{ $data['totalUsers'] }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-value">{{ $data['activeUsers'] }}</div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="stat-value">{{ $data['inactiveUsers'] }}</div>
                <div class="stat-label">Inactive Users</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- User Management -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-people-fill me-2"></i>User Management</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Create, edit, and manage user accounts</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                            <i class="bi bi-list me-2"></i>View All Users
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Create New User
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports & Analytics -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-bar-chart me-2"></i>Reports</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">View reports and system analytics</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.index') }}" class="btn btn-primary">
                            <i class="bi bi-graph-up me-2"></i>Open Reports Dashboard
                        </a>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-clock-history me-2"></i>Audit Logs
                        </button>
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
                    <h5><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
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
