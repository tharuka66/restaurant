<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Events\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private function restaurant() { return Auth::user()->ownedRestaurant; }

    public function index()
    {
        $restaurant = $this->restaurant();
        $orders = $restaurant->orders()
            ->with('session.table', 'items.menuItem')
            ->orderByRaw("FIELD(status, 'pending_approval', 'approved', 'preparing', 'ready', 'completed', 'rejected', 'cancelled')")
            ->latest()
            ->paginate(20);
        return view('owner.orders.index', compact('restaurant', 'orders'));
    }

    public function approve(Request $request, Order $order)
    {
        abort_if($order->restaurant_id !== $this->restaurant()->id, 403);

        $etaMinutes = $request->input('eta_minutes', null);
        $order->update([
            'status'      => Order::STATUS_APPROVED,
            'eta_minutes' => $etaMinutes,
        ]);
        broadcast(new OrderStatusUpdated($order));
        return back()->with('success', 'Order approved.');
    }

    public function reject(Request $request, Order $order)
    {
        abort_if($order->restaurant_id !== $this->restaurant()->id, 403);
        $request->validate(['reason' => 'required|string|max:500']);
        $order->update([
            'status'           => Order::STATUS_REJECTED,
            'rejection_reason' => $request->reason,
        ]);
        broadcast(new OrderStatusUpdated($order));
        return back()->with('success', 'Order rejected.');
    }
}
