@extends('layouts.landlord')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky">
                <h4 class="mt-3">Tenant Admin Dashboard</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-person-circle"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart"></i> Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Welcome, {{ auth()->user()->name }}</h1>
            </div>

            <div class="row">
                <!-- Stats Cards Section -->
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text fs-3 text-primary">{{ $userCount }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card shadow-sm border-success">
                        <div class="card-body">
                            <h5 class="card-title">Active Subscriptions</h5>
                            <p class="card-text fs-3 text-success">{{ $activeSubscriptions }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card shadow-sm border-info">
                        <div class="card-body">
                            <h5 class="card-title">New Signups</h5>
                            <p class="card-text fs-3 text-info">{{ $newSignups }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <div class="card shadow-sm border-warning">
                        <div class="card-body">
                            <h5 class="card-title">Pending Requests</h5>
                            <p class="card-text fs-3 text-warning">{{ $pendingRequests }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Subscriptions Table Section -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Recent Subscriptions</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subscriber Name</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->id }}</td>
                                            <td>{{ $subscription->user->name }}</td>
                                            <td><span class="badge bg-{{ $subscription->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($subscription->status) }}</span></td>
                                            <td>${{ number_format($subscription->amount, 2) }}</td>
                                            <td>{{ $subscription->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Latest Notifications</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($notifications as $notification)
                                    <li class="list-group-item">
                                        <strong>{{ $notification->title }}</strong>
                                        <p>{{ $notification->message }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphs or Charts Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title">Subscription Growth (Last 6 Months)</h5>
                        </div>
                        <div class="card-body">
                            <!-- Insert a graph or chart library here (e.g., Chart.js, ApexCharts) -->
                            <div id="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
