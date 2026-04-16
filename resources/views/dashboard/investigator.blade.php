@extends('layouts.admin')

@section('title', 'Investigator Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-search"></i> Investigator Dashboard
                    </h4>
                    <small class="text-muted">{{ $user->institution->name }} - Criminal Investigations</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['evidenceCount'] }}</h5>
                                    <p class="card-text">Assigned Evidence</p>
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['pendingRequests'] }}</h5>
                                    <p class="card-text">Pending Requests</p>
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
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $user->department->name }}</h5>
                                    <p class="card-text">Department</p>
                                    <i class="fas fa-users fa-2x"></i>
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
                        <a href="{{ route('transfers.create') }}" class="btn btn-primary">
                            <i class="fas fa-exchange-alt"></i> Request Evidence Transfer
                        </a>
                        <a href="{{ route('evidence.index') }}" class="btn btn-secondary">
                            <i class="fas fa-search"></i> Search Evidence
                        </a>
                        <a href="{{ route('transfers.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> View Transfer Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> Assigned Evidence
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['assignedEvidence']->count() > 0)
                        <div class="list-group">
                            @foreach($data['assignedEvidence'] as $evidence)
                                <a href="{{ route('evidence.show', $evidence) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $evidence->title }}</h6>
                                        <small class="text-muted">{{ $evidence->status }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($evidence->description, 50) }}</p>
                                    <small class="text-muted">{{ $evidence->exhibit_number }} • {{ $evidence->collected_date->format('M d, Y') }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No assigned evidence found.</p>
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
                        <i class="fas fa-exchange-alt"></i> Transfer Requests
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['transferRequests']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Evidence</th>
                                        <th>Requester</th>
                                        <th>Destination</th>
                                        <th>Status</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['transferRequests'] as $transfer)
                                        <tr>
                                            <td>{{ $transfer->transfer_reference }}</td>
                                            <td>{{ $transfer->evidence->title }}</td>
                                            <td>{{ $transfer->requestedBy->name }}</td>
                                            <td>{{ $transfer->destinationInstitution->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $transfer->status === 'pending' ? 'warning' : ($transfer->status === 'approved' ? 'success' : 'secondary') }}">
                                                    {{ ucfirst($transfer->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $transfer->requested_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No transfer requests found. <a href="{{ route('transfers.create') }}">Request a transfer</a>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection