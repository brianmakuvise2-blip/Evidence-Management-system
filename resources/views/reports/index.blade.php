@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Executive Summary</h5>
                    <div class="row">
                        <div class="col-sm-6 col-xl-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="bi bi-archive-fill"></i>
                                </div>
                                <div class="stat-value">{{ $data['totalEvidence'] }}</div>
                                <div class="stat-label">Total Evidence</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #20c997 0%, #38d9a9 100%);">
                                    <i class="bi bi-check2-circle"></i>
                                </div>
                                <div class="stat-value">{{ $data['verifiedEvidence'] }}</div>
                                <div class="stat-label">Verified Evidence</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #ff922b 0%, #ffd43b 100%);">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="stat-value">{{ $data['pendingTransfers'] }}</div>
                                <div class="stat-label">Pending Transfers</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #f03e3e 0%, #ff6b6b 100%);">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                                <div class="stat-value">{{ $data['approvedBundles'] }}</div>
                                <div class="stat-label">Approved Bundles</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Evidence Status Distribution</h5>
                    <a href="{{ route('reports.export', ['report' => 'evidence']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
                </div>
                <div class="card-body">
                    <canvas id="evidenceStatusChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Quick Access</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('reports.export', ['report' => 'overview']) }}" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-pdf me-2"></i>Export Overview PDF</a>
                        <a href="{{ route('reports.export', ['report' => 'transfers']) }}" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-pdf me-2"></i>Export Transfer Status PDF</a>
                        <a href="{{ route('reports.export', ['report' => 'bundles']) }}" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-pdf me-2"></i>Export Bundle Status PDF</a>
                        <a href="{{ route('reports.export', ['report' => 'activity']) }}" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-pdf me-2"></i>Export Recent Activity PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Transfer Status Breakdown</h5>
                    <a href="{{ route('reports.export', ['report' => 'transfers']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($data['transferStatusCounts'] as $status => $count)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ \App\Models\TransferRequest::getStatuses()[$status] ?? ucfirst($status) }}
                                <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Bundle Status Breakdown</h5>
                    <a href="{{ route('reports.export', ['report' => 'bundles']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($data['bundleStatusCounts'] as $status => $count)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ \App\Models\CourtBundle::getStatuses()[$status] ?? ucfirst($status) }}
                                <span class="badge bg-success rounded-pill">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                    <a href="{{ route('reports.export', ['report' => 'activity']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['recentActivity'] as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'System' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td class="text-truncate">{{ $log->description }}</td>
                                    <td><span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }}">{{ ucfirst($log->status) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No activity recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>
    <script>
        const statusLabels = {!! json_encode(array_map(function($status) {
            return \App\Models\Evidence::getStatuses()[$status] ?? ucfirst($status);
        }, array_keys($data['evidenceStatusCounts']))) !!};
        const statusData = {!! json_encode(array_values($data['evidenceStatusCounts'])) !!};
        const evidenceStatusChart = document.getElementById('evidenceStatusChart');

        if (evidenceStatusChart) {
            new Chart(evidenceStatusChart, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#20c997', '#6610f2', '#dc3545', '#6c757d'],
                    }],
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        }
    </script>
@endpush
