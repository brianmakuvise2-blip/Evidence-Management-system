@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Welcome back, <strong>{{ $user->name }}</strong>! You have standard user access to the Evidence Management System.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="stat-value">{{ $user->name }}</div>
                <div class="stat-label">Your Name</div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-value">{{ $user->institution->name ?? 'N/A' }}</div>
                <div class="stat-label">Institution</div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-value">{{ $user->employee_id ?? 'N/A' }}</div>
                <div class="stat-label">Employee ID</div>
            </div>
        </div>
    </div>

    <!-- Your Information -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-person-circle me-2"></i>Your Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <strong class="d-block mb-2">Name:</strong>
                                <span class="text-muted">{{ $user->name }}</span>
                            </div>
                            <div class="mb-4">
                                <strong class="d-block mb-2">Email:</strong>
                                <span class="text-muted">{{ $user->email }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <strong class="d-block mb-2">Institution:</strong>
                                <span class="text-muted">{{ $user->institution->name ?? 'Not Assigned' }}</span>
                            </div>
                            <div class="mb-4">
                                <strong class="d-block mb-2">Department:</strong>
                                <span class="text-muted">{{ $user->department->name ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <strong class="d-block mb-2">Job Title:</strong>
                                <span class="text-muted">{{ $user->job_title ?? 'Not Specified' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <strong class="d-block mb-2">Employee ID:</strong>
                                <span class="text-muted">{{ $user->employee_id ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-activity me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if(isset($data['recentActivity']) && $data['recentActivity']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($data['recentActivity']->take(3) as $activity)
                            <div class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-muted">{{ $activity->action }}</small>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No recent activity</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="bi bi-bell me-2"></i>Notifications</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-bell me-2"></i>View Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-gear me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person me-2"></i>Edit Profile
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('evidence.index') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-file-earmark-text me-2"></i>View Evidence
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-bell me-2"></i>Notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection