<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Events\NewOrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Jobs\ExpireOrderCancelWindow;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\TableSession;
use App\Services\ETAService;
use Illuminate\Http\Request;

class OrderingController extends Controller
{
    public function __construct(private ETAService $etaService) {}

    /**
     * Customer scans QR → Start/resume session
     */
    public function scan(string $qrToken)
    {
        $table = Table::where('qr_token', $qrToken)->firstOrFail();
        $restaurant = $table->restaurant;

        if (!$restaurant->isActive()) {
            abort(403, 'This restaurant is not currently active.');
        }

        // Store in session
        session(['qr_token' => $qrToken, 'table_id' => $table->id, 'restaurant_id' => $restaurant->id]);

        $activeSession = $table->activeSession;
        if ($activeSession) {
            session(['session_id' => $activeSession->id]);
            return redirect()->route('customer.menu');
        }

        return view('customer.join', compact('table', 'restaurant'));
    }

    public function startSession(Request $request)
    {
        $request->validate(['customer_name' => 'nullable|string|max:100', 'guests' => 'required|integer|min:1']);

        $tableId      = session('table_id');
        $restaurantId = session('restaurant_id');
        $table        = Table::findOrFail($tableId);

        // Check no active session
        if ($table->activeSession) {
            session(['session_id' => $table->activeSession->id]);
            return redirect()->route('customer.menu');
        }

        $session = TableSession::create([
            'restaurant_id' => $restaurantId,
            'table_id'      => $tableId,
            'customer_name' => $request->customer_name ?? 'Guest',
            'guests'        => $request->guests,
            'opened_at'     => now(),
        ]);

        $table->update(['status' => 'occupied']);
        session(['session_id' => $session->id]);

        return redirect()->route('customer.menu');
    }

    public function menu(Request $request)
    {
        $sessionId = session('session_id');
        $tableId   = session('table_id');

        if (!$sessionId) {
            return redirect()->route('customer.scan', session('qr_token', ''));
        }

        $session    = TableSession::with('restaurant.categories.menuItems')->findOrFail($sessionId);
        $restaurant = $session->restaurant;
        $orders     = $session->orders()->with('items.menuItem')->latest()->get();

        $search = $request->query('q');

        // Fetch all categories with items, optionally filtered
        $categoriesQuery = $restaurant->categories()->with(['menuItems' => function($q) use ($search) {
            $q->where('available', true);
            if ($search) {
                $q->where('name', 'like', "%{$search}%");
            }
        }]);

        $categories = $categoriesQuery->get();

        // Latest 4 items
        $latestItems = \App\Models\MenuItem::where('restaurant_id', $restaurant->id)
            ->where('available', true);
        if ($search) {
            $latestItems->where('name', 'like', "%{$search}%");
        }
        $latestItems = $latestItems->latest()->take(4)->get();

        return view('customer.menu', compact('session', 'restaurant', 'categories', 'orders', 'latestItems', 'search'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'items'                     => 'required|array|min:1',
            'items.*.menu_item_id'      => 'required|exists:menu_items,id',
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.special_request'   => 'nullable|string|max:200',
            'notes'                     => 'nullable|string|max:500',
        ]);

        $session = TableSession::findOrFail(session('session_id'));

        $order = Order::create([
            'restaurant_id'    => $session->restaurant_id,
            'table_session_id' => $session->id,
            'status'           => Order::STATUS_PENDING_CANCEL,
            'cancel_deadline'  => now()->addSeconds(20),
            'notes'            => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $menuItem = \App\Models\MenuItem::findOrFail($item['menu_item_id']);
            OrderItem::create([
                'order_id'       => $order->id,
                'menu_item_id'   => $menuItem->id,
                'quantity'       => $item['quantity'],
                'unit_price'     => $menuItem->price,
                'special_request'=> $item['special_request'] ?? null,
            ]);
        }

        // Calculate ETA
        $order->load('items.menuItem');
        $eta = $this->etaService->calculate($order);
        $order->update(['eta_minutes' => $eta]);

        // Dispatch job to expire cancel window after 20s
        ExpireOrderCancelWindow::dispatch($order->id)->delay(now()->addSeconds(20));

        // Broadcast new order to restaurant
        broadcast(new NewOrderPlaced($order));

        if ($request->expectsJson()) {
            return response()->json([
                'order_id'       => $order->id,
                'cancel_deadline'=> $order->cancel_deadline->toISOString(),
                'eta_minutes'    => $eta,
            ]);
        }

        return redirect()->route('customer.menu')->with('success', 'Order placed! You have 20 seconds to cancel.');
    }

    public function cancelOrder(Order $order)
    {
        if (session('session_id') !== $order->table_session_id) {
            abort(403);
        }
        if (!$order->canBeCancelled()) {
            return response()->json(['error' => 'Cancel window has expired.'], 422);
        }
        $order->update(['status' => Order::STATUS_CANCELLED]);
        broadcast(new OrderStatusUpdated($order));

        return response()->json(['success' => true]);
    }

    public function orderStatus()
    {
        $session = TableSession::with([
            'orders' => fn($q) => $q->with('items.menuItem')->latest()
        ])->findOrFail(session('session_id'));

        return view('customer.order-status', compact('session'));
    }
}
