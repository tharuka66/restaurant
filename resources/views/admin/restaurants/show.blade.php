@extends('layouts.app')
@section('title', $restaurant->name . ' — Admin')
@section('panel-name', 'Super Admin')
@section('page-title', $restaurant->name)
@section('page-subtitle', 'Restaurant Details')

@section('sidebar-nav')
<div class="nav-section">Main</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
<a href="{{ route('admin.restaurants.index') }}" class="nav-item active"><i class="fas fa-store"></i> Restaurants</a>
@endsection

@section('topbar-actions')
<a href="{{ route('admin.restaurants.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
@if($restaurant->status === 'pending')
    <form method="POST" action="{{ route('admin.restaurants.approve', $restaurant) }}" style="display:inline">@csrf
        <button class="btn btn-success btn-sm"><i class="fas fa-check"></i> Approve</button>
    </form>
@elseif($restaurant->status === 'active')
    <form method="POST" action="{{ route('admin.restaurants.suspend', $restaurant) }}" style="display:inline">@csrf
        <button class="btn btn-warning btn-sm"><i class="fas fa-ban"></i> Suspend</button>
    </form>
@elseif($restaurant->status === 'suspended')
    <form method="POST" action="{{ route('admin.restaurants.restore', $restaurant) }}" style="display:inline">@csrf
        <button class="btn btn-success btn-sm"><i class="fas fa-redo"></i> Restore</button>
    </form>
@endif
@endsection

@section('content')
<div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <div class="card" style="margin-bottom:0">
        <div class="card-header"><span class="card-title">Restaurant Info</span>
            <span class="badge badge-{{ $restaurant->status === 'active' ? 'success' : ($restaurant->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($restaurant->status) }}</span>
        </div>
        <div class="card-body">
            <div style="display:grid;gap:12px;">
                <div><span class="text-muted text-small">Owner</span><div style="font-weight:600">{{ $restaurant->owner->name }}</div></div>
                <div><span class="text-muted text-small">Email</span><div>{{ $restaurant->email ?? '—' }}</div></div>
                <div><span class="text-muted text-small">Phone</span><div>{{ $restaurant->phone ?? '—' }}</div></div>
                <div><span class="text-muted text-small">Address</span><div>{{ $restaurant->address ?? '—' }}</div></div>
                <div><span class="text-muted text-small">Registered</span><div>{{ $restaurant->created_at->format('M d, Y') }}</div></div>
                <div><span class="text-muted text-small">Trial Ends</span><div>{{ $restaurant->trial_ends_at?->format('M d, Y') ?? '—' }}</div></div>
            </div>
            @if($restaurant->rejection_reason)
                <div class="alert alert-danger mt-4"><i class="fas fa-info-circle"></i> {{ $restaurant->rejection_reason }}</div>
            @endif
        </div>
    </div>
    <div style="display:grid;gap:16px;">
        <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-tags"></i></div><div><div class="stat-value">{{ $restaurant->categories->count() }}</div><div class="stat-label">Menu Categories</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fas fa-utensils"></i></div><div><div class="stat-value">{{ $restaurant->categories->sum(fn($c)=>$c->allItems->count()) }}</div><div class="stat-label">Menu Items</div></div></div>
        <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-chair"></i></div><div><div class="stat-value">{{ $restaurant->rooms->sum(fn($r)=>$r->tables->count()) }}</div><div class="stat-label">Tables</div></div></div>
        <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-users"></i></div><div><div class="stat-value">{{ $restaurant->staff->count() }}</div><div class="stat-label">Staff Members</div></div></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title"><i class="fas fa-receipt"></i> Recent Orders</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order</th><th>Table</th><th>Items</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($restaurant->orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->session->table->number ?? '—' }}</td>
                <td>{{ $order->items->count() }} items</td>
                <td><span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span></td>
                <td>{{ $order->created_at->format('M d, H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px;">No orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title"><i class="fas fa-users"></i> Staff</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
            <tbody>
            @forelse($restaurant->staff as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->email }}</td>
                <td><span class="badge badge-info">{{ ucfirst($s->role) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:30px;">No staff added.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
