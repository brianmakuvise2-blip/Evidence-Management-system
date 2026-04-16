@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="bi bi-plus-circle"></i> Create Court Bundle
            </h1>
            <p class="text-muted mt-2">Select evidence items and compile a bundle for court presentation.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('bundles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Bundles
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bundles.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Bundle Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Case Reference</label>
                        <input type="text" name="case_reference" value="{{ old('case_reference') }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="mb-3">Choose Evidence Items</h5>
                    <p class="text-muted">Select the evidence items that should be included in this bundle.</p>
                </div>

                <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                    @foreach($evidenceItems as $item)
                        <div class="col">
                            <div class="card border-secondary h-100">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="evidence_ids[]" value="{{ $item->id }}" id="evidence_{{ $item->id }}"
                                            {{ in_array($item->id, old('evidence_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="evidence_{{ $item->id }}">
                                            <strong>{{ $item->exhibit_number ?? 'N/A' }}</strong> - {{ Str::limit($item->title, 60) }}
                                        </label>
                                    </div>

                                    <p class="mb-1"><span class="text-muted">Case:</span> {{ $item->case_reference ?? 'N/A' }}</p>
                                    <p class="mb-1"><span class="text-muted">Status:</span> {{ $item->getStatusDisplay() }}</p>
                                    <p class="mb-1"><span class="text-muted">Institution:</span> {{ $item->institution?->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle"></i> Create Bundle
                    </button>
                    <a href="{{ route('bundles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
