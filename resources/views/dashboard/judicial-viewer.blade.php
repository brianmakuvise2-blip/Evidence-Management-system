@extends('layouts.admin')

@section('title', 'Judicial Viewer Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-balance-scale"></i> Judicial Viewer Dashboard
                    </h4>
                    <small class="text-muted">Judiciary - Court Bundle Access</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $data['bundleCount'] }}</h5>
                                    <p class="card-text">Shared Bundles</p>
                                    <i class="fas fa-folder fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $user->institution->name }}</h5>
                                    <p class="card-text">Institution</p>
                                    <i class="fas fa-building fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Read-Only</h5>
                                    <p class="card-text">Access Level</p>
                                    <i class="fas fa-eye fa-2x"></i>
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
                        <i class="fas fa-info-circle"></i> Access Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-shield-alt"></i> Judicial Access Rights</h6>
                        <ul class="mb-0">
                            <li><strong>Read-Only Access:</strong> You can view approved court bundles shared with you</li>
                            <li><strong>No Downloads:</strong> File downloads are not permitted</li>
                            <li><strong>No Modifications:</strong> You cannot edit, delete, or modify any content</li>
                            <li><strong>Chain of Custody:</strong> Access is limited to bundled evidence only</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> All access is logged for judicial accountability and chain of custody purposes.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder"></i> Recent Shared Bundles
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['recentBundles']->count() > 0)
                        <div class="list-group">
                            @foreach($data['recentBundles'] as $bundle)
                                <a href="{{ route('bundles.show', $bundle) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $bundle->title }}</h6>
                                        <small class="text-muted">
                                            <span class="badge bg-success">Approved</span>
                                        </small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($bundle->description, 60) }}</p>
                                    <small class="text-muted">
                                        Case: {{ $bundle->case_reference }} •
                                        Shared: {{ $bundle->updated_at->format('M d, Y') }}
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No court bundles have been shared with you yet.</p>
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
                        <i class="fas fa-folder-open"></i> All Shared Court Bundles
                    </h5>
                </div>
                <div class="card-body">
                    @if($data['sharedBundles']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Bundle Title</th>
                                        <th>Case Reference</th>
                                        <th>Prepared By</th>
                                        <th>Approved Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['sharedBundles'] as $bundle)
                                        <tr>
                                            <td>{{ $bundle->title }}</td>
                                            <td>{{ $bundle->case_reference }}</td>
                                            <td>{{ $bundle->preparedBy->name }}</td>
                                            <td>{{ $bundle->approved_at ? $bundle->approved_at->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('bundles.show', $bundle) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View Bundle
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-balance-scale fa-4x text-muted mb-3"></i>
                            <h5>No Shared Court Bundles</h5>
                            <p class="text-muted">No court bundles have been formally approved and shared with the Judiciary yet.</p>
                            <small class="text-muted">Bundles will appear here once they are approved and disclosed to judicial officers.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection