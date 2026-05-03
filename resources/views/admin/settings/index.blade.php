@extends('layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>General Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Application Name</label>
                            <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror"
                                value="{{ $settings['app_name'] }}">
                            @error('app_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">System Email</label>
                            <input type="email" name="app_email" class="form-control @error('app_email') is-invalid @enderror"
                                value="{{ $settings['app_email'] }}">
                            @error('app_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em;">Display</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Items Per Page</label>
                            <input type="number" name="items_per_page" class="form-control"
                                value="{{ $settings['items_per_page'] }}" min="10" max="100">
                            <small class="text-muted">Tables pagination (10–100)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Session Timeout (minutes)</label>
                            <input type="number" name="session_timeout" class="form-control"
                                value="{{ $settings['session_timeout'] }}" min="5" max="480">
                            <small class="text-muted">Auto-logout after inactivity</small>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em;">Security</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password Expiry (days)</label>
                            <input type="number" name="password_expiry_days" class="form-control"
                                value="{{ $settings['password_expiry_days'] }}" min="0" max="365">
                            <small class="text-muted">0 = Never expires</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Max Login Attempts</label>
                            <input type="number" name="max_login_attempts" class="form-control"
                                value="{{ $settings['max_login_attempts'] }}" min="3" max="20">
                            <small class="text-muted">Before account lockout</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Lockout Duration (minutes)</label>
                            <input type="number" name="lockout_duration_minutes" class="form-control"
                                value="{{ $settings['lockout_duration_minutes'] }}" min="5" max="120">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="enable_mfa" id="enable_mfa"
                                    {{ $settings['enable_mfa'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="enable_mfa">
                                    Require MFA for all users
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em;">
                        <i class="bi bi-diagram-3 me-1"></i>Cross-Institution Evidence Instructions
                    </h6>
                    <p class="text-muted small mb-3">
                        When evidence is uploaded by any organisation, these instructions are included in the notification sent to administrators of all other institutions.
                    </p>
                    <div class="mb-3">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="cross_institution_notify" id="cross_institution_notify"
                                {{ $settings['cross_institution_notify'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="cross_institution_notify">
                                Enable cross-institution notifications on evidence upload
                            </label>
                        </div>
                        <label class="form-label fw-semibold">Instructions Text</label>
                        <textarea name="evidence_instructions" class="form-control" rows="6">{{ $settings['evidence_instructions'] }}</textarea>
                        <small class="text-muted">This message is appended to every evidence-upload notification sent to other institutions.</small>
                    </div>

                    <div class="d-flex gap-2 mt-4">
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

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>System Information</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">App Version</dt>
                    <dd class="col-7">v1.0.0</dd>
                    <dt class="col-5 text-muted">Environment</dt>
                    <dd class="col-7"><span class="badge bg-info">{{ config('app.env') }}</span></dd>
                    <dt class="col-5 text-muted">PHP Version</dt>
                    <dd class="col-7">{{ phpversion() }}</dd>
                    <dt class="col-5 text-muted">Database</dt>
                    <dd class="col-7">{{ $dbDriver }} {{ $dbVersion }}</dd>
                    <dt class="col-5 text-muted">Timezone</dt>
                    <dd class="col-7">{{ config('app.timezone') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" id="clearCacheBtn" onclick="clearCache()">
                    <i class="bi bi-arrow-repeat me-2"></i>Clear Cache
                </button>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-journal-text me-2"></i>View Audit Logs
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-shield-lock me-2"></i>Manage Roles
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-people me-2"></i>Manage Users
                </a>
            </div>
        </div>

        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Evidence Integrity</h5>
            </div>
            <div class="card-body small">
                <ul class="mb-0 ps-3">
                    <li>SHA-256 hash generated on every upload</li>
                    <li>New hash recorded on any field change</li>
                    <li>Old &amp; new hash shown side-by-side</li>
                    <li>Admins notified of all integrity events</li>
                    <li>Tampering detected &amp; flagged automatically</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    const btn = document.getElementById('clearCacheBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Clearing...';
    fetch('{{ route('settings.clear-cache') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.innerHTML = data.success
            ? '<i class="bi bi-check-circle me-2"></i>Cache Cleared!'
            : '<i class="bi bi-x-circle me-2"></i>Failed';
        btn.disabled = false;
        setTimeout(() => { btn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Clear Cache'; }, 3000);
    })
    .catch(() => {
        btn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Error';
        btn.disabled = false;
        setTimeout(() => { btn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Clear Cache'; }, 3000);
    });
}
</script>
@endsection
