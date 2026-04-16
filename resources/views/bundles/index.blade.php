@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="bi bi-folder2-open"></i> Court Bundles
            </h1>
            <p class="text-muted mt-2">Prepare and review court bundles for case prosecution.</p>
        </div>
        <div class="col-auto">
            @can('prepare-bundle')
                <a href="{{ route('bundles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create Bundle
                </a>
            @endcan
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('bundles.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Case Reference</label>
                    <input type="text" class="form-control" name="case_reference" value="{{ request('case_reference') }}" placeholder="Case reference">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bundle Title</label>
                    <input type="text" class="form-control" name="title" value="{{ request('title') }}" placeholder="Bundle title">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        @foreach(App\Models\CourtBundle::getStatuses() as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-outline-primary mt-4"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    @if($bundles->count())
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Case Reference</th>
                            <th>Version</th>
                            <th>Status</th>
                            <th>Prepared By</th>
                            <th>Approved By</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bundles as $bundle)
                            <tr>
                                <td>{{ $bundle->title }}</td>
                                <td>{{ $bundle->case_reference }}</td>
                                <td>{{ $bundle->version }}</td>
                                <td>
                                    <span class="badge bg-secondary text-uppercase">{{ ucfirst($bundle->status) }}</span>
                                </td>
                                <td>{{ $bundle->preparedBy?->name ?? 'Unknown' }}</td>
                                <td>{{ $bundle->approvedBy?->name ?? '-' }}</td>
                                <td>{{ $bundle->created_at?->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('bundles.show', $bundle) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('prepare-bundle')
                                        <a href="{{ route('bundles.export', $bundle) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($bundles->hasPages())
                <div class="card-footer bg-light">
                    {{ $bundles->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder-x display-4 text-muted"></i>
                <h5 class="mt-3">No bundles found</h5>
                <p class="text-muted mb-0">No court bundles match your current filters.</p>
            </div>
        </div>
    @endif
</div>
@endsection
