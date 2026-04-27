@extends('layouts.admin')

@section('title', 'Institution Admin Dashboard')
@section('page-title', 'Institution Admin Dashboard')

@section('content')
    <div class="row">
        <!-- Institution Statistics -->
        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-value">{{ $data['institutionUsers'] }}</div>
                <div class="stat-label">Institution Users</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-value">{{ $data['activeUsers'] }}</div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stat-value">{{ $data['institutionEvidence'] }}</div>
                <div class="stat-label">Institution Evidence</div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div class="stat-value">{{ $data['pendingTransfers'] }}</div>
                <div class="stat-label">Pending Transfers</div>
            </div>
        </div>
    </div>

    <!-- Institution-Specific Content -->
    <div class="row mt-4">
        @if($data['dashboardType'] === 'rbz-admin')
            <!-- RBZ Specific -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-bank me-2"></i>Financial Evidence</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Manage financial and banking evidence</p>
                        <div class="stat-value text-primary">{{ $data['financialEvidence'] ?? 0 }}</div>
                        <div class="stat-label">Financial Records</div>
                    </div>
                </div>
            </div>
        @elseif($data['dashboardType'] === 'zacc-admin')
            <!-- ZACC Specific -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-search me-2"></i>Investigative Cases</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Anti-corruption investigation evidence</p>
                        <div class="stat-value text-primary">{{ $data['investigativeCases'] ?? 0 }}</div>
                        <div class="stat-label">Active Cases</div>
                    </div>
                </div>
            </div>
        @elseif($data['dashboardType'] === 'npa-admin')
            <!-- NPA Specific -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-file-earmark-ruled me-2"></i>Court Bundles</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Bundles prepared for prosecution</p>
                        <div class="stat-value text-primary">{{ $data['courtBundles'] ?? 0 }}</div>
                        <div class="stat-label">Prepared Bundles</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-share me-2"></i>Evidence Disclosures</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Evidence disclosed to courts</p>
                        <div class="stat-value text-primary">{{ $data['disclosedEvidence'] ?? 0 }}</div>
                        <div class="stat-label">Disclosed Items</div>
                    </div>
                </div>
            </div>
        @elseif($data['dashboardType'] === 'zrp-admin')
            <!-- ZRP Specific -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-shield-check me-2"></i>Seizure Evidence</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Seized items and exhibits</p>
                        <div class="stat-value text-primary">{{ $data['seizureEvidence'] ?? 0 }}</div>
                        <div class="stat-label">Seizure Records</div>
                    </div>
                </div>
            </div>
        @elseif($data['dashboardType'] === 'judicial-admin')
            <!-- Judicial Admin Specific -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-file-earmark-check me-2"></i>Approved Bundles</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Court-approved evidence bundles</p>
                        <div class="stat-value text-primary">{{ $data['approvedBundles'] ?? 0 }}</div>
                        <div class="stat-label">Approved Bundles</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-eye me-2"></i>Shared Evidence</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Evidence shared with judiciary</p>
                        <div class="stat-value text-primary">{{ $data['sharedBundles'] ?? 0 }}</div>
                        <div class="stat-label">Shared Bundles</div>
                    </div>
                </div>
            </div>
        @elseif($data['dashboardType'] === 'courts-admin')
            <!-- Courts Admin Specific -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-archive me-2"></i>Archived Evidence</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Evidence moved to archives</p>
                        <div class="stat-value text-primary">{{ $data['archivedEvidence'] ?? 0 }}</div>
                        <div class="stat-label">Archived Items</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-trash me-2"></i>Disposed Evidence</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Evidence disposed of</p>
                        <div class="stat-value text-primary">{{ $data['disposedEvidence'] ?? 0 }}</div>
                        <div class="stat-label">Disposed Items</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Common Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-gear me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people me-2"></i>Manage Users
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('evidence.index') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-file-earmark-text me-2"></i>View Evidence
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-journal-text me-2"></i>Audit Logs
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-bell me-2"></i>Notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    @if(isset($data['recentLogs']) && $data['recentLogs']->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-activity me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($data['recentLogs']->take(5) as $log)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $log->action }}</h6>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-muted">{{ Str::limit($log->description, 100) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection