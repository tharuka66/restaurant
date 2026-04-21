@extends('layouts.customer')
@section('title', 'Menu')

@push('styles')
<style>
.cat-tabs { display:flex;gap:8px;overflow-x:auto;padding-bottom:8px;margin-bottom:20px;scrollbar-width:none; }
.cat-tabs::-webkit-scrollbar { display:none; }
.cat-tab { white-space:nowrap;padding:8px 16px;border-radius:20px;font-size:.82rem;font-weight:600;cursor:pointer;background:var(--dark-3);color:var(--muted);border:none;transition:all .2s; }
.cat-tab.active { background:var(--primary);color:white; }
.menu-item { display:flex;gap:12px;align-items:center;padding:14px 0;border-bottom:1px solid rgba(51,65,85,.5); }
.menu-item:last-child { border-bottom:none; }
.item-img { width:64px;height:64px;border-radius:10px;object-fit:cover;background:var(--dark-3);flex-shrink:0;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:1.5rem; }
.item-info { flex:1;min-width:0; }
.item-name { font-weight:700;font-size:.95rem;margin-bottom:2px; }
.item-desc { font-size:.75rem;color:var(--muted);margin-bottom:6px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical; }
.item-price { font-weight:800;color:var(--primary); }
.item-prep { font-size:.7rem;color:var(--muted);margin-left:8px; }
.qty-ctrl { display:flex;align-items:center;gap:6px;flex-shrink:0; }
.qty-btn { width:30px;height:30px;border-radius:50%;border:none;cursor:pointer;font-size:1rem;font-weight:700;display:flex;align-items:center;justify-content:center;transition:all .2s; }
.qty-btn.plus { background:var(--primary);color:white; }
.qty-btn.minus { background:var(--dark-3);color:var(--text); }
.qty-val { font-weight:700;min-width:20px;text-align:center; }
.cart-drawer { position:fixed;bottom:0;left:50%;transform:translateX(-50%);width:100%;max-width:480px;background:var(--dark-2);border-top:1px solid var(--border);border-radius:20px 20px 0 0;padding:20px;z-index:300;transition:transform .3s;transform:translateX(-50%) translateY(100%); }
.cart-drawer.open { transform:translateX(-50%) translateY(0); }
.overlay { position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:250;display:none; }
.overlay.open { display:block; }
.cart-item-row { display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(51,65,85,.4);font-size:.85rem; }
.cart-item-row:last-child { border-bottom:none; }
.cancel-countdown { background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:10px 14px;font-size:.82rem;color:var(--danger);display:flex;align-items:center;gap:8px;margin-bottom:12px; }
</style>
@endpush

@section('content')

{{-- Category Tabs --}}
<div class="cat-tabs" id="cat-tabs">
    @foreach($categories as $cat)
    <button class="cat-tab {{ $loop->first ? 'active' : '' }}" onclick="showCat('{{ $cat->id }}', this)">
        {{ $cat->name }} <span style="opacity:.6">({{ $cat->menuItems->count() }})</span>
    </button>
    @endforeach
</div>

{{-- Menu Items --}}
@foreach($categories as $cat)
<div id="cat-{{ $cat->id }}" class="{{ $loop->first ? '' : 'hidden' }}" style="{{ $loop->first ? '' : 'display:none' }}">
    <div class="card">
        <div style="padding:0 16px;">
            @forelse($cat->menuItems as $item)
            <div class="menu-item">
                <div class="item-img">
                    @if($item->image)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:100%;height:100%;border-radius:10px;object-fit:cover;">
                    @else
                        <i class="fas fa-utensils"></i>
                    @endif
                </div>
                <div class="item-info">
                    <div class="item-name">{{ $item->name }}</div>
                    @if($item->description)<div class="item-desc">{{ $item->description }}</div>@endif
                    <div><span class="item-price">${{ number_format($item->price,2) }}</span><span class="item-prep"><i class="fas fa-clock"></i> {{ $item->prep_time_minutes }}m</span></div>
                </div>
                <div class="qty-ctrl">
                    <button class="qty-btn minus" onclick="changeQty({{ $item->id }}, -1, '{{ addslashes($item->name) }}', {{ $item->price }})">−</button>
                    <span class="qty-val" id="qty-{{ $item->id }}">0</span>
                    <button class="qty-btn plus" onclick="changeQty({{ $item->id }}, 1, '{{ addslashes($item->name) }}', {{ $item->price }})">+</button>
                </div>
            </div>
            @empty
            <div style="padding:30px;text-align:center;color:var(--muted);">No items available right now.</div>
            @endforelse
        </div>
    </div>
</div>
@endforeach

