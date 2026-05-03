@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- Deleted Evidence Notice --}}
    @if($evidence->trashed())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <strong>This evidence has been deleted</strong>
                    <p class="mb-0 mt-1">
                        This evidence was permanently deleted on {{ $evidence->deleted_at->format('M j, Y \a\t g:i A') }}.
                        The information below is for audit purposes only.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-archive-fill"></i> {{ $evidence->title }}
            </h1>
            <div class="mt-2">
                <span class="badge {{ $evidence->getStatusBadgeClass() }} me-2">
                    {{ $evidence->getStatusDisplay() }}
                </span>
                <span class="badge bg-light text-dark">
                    {{ $evidence->getEvidenceTypeDisplay() }}
                </span>
            </div>
        </div>
        <div class="col-auto">
            @if(!$evidence->trashed())
                @if($evidence->status !== 'verified' && Auth::user()->hasAnyRole('evidence-officer', 'administrator', 'system-administrator'))
                    <a href="{{ route('evidence.edit', $evidence) }}" class="btn btn-outline-warning me-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endif
            @endif
            <a href="{{ route('evidence.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Evidence Details Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Evidence Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Case Reference</h6>
                            <p class="fs-5">{{ $evidence->case_reference ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Status</h6>
                            <p class="fs-5">
                                <span class="badge {{ $evidence->getStatusBadgeClass() }}">
                                    {{ $evidence->getStatusDisplay() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Evidence Type</h6>
                            <p class="fs-5">{{ $evidence->getEvidenceTypeDisplay() }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Collection Date</h6>
                            <p class="fs-5">{{ $evidence->collected_date?->format('M d, Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted fs-xs">Description</h6>
                            <p class="fs-5">{{ $evidence->description ?? 'No description' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collection Information Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Collection Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Institution</h6>
                            <p class="fs-5">{{ $evidence->institution->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Department</h6>
                            <p class="fs-5">{{ $evidence->department->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Collected By</h6>
                            <p class="fs-5">
                                @if($evidence->collectedBy)
                                    <i class="bi bi-person-circle"></i> {{ $evidence->collectedBy->name }}
                                    <br><small class="text-muted">{{ $evidence->collectedBy->email }}</small>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted fs-xs">Collection Time</h6>
                            <p class="fs-5">{{ $evidence->created_at?->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Verification Information Card --}}
            @if($evidence->status === \App\Models\Evidence::STATUS_VERIFIED || $evidence->verified_at)
                <div class="card shadow-sm mb-4 border-success">
                    <div class="card-header bg-light border-success">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check-lg text-success"></i> Verification Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted fs-xs">Verified By</h6>
                                <p class="fs-5">
                                    @if($evidence->verifiedBy)
                                        <i class="bi bi-person-check"></i> {{ $evidence->verifiedBy->name }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted fs-xs">Verification Date</h6>
                                <p class="fs-5">{{ $evidence->verified_at?->format('M d, Y H:i:s') ?? '-' }}</p>
                            </div>
                        </div>

                        @if($evidence->verification_notes)
                            <div class="row pt-3 border-top">
                                <div class="col-12">
                                    <h6 class="text-muted fs-xs">Verification Notes</h6>
                                    <p class="fs-5">{{ $evidence->verification_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- File Information Card --}}
            @if($evidence->file_path)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Digital Evidence</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted fs-xs">File Type</h6>
                                <p class="fs-5">{{ $evidence->file_type ?? 'Unknown' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted fs-xs">File Size</h6>
                                <p class="fs-5">{{ $evidence->file_size ? number_format($evidence->file_size / 1024 / 1024, 2) . ' MB' : '-' }}</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex">
                            @if($evidence->file_type === 'application/pdf' && $evidence->canBeViewedBy(Auth::user()))
                                @if($evidence->canBeDownloadedBy(Auth::user()))
                                    <a href="{{ route('evidence.download', $evidence) }}" class="btn btn-primary">
                                        <i class="bi bi-download"></i> Download File
                                    </a>
                                @else
                                    <a href="{{ route('evidence.view', $evidence) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i> View File
                                    </a>
                                @endif
                            @elseif($evidence->canBeDownloadedBy(Auth::user()))
                                <a href="{{ route('evidence.download', $evidence) }}" class="btn btn-primary">
                                    <i class="bi bi-download"></i> Download File
                                </a>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-lock"></i>
                                    File preview is only available for PDF evidence.
                                </div>
                            @endif
                        </div>
                        </div>
                    </div>
                @endif

            {{-- Chain of Custody Card --}}
            @if($evidence->chainOfCustodyRecords->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-link-45deg me-2"></i>Chain of Custody</h5>
                        <a href="{{ route('custody-history', $evidence) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-clock-history me-1"></i>Full History
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evidence->chainOfCustodyRecords as $custody)
                                    <tr>
                                        <td>{{ $custody->fromUser?->name ?? '-' }}</td>
                                        <td>{{ $custody->toUser?->name ?? '-' }}</td>
                                        <td>{{ $custody->transferred_at?->format('M d, Y H:i') }}</td>
                                        <td>{{ $custody->location ?? '-' }}</td>
                                        <td>{{ $custody->purpose ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Integrity Hash History Card --}}
            @php
                $hashHistory = \App\Models\EvidenceHashHistory::getHistoryForEvidence($evidence->id);
            @endphp
            @if($hashHistory->count() > 0)
            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10 border-warning d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2 text-warning"></i>Integrity Hash History
                    </h5>
                    <span class="badge bg-warning text-dark">{{ $hashHistory->count() }} entr{{ $hashHistory->count() === 1 ? 'y' : 'ies' }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:140px;">Date</th>
                                <th style="width:110px;">Event</th>
                                <th>Content Hash (SHA-256)</th>
                                <th style="width:130px;">Changed By</th>
                                <th style="width:90px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $prevHash = null; @endphp
                            @foreach($hashHistory as $i => $entry)
                            @php
                                $hashMismatch = $prevHash && $prevHash !== $entry->content_hash;
                                $isLatest = $i === 0;
                            @endphp
                            <tr class="{{ $entry->tampering_detected ? 'table-danger' : ($isLatest ? 'table-success bg-opacity-25' : '') }}">
                                <td class="small">{{ $entry->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ match($entry->change_type) {
                                        'created' => 'bg-success',
                                        'updated' => 'bg-warning text-dark',
                                        'verified' => 'bg-primary',
                                        'file_replaced' => 'bg-danger',
                                        default => 'bg-secondary'
                                    } }}">{{ ucfirst(str_replace('_', ' ', $entry->change_type)) }}</span>
                                </td>
                                <td>
                                    <code class="small">{{ $entry->content_hash ?? '—' }}</code>
                                    @if($entry->previous_state && isset($entry->previous_state['content_hash']) && $entry->previous_state['content_hash'] !== $entry->content_hash)
                                        <br><small class="text-muted">← <span class="text-danger font-monospace" style="font-size:.7rem;">{{ $entry->previous_state['content_hash'] }}</span></small>
                                    @endif
                                    @if($entry->changed_fields && !empty($entry->changed_fields))
                                        @php
                                            $fieldChanges = collect($entry->changed_fields)->filter(fn($v, $k) => $k !== '_integrity_hash' && is_array($v) && isset($v['old'], $v['new']));
                                        @endphp
                                        @foreach($fieldChanges as $field => $change)
                                            <br><small class="text-muted">{{ ucfirst(str_replace('_',' ',$field)) }}: <span class="text-danger">{{ $change['old'] }}</span> → <span class="text-success">{{ $change['new'] }}</span></small>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="small">{{ $entry->user?->name ?? '—' }}</td>
                                <td>
                                    @if($entry->tampering_detected)
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Tampered</span>
                                    @elseif($isLatest)
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Current</span>
                                    @else
                                        <span class="badge bg-light text-dark border">Historic</span>
                                    @endif
                                </td>
                            </tr>
                            @php $prevHash = $entry->content_hash; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @php
                    $tampered = $hashHistory->where('tampering_detected', true)->count();
                @endphp
                @if($tampered > 0)
                <div class="card-footer bg-danger text-white">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>WARNING:</strong> {{ $tampered }} tampering event(s) detected. Contact your system administrator immediately.
                </div>
                @endif
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Actions Card --}}
            @if(Auth::user()->hasAnyRole('administrator', 'system-administrator') && !$evidence->trashed())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning-fill"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($evidence->status !== \App\Models\Evidence::STATUS_VERIFIED)
                            <div class="mb-3">
                                <p class="small text-muted mb-2">
                                    <i class="bi bi-check-lg"></i> Verify Evidence
                                </p>
                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#verifyModal">
                                    Review & Verify
                                </button>
                            </div>
                        @endif

                        @if($evidence->status !== \App\Models\Evidence::STATUS_ARCHIVED)
                            <div class="mb-3">
                                <p class="small text-muted mb-2">
                                    <i class="bi bi-archive"></i> Archive Evidence
                                </p>
                                <form action="{{ route('evidence.archive', $evidence) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning w-100">
                                        Move to Archive
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if(Auth::user()->hasRole('system-administrator'))
                            <div>
                                <p class="small text-muted mb-2">
                                    <i class="bi bi-trash"></i> Delete Evidence
                                </p>
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    Delete Permanently
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Evidence Metadata Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Metadata</h5>
                </div>
                <div class="card-body small">
                    <dl class="row mb-0 fs-xs">
                        <dt class="col-6">Registered:</dt>
                        <dd class="col-6">{{ $evidence->created_at?->format('M d, Y') }}</dd>

                        <dt class="col-6">Last Updated:</dt>
                        <dd class="col-6">{{ $evidence->updated_at?->format('M d, Y H:i') }}</dd>

                        <dt class="col-6">Evidence ID:</dt>
                        <dd class="col-6 text-monospace">{{ $evidence->id }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Information Box --}}
            <div class="alert alert-info small">
                <i class="bi bi-info-circle"></i>
                <strong>Note:</strong> All evidence transfers are tracked in the chain of custody record for audit purposes.
            </div>
        </div>
    </div>
</div>

{{-- Verify Evidence Modal --}}
@if(Auth::user()->hasAnyRole('administrator', 'system-administrator') && !$evidence->trashed())
    <div class="modal fade" id="verifyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('evidence.verify', $evidence) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Verify Evidence</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="action" class="form-label">Action <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="approve" value="approve" checked>
                                <label class="form-check-label" for="approve">
                                    <i class="bi bi-check-circle text-success"></i> Approve & Verify
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="reject" value="reject">
                                <label class="form-check-label" for="reject">
                                    <i class="bi bi-x-circle text-danger"></i> Reject
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="verification_notes" class="form-label">Verification Notes</label>
                            <textarea name="verification_notes" id="verification_notes" class="form-control" rows="4" placeholder="Add any notes about the verification..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Verification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Evidence Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Evidence</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to permanently delete this evidence? This action cannot be undone.</p>
                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-exclamation-triangle"></i> The associated file will also be deleted from storage.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('evidence.destroy', $evidence) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Evidence</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
