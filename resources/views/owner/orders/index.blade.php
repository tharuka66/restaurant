@extends('layouts.app')
@section('title', 'Orders')
@section('panel-name', 'Restaurant Owner')
@section('page-title', 'Order Management')

@section('sidebar-nav')
<div class="nav-section">Home</div>
<a href="{{ route('owner.dashboard') }}" class="nav-item"><i class="fas fa-chart-bar"></i> Dashboard</a>
<div class="nav-section">Manage</div>
<a href="{{ route('owner.menu.index') }}" class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
<a href="{{ route('owner.tables.index') }}" class="nav-item"><i class="fas fa-chair"></i> Rooms & Tables</a>
<a href="{{ route('owner.staff.index') }}" class="nav-item"><i class="fas fa-users"></i> Staff</a>
<a href="{{ route('owner.orders.index') }}" class="nav-item active"><i class="fas fa-receipt"></i> Orders</a>
@endsection

@section('content')
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Table</th><th>Items</th><th>Total</th><th>Status</th><th>ETA</th><th>Time</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
            <tr>
                <td><strong>#{{ $order->id }}</strong></td>
                <td>T{{ $order->session->table->number ?? '—' }}</td>
                <td>
                    @foreach($order->items->take(2) as $item)
                        <div class="text-small">{{ $item->quantity }}× {{ $item->menuItem->name }}</div>
                    @endforeach
                    @if($order->items->count() > 2)<div class="text-muted text-small">+{{ $order->items->count()-2 }} more</div>@endif
                </td>
                <td><strong style="color:var(--primary)">${{ number_format($order->totalAmount(),2) }}</strong></td>
                <td><span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span></td>
                <td>{{ $order->eta_minutes ? $order->eta_minutes.' min' : '—' }}</td>
                <td>{{ $order->created_at->diffForHumans() }}</td>
                <td>
                    @if($order->status === 'pending_approval')
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('owner.orders.approve', $order) }}">@csrf
                            <input type="hidden" name="eta_minutes" value="{{ $order->eta_minutes }}">
                            <button class="btn btn-xs btn-success"><i class="fas fa-check"></i> Approve</button>
                        </form>
                        <button class="btn btn-xs btn-danger" onclick="openReject({{ $order->id }})"><i class="fas fa-times"></i></button>
                    </div>
                    @else
                    <span class="text-muted text-small">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">No orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $orders->links() }}</div>

@foreach($orders->where('status','pending_approval') as $order)
<div class="modal-overlay" id="reject-order-{{ $order->id }}">
    <div class="modal">
        <div class="modal-header">Reject Order #{{ $order->id }}
            <button class="close-btn" onclick="document.getElementById('reject-order-{{ $order->id }}').classList.remove('open')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('owner.orders.reject', $order) }}">@csrf
            <div class="form-group"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="3" required placeholder="e.g. Item out of stock..."></textarea></div>
            <button class="btn btn-danger" style="width:100%">Confirm Rejection</button>
        </form>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
function openReject(id) { document.getElementById('reject-order-' + id).classList.add('open'); }
document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); }));
</script>
@endpush
