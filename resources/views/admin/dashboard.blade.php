@extends('layouts.app')
@section('title', 'Admin Dashboard — FoodFlow')
@section('panel-name', 'Super Admin')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Overview of all restaurants & activity')

@section('sidebar-nav')
<div class="nav-section">Main</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item active"><i class="fas fa-chart-pie"></i> Dashboard</a>
<a href="{{ route('admin.restaurants.index') }}" class="nav-item"><i class="fas fa-store"></i> Restaurants</a>
<a href="{{ route('admin.restaurants.index') }}?status=pending" class="nav-item"><i class="fas fa-clock"></i> Pending Approval</a>
<a href="{{ route('admin.restaurants.index') }}?status=suspended" class="nav-item"><i class="fas fa-ban"></i> Suspended</a>
@endsection

@section('content')
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-store"></i></div>
        <div><div class="stat-value">{{ $stats['total_restaurants'] }}</div><div class="stat-label">Total Restaurants</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div><div class="stat-value">{{ $stats['active_restaurants'] }}</div><div class="stat-label">Active</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-hourglass-half"></i></div>
        <div><div class="stat-value">{{ $stats['pending_restaurants'] }}</div><div class="stat-label">Pending Approval</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-receipt"></i></div>
        <div><div class="stat-value">{{ $stats['total_orders'] }}</div><div class="stat-label">Total Orders</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div><div class="stat-value">{{ $stats['total_users'] }}</div><div class="stat-label">Total Users</div></div>
    </div>
</div>

@if($pendingRestaurants->count())
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-clock" style="color:var(--warning)"></i> Pending Approvals</span>
        <a href="{{ route('admin.restaurants.index') }}?status=pending" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Restaurant</th><th>Owner</th><th>Submitted</th><th>Trial Ends</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach($pendingRestaurants as $r)
            <tr>
                <td><strong>{{ $r->name }}</strong><br><span class="text-muted text-small">{{ $r->email }}</span></td>
                <td>{{ $r->owner->name }}</td>
                <td>{{ $r->created_at->diffForHumans() }}</td>
                <td>{{ $r->trial_ends_at?->format('M d, Y') ?? '—' }}</td>
                <td>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.restaurants.approve', $r) }}">@csrf
                            <button class="btn btn-xs btn-success"><i class="fas fa-check"></i> Approve</button>
                        </form>
                        <a href="{{ route('admin.restaurants.show', $r) }}" class="btn btn-xs btn-secondary"><i class="fas fa-eye"></i> Review</a>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-store"></i> All Restaurants</span>
        <a href="{{ route('admin.restaurants.index') }}" class="btn btn-sm btn-secondary">View Full List</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Owner</th><th>Status</th><th>Created</th><th></th></tr></thead>
            <tbody>
            @foreach($recentRestaurants as $r)
            <tr>
                <td><strong>{{ $r->name }}</strong></td>
                <td>{{ $r->owner->name }}</td>
                <td>
                    <span class="badge badge-{{ $r->status === 'active' ? 'success' : ($r->status === 'pending' ? 'warning' : ($r->status === 'rejected' ? 'danger' : 'muted')) }}">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td>{{ $r->created_at->format('M d, Y') }}</td>
                <td><a href="{{ route('admin.restaurants.show', $r) }}" class="btn btn-xs btn-secondary"><i class="fas fa-eye"></i></a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
