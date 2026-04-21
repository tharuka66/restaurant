<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\TableSession;

class BillingService
{
    private float $taxRate = 0.10; // 10% tax

    public function generateOrUpdate(TableSession $session): Bill
    {
        $orders = $session->orders()
            ->whereIn('status', ['completed', 'ready', 'approved', 'preparing'])
            ->with('items.menuItem')
            ->get();

        $subtotal = $orders->sum(fn($order) => $order->totalAmount());
        $taxAmount = round($subtotal * $this->taxRate, 2);
        $total = $subtotal + $taxAmount;

        return Bill::updateOrCreate(
            ['table_session_id' => $session->id],
            [
                'restaurant_id' => $session->restaurant_id,
                'subtotal'      => $subtotal,
                'tax_amount'    => $taxAmount,
                'total_amount'  => $total,
            ]
        );
    }

    public function markPaid(Bill $bill, string $paymentMethod): Bill
    {
        $bill->update([
            'status'         => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at'        => now(),
        ]);
        return $bill;
    }
}
