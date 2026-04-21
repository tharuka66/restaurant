<div class="kitchen-card" id="order-card-{{ $order->id }}">
    <div class="kitchen-card-header">
        <span class="kitchen-card-table">Table {{ $order->session->table->number }}</span>
        <span class="kitchen-card-time">{{ $order->created_at->diffForHumans() }}</span>
    </div>
    @if($order->notes)
        <div style="font-size:.78rem;color:var(--warning);margin-bottom:6px;"><i class="fas fa-sticky-note"></i> {{ $order->notes }}</div>
    @endif
    <div>
        @foreach($order->items as $item)
        <div class="kitchen-item">
            <span>{{ $item->quantity }}× {{ $item->menuItem->name }}</span>
            @if($item->special_request)<span class="text-muted text-small">{{ $item->special_request }}</span>@endif
        </div>
        @endforeach
    </div>
    <div class="kitchen-card-actions">
        @if($order->status === 'approved')
            <button class="btn btn-sm btn-warning" style="flex:1" onclick="updateOrderStatus({{ $order->id }}, 'preparing', this)">
                <i class="fas fa-fire"></i> Start Preparing
            </button>
        @elseif($order->status === 'preparing')
            <button class="btn btn-sm btn-success" style="flex:1" onclick="updateOrderStatus({{ $order->id }}, 'ready', this)">
                <i class="fas fa-bell"></i> Mark Ready
            </button>
        @elseif($order->status === 'ready')
            <button class="btn btn-sm btn-secondary" style="flex:1" onclick="updateOrderStatus({{ $order->id }}, 'completed', this)">
                <i class="fas fa-check"></i> Complete
            </button>
        @endif
    </div>
</div>
