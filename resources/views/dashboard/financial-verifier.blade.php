@extends('layouts.admin')

@section('title', 'Financial Verifier Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calculator"></i> Financial Verifier Dashboard
                    </h4>
                    <small class="text-muted">Reserve Bank of Zimbabwe - Financial Evidence Verification</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['evidenceCount'] }}</h5>
                                    <p class="card-text">Financial Evidence</p>
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['verifiedCount'] }}</h5>
                                    <p class="card-text">Verified</p>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['pendingCount'] }}</h5>
                                    <p class="card-text">Pending Review</p>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $user->institution->name }}</h5>
                                    <p class="card-text">Institution</p>
                                    <i class="fas fa-building fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('evidence.index') }}?evidence_type=financial" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search Financial Evidence
                        </a>
                        <a href="{{ route('evidence.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> View All Evidence
                        </a>
                        <button class="btn btn-outline-info" disabled>
                            <i class="fas fa-info-circle"></i> Read-Only Access
                        </button>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> As a Financial Verifier, you have read-only access to financial evidence for verification purposes only.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave"></i> Financial Evidence Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">{{ $data['evidenceCount'] }}</h4>
                            <small class="text-muted">Total Items</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">{{ $data['verifiedCount'] }}</h4>
                            <small class="text-muted">Verified</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning">{{ $data['pendingCount'] }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted mb-0">
                        <i class="fas fa-shield-alt"></i>
                        Your role focuses on verifying the authenticity and accuracy of financial evidence within the RBZ jurisdiction.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> Financial Evidence Items
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['financialEvidence']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exhibit #</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Collected</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['financialEvidence'] as $evidence)
                                        <tr>
                                            <td>{{ $evidence->exhibit_number }}</td>
                                            <td>{{ $evidence->title }}</td>
                                            <td>{{ ucfirst($evidence->evidence_type) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $evidence->status === 'verified' ? 'success' : ($evidence->status === 'registered' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($evidence->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $evidence->collected_date->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('evidence.show', $evidence) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5>No Financial Evidence Found</h5>
                            <p class="text-muted">No financial evidence items are currently available for review.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection