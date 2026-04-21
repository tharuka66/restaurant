<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TableSession;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $restaurant = Auth::user()->ownedRestaurant;
        if (!$restaurant) {
            return redirect()->route('owner.register-restaurant');
        }

        $stats = [
            'active_sessions'  => $restaurant->sessions()->where('status', 'active')->count(),
            'pending_orders'   => $restaurant->orders()->whereIn('status', ['pending_approval'])->count(),
            'preparing_orders' => $restaurant->orders()->where('status', 'preparing')->count(),
            'today_revenue'    => $restaurant->bills()
                ->where('status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('total_amount'),
            'today_orders'     => $restaurant->orders()->whereDate('created_at', today())->count(),
        ];

        $recentOrders = $restaurant->orders()
            ->with('session.table', 'items.menuItem')
            ->latest()
            ->take(10)
            ->get();

        return view('owner.dashboard', compact('restaurant', 'stats', 'recentOrders'));
    }

    public function pending()
    {
        $restaurant = Auth::user()->ownedRestaurant;
        return view('owner.pending', compact('restaurant'));
    }

    public function updateSettings(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'service_charge_percent' => 'numeric|min:0|max:100',
            'tax_percent' => 'numeric|min:0|max:100',
            'discount_percent' => 'numeric|min:0|max:100',
            'vat_percent' => 'numeric|min:0|max:100',
        ]);

        $restaurant = Auth::user()->ownedRestaurant;
        $restaurant->update($request->only([
            'service_charge_percent', 'tax_percent', 'discount_percent', 'vat_percent'
        ]));

        return back()->with('success', 'Billing settings updated!');
    }

    public function registerRestaurant()
    {
        return view('owner.register-restaurant');
    }
}
