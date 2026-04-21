@extends('layouts.app')
@section('title', 'Owner Dashboard')
@section('panel-name', 'Restaurant Owner')
@section('page-title', $restaurant->name)
@section('page-subtitle', 'Welcome back, ' . Auth::user()->name)

@section('sidebar-nav')
<div class="nav-section">Home</div>
<a href="{{ route('owner.dashboard') }}" class="nav-item active"><i class="fas fa-chart-bar"></i> Dashboard</a>
<div class="nav-section">Manage</div>
<a href="{{ route('owner.menu.index') }}" class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
<a href="{{ route('owner.tables.index') }}" class="nav-item"><i class="fas fa-chair"></i> Rooms & Tables</a>
<a href="{{ route('owner.staff.index') }}" class="nav-item"><i class="fas fa-users"></i> Staff</a>
<a href="{{ route('owner.orders.index') }}" class="nav-item"><i class="fas fa-receipt"></i> Orders</a>
@endsection

@section('content')
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-door-open"></i></div>
        <div><div class="stat-value">{{ $stats['active_sessions'] }}</div><div class="stat-label">Active Sessions</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
        <div><div class="stat-value">{{ $stats['pending_orders'] }}</div><div class="stat-label">Pending Orders</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-fire"></i></div>
        <div><div class="stat-value">{{ $stats['preparing_orders'] }}</div><div class="stat-label">Preparing</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-dollar-sign"></i></div>
        <div><div class="stat-value">${{ number_format($stats['today_revenue'], 2) }}</div><div class="stat-label">Today's Revenue</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-receipt"></i></div>
        <div><div class="stat-value">{{ $stats['today_orders'] }}</div><div class="stat-label">Today's Orders</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-receipt"></i> Recent Orders</span>
        <a href="{{ route('owner.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order #</th><th>Table</th><th>Items</th><th>Total</th><th>Status</th><th>Time</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($recentOrders as $order)
            <tr>
                <td><strong>#{{ $order->id }}</strong></td>
                <td>{{ $order->session->table->number ?? '—' }}</td>
                <td>{{ $order->items->count() }} items · ${{ number_format($order->totalAmount(), 2) }}</td>
                <td>${{ number_format($order->totalAmount(), 2) }}</td>
                <td><span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span></td>
                <td>{{ $order->created_at->diffForHumans() }}</td>
                <td>
                    @if($order->status === 'pending_approval')
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('owner.orders.approve', $order) }}">@csrf
                            <button class="btn btn-xs btn-success"><i class="fas fa-check"></i> Approve</button>
                        </form>
                        <button class="btn btn-xs btn-danger" onclick="openRejectOrder({{ $order->id }})"><i class="fas fa-times"></i></button>
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">No orders yet today.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($recentOrders->where('status','pending_approval') as $order)
<div class="modal-overlay" id="reject-order-{{ $order->id }}">
    <div class="modal">
        <div class="modal-header">Reject Order #{{ $order->id }}
            <button class="close-btn" onclick="document.getElementById('reject-order-{{ $order->id }}').classList.remove('open')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('owner.orders.reject', $order) }}">
            @csrf
            <div class="form-group"><label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="3" required placeholder="e.g. Out of stock..."></textarea>
            </div>
            <button class="btn btn-danger" style="width:100%">Reject Order</button>
        </form>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
function openRejectOrder(id) { document.getElementById('reject-order-' + id).classList.add('open'); }
document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); }));
</script>
@endpush
