@extends('layouts.customer')
@section('title', 'Order Status')

@push('styles')
<style>
.preparing-anim { display:flex; align-items:center; gap:10px; background:rgba(249,115,22,.1); padding:12px; border-radius:8px; border:1px solid rgba(249,115,22,.2); color:var(--primary); font-weight:700; margin-bottom:12px; }
.bounce { animation:bounce 1s infinite alternate; font-size:1.4rem; }
@keyframes bounce { 0% { transform: translateY(0); } 100% { transform: translateY(-3px); } }
</style>
@endpush

@section('content')
<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
    <a href="{{ route('customer.menu') }}" style="color:var(--muted);text-decoration:none;"><i class="fas fa-arrow-left"></i></a>
    <h1 style="font-size:1.1rem;font-weight:800;">My Orders</h1>
</div>

@forelse($session->orders->whereNotIn('status',['cancelled','rejected']) as $order)
<div class="card" style="overflow:visible;">
    <div style="padding:16px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <strong>Order #{{ $order->id }}</strong>
        <span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
    </div>
    <div style="padding:14px 18px;">
        @if($order->eta_minutes && !in_array($order->status,['completed','rejected','cancelled']))
        <div style="background:rgba(249,115,22,.1);border:1px solid rgba(249,115,22,.2);border-radius:8px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:8px;font-size:.83rem;">
            <i class="fas fa-clock" style="color:var(--primary)"></i>
            <span>Estimated ready in <strong style="color:var(--primary)">{{ $order->eta_minutes }} minutes</strong></span>
        </div>
        @endif

        @if($order->rejection_reason)
        <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:10px 14px;margin-bottom:12px;font-size:.82rem;color:var(--danger);">
            <i class="fas fa-info-circle"></i> {{ $order->rejection_reason }}
        </div>
        @endif

        @if($order->status === 'preparing')
        <div class="preparing-anim">
            <i class="fas fa-fire bounce"></i>
            <div>Food is being prepared! We're making it delicious.</div>
        </div>
        @endif

        {{-- Progress bar --}}
        @php
            $steps = ['pending_approval'=>1,'approved'=>2,'preparing'=>3,'ready'=>4,'completed'=>5];
            $current = $steps[$order->status] ?? 0;
        @endphp
        @if($current > 0)
        <div style="display:flex;gap:4px;margin-bottom:14px;">
            @foreach(['Confirmed','Approved','Preparing','Ready','Done'] as $i=>$label)
            <div style="flex:1;text-align:center;">
                <div style="height:4px;border-radius:2px;background:{{ ($i+1)<=$current ? 'var(--primary)' : 'var(--dark-3)' }};margin-bottom:4px;"></div>
                <div style="font-size:.62rem;color:{{ ($i+1)<=$current ? (($order->status === 'preparing' && ($i+1)==3) ? 'var(--primary); font-weight:bold; animation:pulse 1s infinite;' : 'var(--primary)') : 'var(--muted)' }}">{{ $label }}</div>
            </div>
            @endforeach
        </div>
        @endif

        @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:.85rem;border-bottom:1px solid rgba(51,65,85,.4);">
            <span>{{ $item->quantity }}× {{ $item->menuItem->name }}
                @if($item->special_request)<span style="font-size:.72rem;color:var(--muted);"> ({{ $item->special_request }})</span>@endif
            </span>
            <span style="color:var(--primary)">${{ number_format($item->subtotal(),2) }}</span>
        </div>
        @endforeach

        <div style="display:flex;justify-content:space-between;font-weight:800;margin-top:10px;">
            <span>Order Total</span><span style="color:var(--primary)">${{ number_format($order->totalAmount(),2) }}</span>
        </div>

        @if($order->canBeCancelled())
        <div class="cancel-timer-container" data-order-id="{{ $order->id }}" data-deadline="{{ $order->cancel_deadline->timestamp }}" style="margin-top:12px;">
            <button class="btn btn-danger btn-full" style="font-size:.85rem;" onclick="cancelOrder({{ $order->id }}, this)">
                <i class="fas fa-times"></i> Cancel Order (<span class="sec-left">20</span>s)
            </button>
        </div>
        @endif
    </div>
</div>
@empty
<div style="text-align:center;padding:60px 20px;color:var(--muted);">
    <i class="fas fa-receipt" style="font-size:3rem;display:block;margin-bottom:14px;"></i>
    <h3>No orders yet</h3>
    <p style="margin-top:6px;font-size:.85rem;">Browse the menu and place your first order.</p>
    <a href="{{ route('customer.menu') }}" class="btn btn-primary" style="margin-top:20px;"><i class="fas fa-utensils"></i> View Menu</a>
</div>
@endforelse
@endsection

@push('scripts')
<script>
async function cancelOrder(orderId, btn) {
    if (!confirm('Cancel this order?')) return;
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    const res = await fetch('/table/order/' + orderId + '/cancel', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.success) location.reload();
    else { alert(data.error || 'Cannot cancel.'); btn.disabled = false; }
}
// Cancel timer logic
const timers = document.querySelectorAll('.cancel-timer-container');
setInterval(() => {
    const now = Math.floor(Date.now() / 1000);
    timers.forEach(t => {
        const deadline = parseInt(t.dataset.deadline);
        let left = deadline - now;
        if(left < 0) left = 0;
        const span = t.querySelector('.sec-left');
        const btn = t.querySelector('.btn-danger');
        if (span) span.textContent = left;
        if (left <= 0) {
            if (btn) btn.disabled = true;
            t.style.opacity = '0.5';
        }
    });
}, 1000);

// Auto-refresh every 15 seconds
setTimeout(() => location.reload(), 15000);
</script>
@endpush
