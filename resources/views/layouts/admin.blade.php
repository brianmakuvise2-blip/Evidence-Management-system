<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Evidence Management System')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2b4c7c;
            --primary-dark: #0f2a44;
            --secondary: #64748b;
            --success: #0d9488;
            --warning: #b45309;
            --danger: #b91c1c;
            --info: #2563eb;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
            overflow: hidden;
        }

        /* Layout */
        .app-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid var(--gray-200);
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .sidebar-header p {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin: 0.25rem 0 0 0;
        }

        .sidebar-nav {
            padding: 1.5rem 1rem;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
            padding: 0 1rem;
            margin-bottom: 0.75rem;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--gray-700);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.9375rem;
        }

        .nav-link i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            color: var(--gray-400);
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            background: var(--gray-100);
            color: var(--primary);
        }

        .nav-link:hover i {
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link.active i {
            color: white;
        }

        .nav-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .nav-link.disabled:hover {
            background: transparent;
            color: var(--gray-700);
        }

        .nav-link.disabled:hover i {
            color: var(--gray-400);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* Top Navigation */
        .top-nav {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-badge {
            position: relative;
            color: var(--gray-600);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        .notification-badge:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            font-size: 0.625rem;
            padding: 0.125rem 0.375rem;
            border-radius: 50%;
            min-width: 18px;
            text-align: center;
        }

        .notification-dropdown {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .notification-dropdown .dropdown-header {
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .notification-item {
            padding: 0.75rem 1rem;
            border-left: 3px solid transparent;
        }

        .notification-item.unread {
            background: #f8f9ff;
            border-left-color: #007bff;
        }

        .notification-item.read {
            background: #ffffff;
        }

        .notification-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .notification-indicator {
            width: 8px;
            height: 8px;
            background: #007bff;
            border-radius: 50%;
            margin-left: 0.5rem;
            flex-shrink: 0;
        }
            border-radius: 1rem;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: var(--gray-100);
            border-radius: 2rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .user-dropdown:hover {
            background: var(--gray-200);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .user-info {
            line-height: 1.4;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--gray-900);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Content Area */
        .content-area {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
        }

        /* Cards */
        .card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .card-header {
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
            border-bottom: none;
            background: transparent;
        }

        .card-header h5 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--gray-900);
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        /* Table Container */
        .table-container {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            overflow: auto;
            max-height: calc(100vh - 350px);
            position: relative;
        }

        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }

        /* Table */
        .table {
            margin: 0;
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
        }

        .table thead th {
            background: var(--gray-100);
            padding: 1rem 0.75rem;
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            border-bottom: 1px solid var(--gray-200);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            font-size: 0.9375rem;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
            overflow: hidden;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover td {
            background: var(--gray-50);
        }

        /* Text truncation */
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }

        /* Badges */
        .badge {
            padding: 0.375rem 0.75rem;
            font-weight: 500;
            font-size: 0.75rem;
            border-radius: 2rem;
            letter-spacing: 0.01em;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        .badge.bg-success {
            background: #e0f2f1 !important;
            color: #0d9488;
        }

        .badge.bg-warning {
            background: #fff7e0 !important;
            color: #b45309;
        }

        .badge.bg-danger {
            background: #fee9e9 !important;
            color: #b91c1c;
        }

        .badge.bg-info {
            background: #e0edff !important;
            color: #2563eb;
        }

        .badge.bg-secondary {
            background: var(--gray-200) !important;
            color: var(--gray-700);
        }

        /* Buttons */
        .btn {
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(30,58,95,0.2);
        }

        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(30,58,95,0.2);
        }

        .btn-outline-secondary {
            border-color: var(--gray-300);
            color: var(--gray-700);
            background: white;
        }

        .btn-outline-secondary:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }

        .btn-group {
            display: flex;
            flex-wrap: nowrap;
            gap: 2px;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            flex: 0 0 auto;
            border-radius: 0.375rem !important;
        }

        /* Forms */
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--gray-700);
            margin-bottom: 0.375rem;
        }

        .form-control, .form-select {
            padding: 0.625rem 1rem;
            font-size: 0.9375rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30,58,95,0.1);
            outline: none;
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--danger);
        }

        .invalid-feedback {
            font-size: 0.8125rem;
            color: var(--danger);
            margin-top: 0.25rem;
        }

        /* Modals */
        .modal-content {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
            border-bottom: none;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem 1.5rem 1.5rem;
            border-top: none;
            gap: 0.75rem;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            font-size: 0.9375rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #e0f2f1;
            color: #0d9488;
        }

        .alert-danger {
            background: #fee9e9;
            color: #b91c1c;
        }

        .alert-warning {
            background: #fff7e0;
            color: #b45309;
        }

        /* Pagination */
        .pagination {
            gap: 0.25rem;
            margin: 0;
        }

        .page-link {
            padding: 0.5rem 0.875rem;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            color: var(--gray-900);
        }

        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .page-item.disabled .page-link {
            background: var(--gray-100);
            color: var(--gray-400);
            border-color: var(--gray-200);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                z-index: 1000;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                width: 100%;
            }
            
            .top-nav {
                padding: 1rem;
            }
            
            .content-area {
                padding: 1rem;
            }
            
            .table-container {
                max-height: calc(100vh - 400px);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>Evidence Management</h1>
                <p>Republic of Zimbabwe</p>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Main Navigation -->
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    
                    <div class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    
                    @if(Auth::user()->hasAnyRole(['administrator', 'system-administrator']) || Auth::user()->can('manage-evidence'))
                    <div class="nav-item">
                        <a href="{{ route('evidence.index') }}" class="nav-link {{ request()->routeIs('evidence.*') ? 'active' : '' }}">
                            <i class="bi bi-archive-fill"></i>
                            <span>Evidence</span>
                        </a>
                    </div>
                    @endif

                    @if(Auth::user()->can('view-bundle') || Auth::user()->can('prepare-bundle'))
                        <div class="nav-item">
                            <a href="{{ route('bundles.index') }}" class="nav-link {{ request()->routeIs('bundles.*') ? 'active' : '' }}">
                                <i class="bi bi-journal-bookmark"></i>
                                <span>Court Bundles</span>
                            </a>
                        </div>
                    @endif

                    @if(Auth::user()->can('request-transfer') || Auth::user()->can('approve-transfer') || Auth::user()->can('acknowledge-receipt') || Auth::user()->can('manage-custody'))
                        <div class="nav-item">
                            <a href="{{ route('transfers.index') }}" class="nav-link {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                                <i class="bi bi-link-45deg"></i>
                                <span>Chain of Custody</span>
                            </a>
                        </div>
                    @endif

                    @if(Auth::user()->hasAnyRole(['administrator', 'system-administrator']))
                    <div class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            <span>Users</span>
                        </a>
                    </div>
                    @endif
                </div>
                
                @if(Auth::user()->can('view-reports') || Auth::user()->can('view-audit-logs') || Auth::user()->can('manage-settings'))
                <!-- Administration -->
                <div class="nav-section">
                    <div class="nav-section-title">Administration</div>
                    
                    @if(Auth::user()->can('view-reports'))
                        <div class="nav-item">
                            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="bi bi-bar-chart"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                    @endif

                    <div class="nav-item">
                        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <i class="bi bi-bell"></i>
                            <span>Notifications</span>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger ms-auto">{{ Auth::user()->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ Auth::user()->can('view-audit-logs') ? route('audit-logs.index') : '#' }}" class="nav-link {{ Auth::user()->can('view-audit-logs') ? '' : 'disabled' }}">
                            <i class="bi bi-journal-text"></i>
                            <span>Audit Logs</span>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="{{ Auth::user()->can('manage-settings') ? route('settings.index') : '#' }}" class="nav-link {{ Auth::user()->can('manage-settings') ? '' : 'disabled' }}">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="top-nav">
                <h2 class="page-title">@yield('page-title')</h2>
                
                <div class="user-menu">
                    <div class="notification-badge" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <i class="bi bi-bell"></i>
                        <span class="badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                    </div>

                    <!-- Notification Dropdown -->
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Notifications</h6>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Mark All Read</button>
                                </form>
                            @endif
                        </div>
                        <div class="dropdown-divider"></div>

                        @forelse(Auth::user()->notifications->take(10) as $notification)
                            <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <div class="notification-title">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                        </div>
                                        <div class="notification-message small text-muted">
                                            {{ $notification->data['message'] ?? '' }}
                                        </div>

                                        @if(isset($notification->data['hash_summary']))
                                            <div class="mt-2 p-2 bg-light rounded small">
                                                <strong>Evidence ID:</strong> {{ $notification->data['hash_summary']['evidence_id'] }}<br>
                                                @if($notification->data['hash_summary']['file_updated'])
                                                    <strong>File Updated:</strong> Yes<br>
                                                    @if(isset($notification->data['hash_summary']['changes']['file']))
                                                        <strong>Hash Change:</strong><br>
                                                        <span class="text-danger">Old: {{ substr($notification->data['hash_summary']['changes']['file']['old_hash'], 0, 16) }}...</span><br>
                                                        <span class="text-success">New: {{ substr($notification->data['hash_summary']['changes']['file']['new_hash'], 0, 16) }}...</span>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif

                                        <div class="notification-time small text-muted">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    @if(!$notification->read_at)
                                        <div class="notification-indicator"></div>
                                    @endif
                                </div>
                                @if(isset($notification->data['action_url']))
                                    <div class="mt-2">
                                        <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-primary">View Details</a>
                                    </div>
                                @endif
                                <div class="dropdown-divider"></div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-bell-slash"></i>
                                <div>No notifications</div>
                            </div>
                        @endforelse

                        @if(Auth::user()->notifications->count() > 10)
                            <div class="text-center py-2">
                                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary">View All Notifications</a>
                            </div>
                        @endif
                    </div>

                    <div class="dropdown">
                        <div class="user-dropdown" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="user-info d-none d-md-block">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
                            </div>
                            <i class="bi bi-chevron-down text-gray-500"></i>
                        </div>
                        
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit-password') }}"><i class="bi bi-lock me-2"></i>Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="content-area">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Please correct the following errors:</strong>
                        <ul class="mt-2 mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Page Content -->
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-dismiss only success and error alerts after 4 seconds
        // Notices and warnings remain visible until dismissed manually.
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-success, .alert-danger');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 4000); // 4 seconds
            });
        });
    </script>
    
    <script>
        // Poll for notifications every 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            let lastUnreadCount = {{ Auth::user()->unreadNotifications->count() }};
            
            function updateNotifications() {
                fetch('{{ route("notifications.unread") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const currentCount = data.unread_count;
                    const badge = document.querySelector('.notification-badge .badge');
                    if (badge) {
                        badge.textContent = currentCount;
                        if (currentCount > 0) {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                    
                    // If new notifications, update the dropdown content and show toast
                    if (currentCount > lastUnreadCount) {
                        updateDropdown(data.notifications);
                        showNotificationToast(data.notifications.filter(n => !n.read_at).slice(0, currentCount - lastUnreadCount));
                    }
                    
                    lastUnreadCount = currentCount;
                })
                .catch(error => console.error('Error fetching notifications:', error));
            }
            
            function updateDropdown(notifications) {
                const dropdownMenu = document.querySelector('.notification-dropdown');
                if (!dropdownMenu) return;
                
                // Find the divider and remove all notification items after it
                const divider = dropdownMenu.querySelector('.dropdown-divider');
                if (!divider) return;
                
                // Remove existing notification items
                let nextSibling = divider.nextElementSibling;
                while (nextSibling && !nextSibling.classList.contains('text-center')) {
                    const temp = nextSibling.nextElementSibling;
                    nextSibling.remove();
                    nextSibling = temp;
                }
                
                // Add new notifications before the "View All" link
                const viewAllLink = dropdownMenu.querySelector('.text-center.py-2');
                notifications.forEach(notification => {
                    const item = document.createElement('div');
                    item.className = 'notification-item ' + (notification.read_at ? 'read' : 'unread');
                    item.innerHTML = `
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="notification-title">${notification.title}</div>
                                <div class="notification-message small text-muted">${notification.message}</div>
                                ${notification.hash_summary ? `
                                    <div class="mt-2 p-2 bg-light rounded small">
                                        <strong>Evidence ID:</strong> ${notification.hash_summary.evidence_id}<br>
                                        ${notification.hash_summary.file_updated ? `
                                            <strong>File Updated:</strong> Yes<br>
                                            ${notification.hash_summary.changes && notification.hash_summary.changes.file ? `
                                                <strong>Hash Change:</strong><br>
                                                <span class="text-danger">Old: ${notification.hash_summary.changes.file.old_hash.substring(0,16)}...</span><br>
                                                <span class="text-success">New: ${notification.hash_summary.changes.file.new_hash.substring(0,16)}...</span>
                                            ` : ''}
                                        ` : ''}
                                    </div>
                                ` : ''}
                                <div class="notification-time small text-muted">${notification.created_at}</div>
                            </div>
                            ${!notification.read_at ? '<div class="notification-indicator"></div>' : ''}
                        </div>
                        ${notification.action_url ? `<div class="mt-2"><a href="${notification.action_url}" class="btn btn-sm btn-primary">View Details</a></div>` : ''}
                        <div class="dropdown-divider"></div>
                    `;
                    dropdownMenu.insertBefore(item, viewAllLink);
                });
            }
            
            function showNotificationToast(newNotifications) {
                if (newNotifications.length === 0) return;
                
                // Create a simple toast notification
                const toastHtml = `
                    <div class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-bell me-2"></i>
                                You have ${newNotifications.length} new notification${newNotifications.length > 1 ? 's' : ''}!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                
                const toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.innerHTML = toastHtml;
                document.body.appendChild(toastContainer);
                
                const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
                toast.show();
                
                // Remove after shown
                toastContainer.addEventListener('hidden.bs.toast', () => {
                    toastContainer.remove();
                });
            }
            
            // Poll every 10 seconds
            setInterval(updateNotifications, 10000);
        });
    </script>
    
    <script>
        // Poll for notifications every 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            let lastUnreadCount = {{ Auth::user()->unreadNotifications->count() }};
            
            function updateNotifications() {
                fetch('{{ route("notifications.unread") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const currentCount = data.unread_count;
                    const badge = document.querySelector('.notification-badge .badge');
                    if (badge) {
                        badge.textContent = currentCount;
                        if (currentCount > 0) {
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                    
                    // If new notifications, update the dropdown content and show toast
                    if (currentCount > lastUnreadCount) {
                        updateDropdown(data.notifications);
                        showNotificationToast(data.notifications.filter(n => !n.read_at).slice(0, currentCount - lastUnreadCount));
                    }
                    
                    lastUnreadCount = currentCount;
                })
                .catch(error => console.error('Error fetching notifications:', error));
            }
            
            function updateDropdown(notifications) {
                const dropdownMenu = document.querySelector('.notification-dropdown');
                if (!dropdownMenu) return;
                
                // Find the divider and remove all notification items after it
                const divider = dropdownMenu.querySelector('.dropdown-divider');
                if (!divider) return;
                
                // Remove existing notification items
                let nextSibling = divider.nextElementSibling;
                while (nextSibling && !nextSibling.classList.contains('text-center')) {
                    const temp = nextSibling.nextElementSibling;
                    nextSibling.remove();
                    nextSibling = temp;
                }
                
                // Add new notifications before the "View All" link
                const viewAllLink = dropdownMenu.querySelector('.text-center.py-2');
                notifications.forEach(notification => {
                    const item = document.createElement('div');
                    item.className = 'notification-item ' + (notification.read_at ? 'read' : 'unread');
                    item.innerHTML = `
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="notification-title">${notification.title}</div>
                                <div class="notification-message small text-muted">${notification.message}</div>
                                ${notification.hash_summary ? `
                                    <div class="mt-2 p-2 bg-light rounded small">
                                        <strong>Evidence ID:</strong> ${notification.hash_summary.evidence_id}<br>
                                        ${notification.hash_summary.file_updated ? `
                                            <strong>File Updated:</strong> Yes<br>
                                            ${notification.hash_summary.changes && notification.hash_summary.changes.file ? `
                                                <strong>Hash Change:</strong><br>
                                                <span class="text-danger">Old: ${notification.hash_summary.changes.file.old_hash.substring(0,16)}...</span><br>
                                                <span class="text-success">New: ${notification.hash_summary.changes.file.new_hash.substring(0,16)}...</span>
                                            ` : ''}
                                        ` : ''}
                                    </div>
                                ` : ''}
                                <div class="notification-time small text-muted">${notification.created_at}</div>
                            </div>
                            ${!notification.read_at ? '<div class="notification-indicator"></div>' : ''}
                        </div>
                        ${notification.action_url ? `<div class="mt-2"><a href="${notification.action_url}" class="btn btn-sm btn-primary">View Details</a></div>` : ''}
                        <div class="dropdown-divider"></div>
                    `;
                    dropdownMenu.insertBefore(item, viewAllLink);
                });
            }
            
            function showNotificationToast(newNotifications) {
                if (newNotifications.length === 0) return;
                
                // Create a simple toast notification
                const toastHtml = `
                    <div class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-bell me-2"></i>
                                You have ${newNotifications.length} new notification${newNotifications.length > 1 ? 's' : ''}!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                
                const toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.innerHTML = toastHtml;
                document.body.appendChild(toastContainer);
                
                const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
                toast.show();
                
                // Remove after shown
                toastContainer.addEventListener('hidden.bs.toast', () => {
                    toastContainer.remove();
                });
            }
            
            // Poll every 10 seconds
            setInterval(updateNotifications, 10000);
        });
    </script>
    
    @stack('scripts')
    
</body>
</html>