{{-- Recent Orders --}}
@if($orders->count())
<h3 style="font-size:.85rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px;margin-top:24px;">Your Orders</h3>
@foreach($orders->whereNotIn('status',['cancelled']) as $order)
<div class="card" style="padding:14px 16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
        <span style="font-weight:700;font-size:.9rem;">Order #{{ $order->id }}</span>
        <span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
    </div>
    @if($order->eta_minutes && in_array($order->status,['approved','preparing']))
    <div style="font-size:.78rem;color:var(--warning);margin-bottom:6px;"><i class="fas fa-clock"></i> Est. {{ $order->eta_minutes }} min</div>
    @endif
    @foreach($order->items as $item)
    <div style="font-size:.8rem;color:var(--muted);padding:2px 0;">{{ $item->quantity }}× {{ $item->menuItem->name }}</div>
    @endforeach
    @if($order->canBeCancelled())
    <button class="btn btn-danger btn-full" style="margin-top:10px;font-size:.82rem;padding:9px;" onclick="cancelOrder({{ $order->id }}, this)">
        <i class="fas fa-times"></i> Cancel Order
    </button>
    @endif
</div>
@endforeach
@endif

{{-- Cart FAB --}}
<button class="cart-fab" id="cart-fab" onclick="openCart()" style="display:none">
    <i class="fas fa-shopping-cart"></i> View Cart
    <span class="cart-badge" id="cart-count">0</span>
</button>

{{-- Overlay --}}
<div class="overlay" id="overlay" onclick="closeCart()"></div>

{{-- Cart Drawer --}}
<div class="cart-drawer" id="cart-drawer">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h3 style="font-size:1rem;font-weight:800;"><i class="fas fa-shopping-cart" style="color:var(--primary)"></i> Your Cart</h3>
        <button onclick="closeCart()" style="background:none;border:none;color:var(--muted);font-size:1.2rem;cursor:pointer;"><i class="fas fa-times"></i></button>
    </div>
    <div id="cart-items-list"></div>
    <div class="divider" style="border-top:1px solid var(--border);margin:12px 0;"></div>
    <div style="display:flex;justify-content:space-between;font-weight:800;font-size:1.1rem;margin-bottom:16px;">
        <span>Total</span><span style="color:var(--primary)" id="cart-total">$0.00</span>
    </div>
    <div class="form-group">
        <label class="form-label">Special Notes</label>
        <textarea id="order-notes" class="form-control" rows="2" placeholder="Any special requests?"></textarea>
    </div>
    <button class="btn btn-primary btn-full" style="font-size:1rem;padding:14px;" onclick="placeOrder()">
        <i class="fas fa-paper-plane"></i> Place Order
    </button>
</div>

@endsection

@push('scripts')
<script>
let cart = {};

function showCat(id, btn) {
    document.querySelectorAll('[id^="cat-"]').forEach(el => el.style.display = 'none');
    document.getElementById('cat-' + id).style.display = 'block';
    document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function changeQty(id, delta, name, price) {
    if (!cart[id]) cart[id] = { name, price, qty: 0 };
    cart[id].qty = Math.max(0, cart[id].qty + delta);
    if (cart[id].qty === 0) delete cart[id];
    document.getElementById('qty-' + id).textContent = cart[id]?.qty || 0;
    updateCartFab();
}

function updateCartFab() {
    const total = Object.values(cart).reduce((s, i) => s + i.qty, 0);
    document.getElementById('cart-count').textContent = total;
    document.getElementById('cart-fab').style.display = total > 0 ? 'flex' : 'none';
}

function openCart() {
    const list = document.getElementById('cart-items-list');
    let html = '', total = 0;
    for (const [id, item] of Object.entries(cart)) {
        const sub = item.qty * item.price;
        total += sub;
        html += `<div class="cart-item-row"><span>${item.qty}× ${item.name}</span><span style="color:var(--primary)">$${sub.toFixed(2)}</span></div>`;
    }
    list.innerHTML = html || '<div style="text-align:center;color:var(--muted);padding:20px;">Cart is empty.</div>';
    document.getElementById('cart-total').textContent = '$' + total.toFixed(2);
    document.getElementById('cart-drawer').classList.add('open');
    document.getElementById('overlay').classList.add('open');
}

function closeCart() {
    document.getElementById('cart-drawer').classList.remove('open');
    document.getElementById('overlay').classList.remove('open');
}

async function placeOrder() {
    if (!Object.keys(cart).length) return alert('Add items to your cart first.');
    const items = Object.entries(cart).map(([id, i]) => ({ menu_item_id: id, quantity: i.qty }));
    const notes = document.getElementById('order-notes').value;
    try {
        const res = await fetch('{{ route("customer.order.place") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ items, notes })
        });
        const data = await res.json();
        if (res.ok) {
            cart = {};
            updateCartFab();
            closeCart();
            document.querySelectorAll('[id^="qty-"]').forEach(el => el.textContent = '0');
            showCancelCountdown(data.order_id, data.cancel_deadline);
            location.reload();
        } else {
            alert(data.message || 'Failed to place order.');
        }
    } catch(e) { alert('Network error. Please try again.'); }
}

async function cancelOrder(orderId, btn) {
    if (!confirm('Cancel this order?')) return;
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
    try {
        const res = await fetch('/table/order/' + orderId + '/cancel', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) location.reload();
        else alert(data.error || 'Cannot cancel.');
    } catch(e) { btn.disabled = false; }
}
</script>
@endpush
