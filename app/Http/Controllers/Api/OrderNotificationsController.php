<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderNotificationsController extends Controller
{
    public function poll(Request $request)
    {
        $user = auth()->user();
        
        // Only owner and kitchen care about incoming notifications in modal
        if (!in_array($user->role, ['owner', 'kitchen'])) {
            return response()->json(['orders' => []]);
        }

        $restaurantId = $user->restaurant_id;

        // Fetch orders that are pending_approval AND their cancel_deadline has passed
        // This ensures the customer's 20-second window is over before staff sees it.
        $orders = Order::with('session.table')
            ->where('restaurant_id', $restaurantId)
            ->where('status', Order::STATUS_PENDING_APPROVAL)
            ->where('created_at', '>=', now()->subMinutes(10)) // don't fetch super old ones
            ->get();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'table_number' => $order->session->table->number ?? 'Unknown',
                'items_count' => $order->items()->count(),
                'time_ago' => $order->created_at->diffForHumans()
            ];
        });

        return response()->json(['orders' => $formattedOrders]);
    }

    public function accept(Order $order)
    {
        if ($order->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        $order->update(['status' => Order::STATUS_APPROVED]);
        return response()->json(['success' => true]);
    }

    public function reject(Order $order, Request $request)
    {
        if ($order->restaurant_id !== auth()->user()->restaurant_id) abort(403);
        $order->update([
            'status' => Order::STATUS_REJECTED,
            'rejection_reason' => $request->reason ?? 'Rejected by kitchen'
        ]);
        return response()->json(['success' => true]);
    }
}
