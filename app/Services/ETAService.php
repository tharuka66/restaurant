<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TableSession;

class ETAService
{
    /**
     * Calculate estimated preparation time for a new order.
     * Takes the max item prep time + 5 minute buffer.
     */
    public function calculate(Order $order): int
    {
        $maxPrepTime = $order->items->max(fn($item) => $item->menuItem->prep_time_minutes ?? 15);
        return $maxPrepTime + 5;
    }
}
