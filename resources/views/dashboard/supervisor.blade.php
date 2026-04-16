@extends('layouts.admin')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-check"></i> Supervisor Dashboard
                    </h4>
                    <small class="text-muted">{{ $user->institution->name }} - Transfer Approvals</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['pendingCount'] }}</h5>
                                    <p class="card-text">Pending Approvals</p>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['approvedCount'] }}</h5>
                                    <p class="card-text">Approved (This Week)</p>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['rejectedCount'] }}</h5>
                                    <p class="card-text">Rejected (This Week)</p>
                                    <i class="fas fa-times-circle fa-2x"></i>
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Pending Approvals
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['pendingApprovals']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Evidence</th>
                                        <th>Requester</th>
                                        <th>Destination</th>
                                        <th>Urgency</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['pendingApprovals'] as $transfer)
                                        <tr>
                                            <td>{{ $transfer->transfer_reference }}</td>
                                            <td>
                                                <strong>{{ $transfer->evidence->title }}</strong><br>
                                                <small class="text-muted">{{ $transfer->evidence->exhibit_number }}</small>
                                            </td>
                                            <td>{{ $transfer->requestedBy->name }}</td>
                                            <td>{{ $transfer->destinationInstitution->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $transfer->urgency_level === 'critical' ? 'danger' : ($transfer->urgency_level === 'high' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($transfer->urgency_level) }}
                                                </span>
                                            </td>
                                            <td>{{ $transfer->requested_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('transfers.approval-form', $transfer) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> Review
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>No Pending Approvals</h5>
                            <p class="text-muted">All transfer requests have been processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Recent Actions
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['recentActions']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Evidence</th>
                                        <th>Requester</th>
                                        <th>Action</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['recentActions'] as $transfer)
                                        <tr>
                                            <td>{{ $transfer->transfer_reference }}</td>
                                            <td>{{ $transfer->evidence->title }}</td>
                                            <td>{{ $transfer->requestedBy->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $transfer->status === 'approved' ? 'success' : ($transfer->status === 'rejected' ? 'danger' : 'secondary') }}">
                                                    {{ ucfirst($transfer->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $transfer->updated_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent actions found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection