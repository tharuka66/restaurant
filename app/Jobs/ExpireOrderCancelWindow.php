<?php

namespace App\Jobs;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireOrderCancelWindow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::find($this->orderId);
        if (!$order) return;

        // Only move to pending_approval if still in cancel window
        if ($order->status === Order::STATUS_PENDING_CANCEL) {
            $order->update(['status' => Order::STATUS_PENDING_APPROVAL]);
            broadcast(new OrderStatusUpdated($order));
        }
    }
}
