@extends('layouts.app')
@section('title', 'Kitchen Dashboard')
@section('panel-name', 'Kitchen')
@section('page-title', 'Kitchen Dashboard')
@section('page-subtitle', $restaurant->name . ' · Real-time Orders')

@section('sidebar-nav')
<div class="nav-section">Kitchen</div>
<a href="{{ route('kitchen.index') }}" class="nav-item active"><i class="fas fa-fire"></i> Live Orders</a>
@endsection

@section('topbar-actions')
<span id="live-indicator" style="display:flex;align-items:center;gap:6px;font-size:.8rem;color:var(--success);">
    <span style="width:8px;height:8px;border-radius:50%;background:var(--success);animation:pulse 1.5s infinite;display:inline-block;"></span> Live
</span>
@endsection

@section('content')

@push('styles')
<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
.order-col { display:flex;flex-direction:column;gap:12px; }
.order-board { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; }
.board-col { background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden; }
.board-col-header { padding:14px 18px;font-weight:700;font-size:.9rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px; }
.board-col-body { padding:12px;min-height:200px; }
.kitchen-card { background:var(--dark-2);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px;transition:box-shadow .2s; }
.kitchen-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.3); }
.kitchen-card-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:8px; }
.kitchen-card-table { font-size:1.1rem;font-weight:800;color:var(--primary); }
.kitchen-card-time { font-size:.72rem;color:var(--muted); }
.kitchen-item { display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid rgba(51,65,85,.4);font-size:.83rem; }
.kitchen-item:last-child { border-bottom:none; }
.kitchen-card-actions { margin-top:10px;display:flex;gap:6px; }
</style>
@endpush

<div class="order-board" id="order-board">
    {{-- Approved --}}
    <div class="board-col">
        <div class="board-col-header" style="color:var(--info)"><i class="fas fa-check-circle"></i> Approved <span class="badge badge-info" id="count-approved">{{ $orders->where('status','approved')->count() }}</span></div>
        <div class="board-col-body" id="col-approved">
            @foreach($orders->where('status','approved') as $order)
                @include('staff._kitchen_card', ['order' => $order])
            @endforeach
        </div>
    </div>
    {{-- Preparing --}}
    <div class="board-col">
        <div class="board-col-header" style="color:var(--warning)"><i class="fas fa-fire"></i> Preparing <span class="badge badge-warning" id="count-preparing">{{ $orders->where('status','preparing')->count() }}</span></div>
        <div class="board-col-body" id="col-preparing">
            @foreach($orders->where('status','preparing') as $order)
                @include('staff._kitchen_card', ['order' => $order])
            @endforeach
        </div>
    </div>
    {{-- Ready --}}
    <div class="board-col">
        <div class="board-col-header" style="color:var(--success)"><i class="fas fa-bell"></i> Ready <span class="badge badge-success" id="count-ready">{{ $orders->where('status','ready')->count() }}</span></div>
        <div class="board-col-body" id="col-ready">
            @foreach($orders->where('status','ready') as $order)
                @include('staff._kitchen_card', ['order' => $order])
            @endforeach
        </div>
    </div>
</div>

@if($orders->isEmpty())
<div style="text-align:center;padding:80px;color:var(--muted)">
    <i class="fas fa-check-double" style="font-size:3rem;display:block;margin-bottom:16px;color:var(--success)"></i>
    <h3>All caught up!</h3>
    <p>No active orders right now. Waiting for new orders...</p>
</div>
@endif

@endsection

@push('scripts')
<script>
async function updateOrderStatus(orderId, status, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    try {
        const res = await fetch(`/kitchen/orders/${orderId}/status/${status}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
        });
        if (res.ok) { setTimeout(() => location.reload(), 300); }
        else { btn.disabled = false; btn.innerHTML = 'Error'; }
    } catch(e) { btn.disabled = false; }
}
</script>
@endpush
