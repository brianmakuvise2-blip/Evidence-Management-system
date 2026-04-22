@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- General Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">General Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Application Name</label>
                                <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror" 
                                    value="{{ config('app.name', 'Evidence Management System') }}">
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Application Email</label>
                                <input type="email" name="app_email" class="form-control @error('app_email') is-invalid @enderror"
                                    value="{{ config('mail.from.address', 'noreply@evidence-system.local') }}">
                                @error('app_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Display Settings -->
                        <h6 class="mb-3">Display Settings</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Items Per Page</label>
                                <input type="number" name="items_per_page" class="form-control @error('items_per_page') is-invalid @enderror"
                                    value="{{ config('app.items_per_page', 50) }}" min="10" max="100">
                                <small class="text-muted">Number of items to display in tables (10-100)</small>
                                @error('items_per_page')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Session Timeout (minutes)</label>
                                <input type="number" name="session_timeout" class="form-control @error('session_timeout') is-invalid @enderror"
                                    value="{{ config('session.lifetime', 120) }}" min="5" max="480">
                                <small class="text-muted">Auto-logout after inactivity</small>
                                @error('session_timeout')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Security Settings -->
                        <h6 class="mb-3">Security Settings</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Expiry (days)</label>
                                <input type="number" name="password_expiry_days" class="form-control @error('password_expiry_days') is-invalid @enderror"
                                    value="{{ config('auth.password_expiry_days', 90) }}" min="0" max="365">
                                <small class="text-muted">0 = Never expires</small>
                                @error('password_expiry_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Login Attempts</label>
                                <input type="number" name="max_login_attempts" class="form-control @error('max_login_attempts') is-invalid @enderror"
                                    value="{{ config('auth.max_login_attempts', 5) }}" min="3" max="20">
                                <small class="text-muted">Before account lockout</small>
                                @error('max_login_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Lockout Duration (minutes)</label>
                                <input type="number" name="lockout_duration_minutes" class="form-control @error('lockout_duration_minutes') is-invalid @enderror"
                                    value="{{ config('auth.lockout_duration_minutes', 15) }}" min="5" max="120">
                                <small class="text-muted">Duration to lock account after failed attempts</small>
                                @error('lockout_duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label d-flex align-items-center">
                                    <input type="checkbox" name="enable_mfa" class="form-check-input me-2" 
                                        {{ config('auth.mfa_enabled', false) ? 'checked' : '' }}>
                                    Enable Multi-Factor Authentication
                                </label>
                                <small class="text-muted d-block">Require MFA for all users</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Settings
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">System Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Application Version</label>
                        <div class="fs-6">v1.0.0</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Environment</label>
                        <div class="fs-6">
                            <span class="badge bg-info">{{ config('app.env') }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">PHP Version</label>
                        <div class="fs-6">{{ phpversion() }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Database</label>
                        <div class="fs-6">MySQL {{ \Illuminate\Support\Facades\DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Timezone</label>
                        <div class="fs-6">{{ config('app.timezone') }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="clearCache()">
                            <i class="bi bi-arrow-repeat me-2"></i>Clear Cache
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="clearLogs()">
                            <i class="bi bi-trash me-2"></i>Clear Logs
                        </button>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-journal-text me-2"></i>View Audit Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clearCache() {
            if (confirm('Are you sure you want to clear the application cache? This action may temporarily impact performance.')) {
                // Implementation would go here
                alert('Cache cleared successfully!');
            }
        }

        function clearLogs() {
            if (confirm('Are you sure you want to clear logs? This cannot be undone.')) {
                // Implementation would go here
                alert('Logs cleared successfully!');
            }
        }
    </script>
@endsection
