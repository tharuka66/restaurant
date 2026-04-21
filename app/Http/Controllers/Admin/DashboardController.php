<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_restaurants' => Restaurant::count(),
            'active_restaurants' => Restaurant::where('status', 'active')->count(),
            'pending_restaurants' => Restaurant::where('status', 'pending')->count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
        ];

        $pendingRestaurants = Restaurant::with('owner')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentRestaurants = Restaurant::with('owner')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'pendingRestaurants', 'recentRestaurants'));
    }
}
