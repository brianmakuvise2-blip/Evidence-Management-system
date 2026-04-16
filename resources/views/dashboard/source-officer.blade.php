@extends('layouts.admin')

@section('title', 'Source Officer Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-shield"></i> Source Officer Dashboard
                    </h4>
                    <small class="text-muted">City of Bulawayo - Evidence Collection</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['evidenceCount'] }}</h5>
                                    <p class="card-text">My Evidence Items</p>
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['pendingTransfers'] }}</h5>
                                    <p class="card-text">Pending Transfers</p>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['completedTransfers'] }}</h5>
                                    <p class="card-text">Completed Transfers</p>
                                    <i class="fas fa-check-circle fa-2x"></i>
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
                        <a href="{{ route('evidence.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Evidence
                        </a>
                        <a href="{{ route('transfers.create') }}" class="btn btn-secondary">
                            <i class="fas fa-exchange-alt"></i> Request Transfer
                        </a>
                        <a href="{{ route('evidence.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> View My Evidence
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> My Recent Evidence
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['myEvidence']->count() > 0)
                        <div class="list-group">
                            @foreach($data['myEvidence'] as $evidence)
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
                        <p class="text-muted">No evidence items found. <a href="{{ route('evidence.create') }}">Create your first evidence item</a>.</p>
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
                        <i class="fas fa-exchange-alt"></i> My Transfer Requests
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['myTransferRequests']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Evidence</th>
                                        <th>Destination</th>
                                        <th>Status</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['myTransferRequests'] as $transfer)
                                        <tr>
                                            <td>{{ $transfer->transfer_reference }}</td>
                                            <td>{{ $transfer->evidence->title }}</td>
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
                        <p class="text-muted">No transfer requests found. <a href="{{ route('transfers.create') }}">Request your first transfer</a>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection