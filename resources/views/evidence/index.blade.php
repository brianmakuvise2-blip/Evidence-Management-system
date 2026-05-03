@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="bi bi-archive-fill"></i> Evidence Collection
            </h1>
            <p class="text-muted mt-2">Manage and track all evidence in the system</p>
        </div>
        <div class="col-auto">
            @if(Auth::user()->hasAnyRole('source-officer', 'evidence-officer', 'administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin', 'judicial-system-admin'))
                <a href="{{ route('evidence.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Register Evidence
                </a>
            @endif
        </div>
    </div>

    {{-- Advanced Search and Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('evidence.index') }}" class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           placeholder="Keyword, title, officer..." value="{{ request('search') }}">
                </div>

                <div class="col-lg-4 col-md-6">
                    <label for="case_reference" class="form-label">Record Reference</label>
                    <input type="text" class="form-control" id="case_reference" name="case_reference"
                           placeholder="REC-2026-001" value="{{ request('case_reference') }}">
                </div>

                <div class="col-lg-4 col-md-6">
                    <label for="exhibit_number" class="form-label">Exhibit Number</label>
                    <input type="text" class="form-control" id="exhibit_number" name="exhibit_number"
                           placeholder="EXH-2026-001-A" value="{{ request('exhibit_number') }}">
                </div>

                <div class="col-lg-4 col-md-6">
                    <label for="file_hash" class="form-label">File Hash</label>
                    <input type="text" class="form-control" id="file_hash" name="file_hash"
                           placeholder="SHA-256 hash" value="{{ request('file_hash') }}">
                </div>

                <div class="col-lg-4 col-md-6">
                    <label for="officer_name" class="form-label">Officer Name</label>
                    <input type="text" class="form-control" id="officer_name" name="officer_name"
                           placeholder="Officer name" value="{{ request('officer_name') }}">
                </div>

                <div class="col-lg-4 col-md-6">
                    <label for="classification_level" class="form-label">Classification</label>
                    <select class="form-select" id="classification_level" name="classification_level">
                        <option value="">All Levels</option>
                        <option value="public" {{ request('classification_level') === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="confidential" {{ request('classification_level') === 'confidential' ? 'selected' : '' }}>Confidential</option>
                        <option value="restricted" {{ request('classification_level') === 'restricted' ? 'selected' : '' }}>Restricted</option>
                        <option value="sealed" {{ request('classification_level') === 'sealed' ? 'selected' : '' }}>Sealed</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="evidence_type" class="form-label">Evidence Type</label>
                    <select class="form-select" id="evidence_type" name="evidence_type">
                        <option value="">All Types</option>
                        @foreach($evidenceTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('evidence_type') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="institution_id" class="form-label">Institution</label>
                    <select class="form-select" id="institution_id" name="institution_id">
                        <option value="">All Institutions</option>
                        @foreach($institutions as $institution)
                            <option value="{{ $institution->id }}" {{ request('institution_id') == $institution->id ? 'selected' : '' }}>
                                {{ $institution->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="start_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                           value="{{ request('start_date') }}">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label for="end_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="{{ request('end_date') }}">
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Search & Filter
                    </button>
                    <a href="{{ route('evidence.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Evidence Cards --}}
    @if($evidence->count() > 0)
        <div class="row row-cols-1 row-cols-xl-2 g-4">
            @foreach($evidence as $item)
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <a href="{{ route('evidence.show', $item) }}" class="text-decoration-none">
                                        <h5 class="mb-1">{{ Str::limit($item->title, 50) }}</h5>
                                    </a>
                                    <p class="mb-0 text-muted">{{ $item->case_reference ?? 'No record reference' }}</p>
                                </div>
                                <span class="badge {{ $item->getStatusBadgeClass() }}">
                                    {{ $item->getStatusDisplay() }}
                                </span>
                            </div>

                            <p class="mb-2 text-muted small">
                                Exhibit: <strong>{{ $item->exhibit_number ?? 'N/A' }}</strong>
                                &middot; Type: <strong>{{ $item->getEvidenceTypeDisplay() }}</strong>
                            </p>

                            <div class="row text-sm mb-3">
                                <div class="col-6 mb-2">
                                    <span class="text-muted">Institution</span>
                                    <div>{{ $item->institution?->name ?? '-' }}</div>
                                </div>
                                <div class="col-6 mb-2">
                                    <span class="text-muted">Officer</span>
                                    <div>{{ $item->collectedBy?->name ?? 'Unknown' }}</div>
                                </div>
                                <div class="col-6 mb-2">
                                    <span class="text-muted">Classification</span>
                                    <div>{{ ucfirst($item->classification_level ?? 'N/A') }}</div>
                                </div>
                                <div class="col-6 mb-2">
                                    <span class="text-muted">Collected</span>
                                    <div>{{ $item->collected_date?->format('M d, Y') ?? '-' }}</div>
                                </div>
                            </div>

                            <p class="text-truncate mb-0">{{ Str::limit($item->description, 120) }}</p>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                            <small class="text-muted">File hash: {{ $item->file_hash ? Str::limit($item->file_hash, 12) : 'None' }}</small>
                            <div class="btn-group" role="group">
                                <a href="{{ route('evidence.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if($item->status !== 'verified' && Auth::user()->hasAnyRole('evidence-officer', 'administrator', 'system-administrator', 'super-admin', 'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin', 'zrp-system-admin', 'judicial-system-admin'))
                                    <a href="{{ route('evidence.edit', $item) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($evidence->hasPages())
            <div class="mt-4">
                {{ $evidence->links() }}
            </div>
        @endif
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <h5 class="mt-3">No Evidence Found</h5>
                <p class="text-muted mb-0">No evidence matches your current filters. Try broadening your search criteria.</p>
            </div>
        </div>
    @endif
</div>
@endsection
