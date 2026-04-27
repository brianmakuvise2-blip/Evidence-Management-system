@extends('layouts.admin')

@section('title', 'Dashboard - Evidence Management System')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Welcome {{ Auth::user()->name }}</h2>
            
            <div class="card">
                <div class="card-header">
                    <h5>Your Information</h5>
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
                    <h5>Admin Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection