@extends('layouts.admin')

@section('title', 'Prosecutor Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-gavel"></i> Prosecutor Dashboard
                    </h4>
                    <small class="text-muted">National Prosecuting Authority - Court Preparation</small>
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
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['bundleCount'] }}</h5>
                                    <p class="card-text">Court Bundles</p>
                                    <i class="fas fa-folder fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['approvedBundles'] }}</h5>
                                    <p class="card-text">Approved Bundles</p>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['draftBundles'] }}</h5>
                                    <p class="card-text">Draft Bundles</p>
                                    <i class="fas fa-edit fa-2x"></i>
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
                        <a href="{{ route('evidence.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Review Evidence
                        </a>
                        <a href="{{ route('bundles.create') }}" class="btn btn-secondary">
                            <i class="fas fa-folder-plus"></i> Create Court Bundle
                        </a>
                        <a href="{{ route('bundles.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> View Court Bundles
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder"></i> My Court Bundles
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['myBundles']->count() > 0)
                        <div class="list-group">
                            @foreach($data['myBundles'] as $bundle)
                                <a href="{{ route('bundles.show', $bundle) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $bundle->title }}</h6>
                                        <small class="text-muted">
                                            <span class="badge bg-{{ $bundle->status === 'approved' ? 'success' : 'warning' }}">
                                                {{ ucfirst($bundle->status) }}
                                            </span>
                                        </small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($bundle->description, 60) }}</p>
                                    <small class="text-muted">Record: {{ $bundle->case_reference }} • Created: {{ $bundle->created_at->format('M d, Y') }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No court bundles created yet.</p>
                            <a href="{{ route('court-bundles.create') }}">Create your first bundle</a>.
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
                        <i class="fas fa-file-alt"></i> Assigned Evidence for Review
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['assignedEvidence']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exhibit #</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Collected</th>
                                        <th>Source</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['assignedEvidence'] as $evidence)
                                        <tr>
                                            <td>{{ $evidence->exhibit_number }}</td>
                                            <td>{{ $evidence->title }}</td>
                                            <td>{{ ucfirst($evidence->evidence_type) }}</td>
                                            <td>{{ $evidence->collected_date->format('M d, Y') }}</td>
                                            <td>{{ $evidence->collectedBy->name }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('evidence.show', $evidence) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('bundles.create', ['evidence_id' => $evidence->id]) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-plus"></i> Bundle
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
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5>No Assigned Evidence</h5>
                            <p class="text-muted">No verified evidence is currently assigned to you for review.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection