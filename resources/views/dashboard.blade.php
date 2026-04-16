<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Evidence Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">EMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-light">
                            {{ Auth::user()->name }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>Dashboard</h1>
                <p>Welcome to the Evidence Management System</p>
                
                <div class="card mt-4">
                    <div class="card-header">
                        Your Information
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>Name:</th>
                                <td>{{ Auth::user()->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ Auth::user()->email }}</td>
                            </tr>
                            <tr>
                                <th>Employee ID:</th>
                                <td>{{ Auth::user()->employee_id ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>Job Title:</th>
                                <td>{{ Auth::user()->job_title ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>Last Login:</th>
                                <td>{{ Auth::user()->last_login_at ?? 'First login' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if(auth()->user()->hasRole('administrator') || auth()->user()->hasRole('system-administrator'))
                <div class="card mt-4">
                    <div class="card-header">
                        Admin Actions
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Manage Users</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>