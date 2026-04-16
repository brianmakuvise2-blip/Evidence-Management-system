@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="bi bi-journal-bookmark"></i> {{ $bundle->title }}
            </h1>
            <p class="text-muted mt-2">Court bundle details, item index, and version history.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('bundles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Bundles
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Case Reference</h6>
                            <p>{{ $bundle->case_reference }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Version</h6>
                            <p>{{ $bundle->version }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Prepared By</h6>
                            <p>{{ $bundle->preparedBy?->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status</h6>
                            <span class="badge bg-secondary">{{ ucfirst($bundle->status) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Description</h6>
                        <p>{{ $bundle->description ?? 'No description provided.' }}</p>
                    </div>

                    <div class="mb-4">
                        <h5>Bundle Index</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Page</th>
                                        <th>Exhibit Number</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bundle->items as $item)
                                        <tr>
                                            <td>{{ $item->page_reference }}</td>
                                            <td>{{ $item->exhibit_number }}</td>
                                            <td>{{ Str::limit($item->description ?? '-', 120) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h5>Disclosure Log</h5>
                        @if($bundle->disclosures->count())
                            <ul class="list-group list-group-flush">
                                @foreach($bundle->disclosures as $disclosure)
                                    <li class="list-group-item">
                                        <strong>{{ $disclosure->created_at?->format('M d, Y H:i') }}</strong>
                                        <br>
                                        Shared with: {{ $disclosure->recipient_name ?? $disclosure->sharedWith?->name ?? 'Unknown' }}
                                        <br>
                                        Notes: {{ $disclosure->notes ?? 'No notes' }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No disclosure events recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Bundle Actions</h5>
                    @can('prepare-bundle')
                        @if($bundle->status === App\Models\CourtBundle::STATUS_DRAFT)
                            <form action="{{ route('bundles.approve', $bundle) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check2-circle"></i> Approve Bundle
                                </button>
                            </form>
                        @endif
                    @endcan

                    @if($bundle->isApproved() || Auth::user()->can('prepare-bundle'))
                        <a href="{{ route('bundles.export', $bundle) }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-download"></i> Export PDF Bundle
                        </a>
                    @endif

                    <div class="mt-3">
                        <p class="text-muted mb-1">Prepared on</p>
                        <p>{{ $bundle->created_at?->format('M d, Y H:i') }}</p>
                        <p class="text-muted mb-1">Approved on</p>
                        <p>{{ $bundle->approved_at?->format('M d, Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
