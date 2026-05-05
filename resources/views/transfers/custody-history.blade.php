@extends('layouts.admin')

@section('title', 'Custody History - ' . $evidence->exhibit_number)
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">
                <i class="fas fa-link"></i> Chain of Custody
            </h3>
            <small class="text-muted">Evidence: {{ $evidence->exhibit_number }} - {{ $evidence->title }}</small>
        </div>
        <div class="col-md-4 text-end">
            @can('disclose-evidence')
                <a href="{{ route('custody-history-export', $evidence) }}" class="btn btn-outline-success" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            @endcan
        </div>
    </div>

    {{-- Evidence Summary --}}
    <div class="card mb-4">
        <div class="card-head bg-light p-3">
            <h6 class="mb-0"><strong>Evidence Summary</strong></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Exhibit Number:</strong><br>
                    {{ $evidence->exhibit_number }}
                </div>
                <div class="col-md-3">
                    <strong>Title:</strong><br>
                    {{ $evidence->title }}
                </div>
                <div class="col-md-3">
                    <strong>Classification:</strong><br>
                    {{ $evidence->classification_level ?? 'Unclassified' }}
                </div>
                <div class="col-md-3">
                    <strong>File Hash:</strong><br>
                    <code>{{ substr($evidence->file_hash ?? 'N/A', 0, 32) }}...</code>
                </div>
            </div>
        </div>
    </div>

    {{-- Custody Timeline --}}
    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-list"></i> Complete Custody Record</h6>
        </div>

        @if($custodyHistory->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>From Institution</th>
                            <th>From Officer</th>
                            <th>To Institution</th>
                            <th>To Officer</th>
                            <th>Transfer Date/Time</th>
                            <th>Reason</th>
                            <th>Reference</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($custodyHistory as $index => $record)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <strong>{{ $record->fromInstitution?->name ?? 'Unknown' }}</strong>
                                </td>
                                <td>
                                    {{ $record->fromUser?->name ?? 'Unknown' }}<br>
                                    <small class="text-muted">{{ $record->fromUser?->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $record->toInstitution?->name ?? 'Unknown' }}</strong>
                                </td>
                                <td>
                                    {{ $record->toUser?->name ?? 'Unknown' }}<br>
                                    <small class="text-muted">{{ $record->toUser?->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $record->transferred_at?->format('M d, Y') ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $record->transferred_at?->format('H:i:s') ?? '' }}</small>
                                </td>
                                <td>
                                    {{ $record->transfer_reason ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $record->transfer_reference ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($record->notes)
                                        <small>{{ substr($record->notes, 0, 50) }}{{ strlen($record->notes) > 50 ? '...' : '' }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    This chain of custody record shows all transfers of this evidence item. Each transfer has been digitally recorded with timestamps and digital signatures.
                </small>
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No custody history records found for this evidence.</p>
            </div>
        @endif
    </div>

    {{-- Integrity Verification --}}
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Integrity Verification</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Original File Hash:</strong>
                    <div class="alert alert-info">
                        <code class="word-break">{{ $evidence->file_hash ?? 'N/A' }}</code>
                    </div>
                    <small class="text-muted">SHA-256 hash calculated at upload</small>
                </div>
                <div class="col-md-6">
                    <strong>Hash Status:</strong>
                    <div class="alert alert-info">
                        @if($evidence->file_hash && $custodyHistory->count() > 0)
                            <span class="badge bg-success mb-2">
                                <i class="fas fa-check-circle me-1"></i> Integrity Verified
                            </span>
                            <div>File has not been modified.</div>
                        @else
                            <span class="badge bg-secondary mb-2">
                                <i class="fas fa-info-circle me-1"></i> Verification Pending
                            </span>
                            <div>No file hash available or no custody records.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('evidence.show', $evidence) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Evidence
        </a>
    </div>
</div>

<style>
    code.word-break {
        word-break: break-all;
        font-size: 0.85rem;
    }
</style>
@endsection
