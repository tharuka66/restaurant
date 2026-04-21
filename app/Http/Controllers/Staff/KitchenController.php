<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class KitchenController extends Controller
{
    public function index()
    {
        $restaurant = Auth::user()->restaurant;
        $orders = $restaurant->orders()
            ->with('session.table', 'items.menuItem')
            ->whereIn('status', [
                Order::STATUS_APPROVED,
                Order::STATUS_PREPARING,
                Order::STATUS_READY,
            ])
            ->latest()
            ->get();
        return view('staff.kitchen', compact('restaurant', 'orders'));
    }

    public function updateStatus(Order $order, string $status)
    {
        abort_if($order->restaurant_id !== Auth::user()->restaurant_id, 403);

        $allowed = [Order::STATUS_PREPARING, Order::STATUS_READY, Order::STATUS_COMPLETED];
        abort_unless(in_array($status, $allowed), 422);

        $order->update(['status' => $status]);
        broadcast(new OrderStatusUpdated($order))->toOthers();

        if (request()->expectsJson()) {
            return response()->json(['status' => $status, 'label' => $order->statusLabel()]);
        }
        return back()->with('success', 'Order status updated.');
    }
}
