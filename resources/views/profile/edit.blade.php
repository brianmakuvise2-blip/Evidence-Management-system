@extends('layouts.admin')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-pencil-square me-2"></i>Edit Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone_mobile" class="form-label">Mobile Phone</label>
                                    <input type="tel" class="form-control @error('phone_mobile') is-invalid @enderror" 
                                        id="phone_mobile" name="phone_mobile" value="{{ old('phone_mobile', $user->phone_mobile) }}">
                                    @error('phone_mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone_work" class="form-label">Work Phone</label>
                                    <input type="tel" class="form-control @error('phone_work') is-invalid @enderror" 
                                        id="phone_work" name="phone_work" value="{{ old('phone_work', $user->phone_work) }}">
                                    @error('phone_work')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Read-only Information -->
                        <hr>
                        <div class="alert alert-light mb-3">
                            <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Read-Only Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="d-block text-muted mb-1">Employee ID:</small>
                                    <small class="fw-500">{{ $user->employee_id ?? 'Not set' }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="d-block text-muted mb-1">Department:</small>
                                    <small class="fw-500">{{ $user->department?->name ?? 'Not set' }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
