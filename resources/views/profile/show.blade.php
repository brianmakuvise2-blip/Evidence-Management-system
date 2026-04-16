@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Name:</strong>
                            <span class="text-muted">{{ $user->name }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Email:</strong>
                            <span class="text-muted">{{ $user->email }}</span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Employee ID:</strong>
                            <span class="text-muted">{{ $user->employee_id ?? 'Not set' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Badge Number:</strong>
                            <span class="text-muted">{{ $user->badge_number ?? 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Job Title:</strong>
                            <span class="text-muted">{{ $user->job_title ?? 'Not set' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Department:</strong>
                            <span class="text-muted">{{ $user->department?->name ?? 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Institution:</strong>
                            <span class="text-muted">{{ $user->institution?->name ?? 'Not set' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Role:</strong>
                            <span>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-primary">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</span>
                                @empty
                                    <span class="text-muted">No role assigned</span>
                                @endforelse
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Mobile Phone:</strong>
                            <span class="text-muted">{{ $user->phone_mobile ?? 'Not set' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Work Phone:</strong>
                            <span class="text-muted">{{ $user->phone_work ?? 'Not set' }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Last Login:</strong>
                            <span class="text-muted">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('M d, Y H:i') }}
                                @else
                                    First login
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block mb-2">Account Status:</strong>
                            <span>
                                <span class="badge bg-{{ $user->account_status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($user->account_status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('profile.edit-password') }}" class="btn btn-outline-primary">
                            <i class="bi bi-lock me-2"></i>Change Password
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-shield-check me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Member Since:</small>
                        <span class="text-sm">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Last Updated:</small>
                        <span class="text-sm">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-2">Security Status:</small>
                        @if($user->mfa_enabled)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>2FA Enabled
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="bi bi-exclamation-circle me-1"></i>2FA Disabled
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
