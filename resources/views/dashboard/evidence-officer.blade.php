@extends('layouts.admin')

@section('title', 'Evidence Officer Dashboard')
@section('page-title', 'Evidence Officer Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-label">Evidence Items</div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="bi bi-link-45deg"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-label">Custody Transfers</div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-value">{{ $data['lastLogin'] ? $data['lastLogin']->diffForHumans() : 'First login' }}</div>
                <div class="stat-label">Last Login</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Evidence Management -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-folder-fill me-2"></i>Evidence Management</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Manage and track evidence items</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" disabled>
                            <i class="bi bi-plus-circle me-2"></i>Log New Evidence
                        </button>
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-search me-2"></i>Search Evidence
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chain of Custody -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-link-45deg me-2"></i>Chain of Custody</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Track evidence custody and transfers</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-arrow-left-right me-2"></i>Transfer Evidence
                        </button>
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-list-task me-2"></i>View Transfers
                        </button>
                    </div>
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
                            {{ $data['department']->name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Last Login:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $data['lastLogin'] ? $data['lastLogin']->format('M d, Y H:i') : 'First login' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
