<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; }
    .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 16px; margin-bottom: 16px; }
    .restaurant-name { font-size: 1.3rem; font-weight: 800; }
    .table-info { color: #666; font-size: .85rem; margin-top: 4px; }
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .items-table th { text-align: left; font-size: .75rem; text-transform: uppercase; color: #666; border-bottom: 1px solid #ccc; padding: 6px 0; }
    .items-table td { padding: 6px 0; font-size: .85rem; border-bottom: 1px solid #eee; }
    .totals { border-top: 2px solid #333; padding-top: 12px; }
    .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: .9rem; }
    .total-row.grand { font-weight: 800; font-size: 1.1rem; }
    .footer { text-align: center; margin-top: 20px; font-size: .75rem; color: #999; border-top: 1px dashed #ccc; padding-top: 12px; }
    .paid-stamp { text-align: center; border: 3px solid green; color: green; font-size: 1.5rem; font-weight: 900; padding: 8px 20px; display: inline-block; transform: rotate(-10deg); margin: 10px auto; border-radius: 4px; }
</style>
</head>
<body>
<div class="header">
    <div class="restaurant-name">{{ $session->restaurant->name }}</div>
    <div class="table-info">Table {{ $session->table->number }} · {{ $session->customer_name }}</div>
    <div class="table-info">{{ now()->format('M d, Y H:i') }}</div>
</div>

<table class="items-table">
    <thead><tr><th>Item</th><th>Qty</th><th style="text-align:right">Amount</th></tr></thead>
    <tbody>
    @foreach($session->orders->whereNotIn('status',['cancelled','rejected']) as $order)
        @foreach($order->items as $item)
        <tr>
            <td>{{ $item->menuItem->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td style="text-align:right">${{ number_format($item->subtotal(),2) }}</td>
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>

<div class="totals">
    <div class="total-row"><span>Subtotal</span><span>${{ number_format($bill->subtotal,2) }}</span></div>
    <div class="total-row"><span>Tax (10%)</span><span>${{ number_format($bill->tax_amount,2) }}</span></div>
    <div class="total-row grand"><span>TOTAL</span><span>${{ number_format($bill->total_amount,2) }}</span></div>
    @if($bill->payment_method)
    <div class="total-row"><span>Payment</span><span>{{ ucfirst($bill->payment_method) }}</span></div>
    @endif
</div>

@if($bill->isPaid())
<div style="text-align:center;margin-top:16px;">
    <div class="paid-stamp">✓ PAID</div>
</div>
@endif

<div class="footer">
    Thank you for dining with us!<br>
    {{ $session->restaurant->name }} · {{ $session->restaurant->phone }}<br>
    Bill #{{ $bill->id }} · Session #{{ $session->id }}
</div>
</body>
</html>
