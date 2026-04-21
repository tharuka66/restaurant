@extends('layouts.app')
@section('title', 'Checkout — Table ' . $session->table->number)
@section('panel-name', 'Cashier')
@section('page-title', 'Checkout — Table ' . $session->table->number)

@section('sidebar-nav')
<div class="nav-section">Cashier</div>
<a href="{{ route('cashier.index') }}" class="nav-item"><i class="fas fa-cash-register"></i> Active Sessions</a>
@endsection

@section('topbar-actions')
<a href="{{ route('cashier.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
@if(!$bill->isPaid())
<a href="{{ route('cashier.bill.print', $session) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print Bill</a>
@endif
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start;">
    {{-- Orders --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-list"></i> Orders</span></div>
            <div class="card-body" style="padding:0;">
                @foreach($session->orders->whereNotIn('status',['cancelled','rejected']) as $order)
                <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
                    <div class="flex" style="justify-content:space-between;margin-bottom:8px;">
                        <strong style="font-size:.9rem;">Order #{{ $order->id }}</strong>
                        <span class="badge badge-{{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
                    </div>
                    @foreach($order->items as $item)
                    <div class="flex" style="justify-content:space-between;padding:4px 0;font-size:.83rem;border-bottom:1px solid rgba(51,65,85,.3);">
                        <span>{{ $item->quantity }}× {{ $item->menuItem->name }}</span>
                        <span style="color:var(--primary)">${{ number_format($item->subtotal(), 2) }}</span>
                    </div>
                    @endforeach
                    <div class="flex" style="justify-content:flex-end;margin-top:6px;font-size:.85rem;color:var(--muted);">
                        Order total: <strong style="margin-left:6px;color:var(--text)">${{ number_format($order->totalAmount(), 2) }}</strong>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bill --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-file-invoice-dollar"></i> Bill Summary</span></div>
            <div class="card-body">
                <div style="display:grid;gap:10px;margin-bottom:16px;">
                    <div class="flex" style="justify-content:space-between;"><span class="text-muted">Subtotal</span><span>${{ number_format($bill->subtotal,2) }}</span></div>
                    <div class="flex" style="justify-content:space-between;"><span class="text-muted">Tax (10%)</span><span>${{ number_format($bill->tax_amount,2) }}</span></div>
                    <hr class="divider" style="margin:4px 0;">
                    <div class="flex" style="justify-content:space-between;font-size:1.3rem;font-weight:800;">
                        <span>Total</span><span style="color:var(--primary)">${{ number_format($bill->total_amount,2) }}</span>
                    </div>
                </div>

                @if($bill->isPaid())
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> Paid via {{ ucfirst($bill->payment_method) }} on {{ $bill->paid_at->format('M d, Y H:i') }}</div>
                @else
                    <form method="POST" action="{{ route('cashier.pay', $session) }}">
                        @csrf
                        <div class="form-group"><label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">💵 Cash</option>
                                <option value="card">💳 Card</option>
                                <option value="online">📱 Online</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" style="width:100%;font-size:1rem;padding:14px;" onclick="return confirm('Mark bill as paid and close session?')">
                            <i class="fas fa-check-circle"></i> Confirm Payment & Close
                        </button>
                    </form>
                @endif

                <div class="divider"></div>
                <div style="font-size:.75rem;color:var(--muted);text-align:center;">
                    Session opened {{ $session->opened_at->format('H:i') }}<br>
                    {{ $session->customer_name }} · {{ $session->guests }} guest(s)
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
