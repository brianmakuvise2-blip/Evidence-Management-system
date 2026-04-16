@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Welcome back, <strong>{{ $user->name }}</strong>! You have limited access to the Evidence Management System.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="stat-value">{{ $user->name }}</div>
                <div class="stat-label">Your Name</div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-value">{{ $user->employee_id ?? 'N/A' }}</div>
                <div class="stat-label">Employee ID</div>
            </div>
        </div>
    </div>

    <!-- Your Information -->
    <div class="row mt-4">
        <div class="col-md-12">
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
                            <div class="mb-4">
                                <strong class="d-block mb-2">Employee ID:</strong>
                                <span class="text-muted">{{ $user->employee_id ?? 'Not set' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <strong class="d-block mb-2">Job Title:</strong>
                                <span class="text-muted">{{ $user->job_title ?? 'Not set' }}</span>
                            </div>
                            <div class="mb-4">
                                <strong class="d-block mb-2">Department:</strong>
                                <span class="text-muted">{{ $user->department->name ?? 'Not set' }}</span>
                            </div>
                            <div class="mb-4">
                                <strong class="d-block mb-2">Last Login:</strong>
                                <span class="text-muted">
                                    @if($data['lastLogin'])
                                        {{ $data['lastLogin']->format('M d, Y H:i') }}
                                    @else
                                        First login
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help & Support -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-question-circle me-2"></i>Help & Support</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        If you need access to additional features or have questions about the Evidence Management System,
                        please contact your system administrator.
                    </p>
                    <div class="alert alert-light mt-3 mb-0">
                        <strong>Quick Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>You can update your profile information from the user menu</li>
                            <li>Your role determines what features you can access</li>
                            <li>All actions are logged for security purposes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
