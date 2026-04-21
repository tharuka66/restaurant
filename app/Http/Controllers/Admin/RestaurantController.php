<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::with('owner')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $restaurants = $query->paginate(15);
        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->load([
            'owner', 'rooms.tables', 'categories.allItems',
            'orders' => fn($q) => $q->latest()->take(20),
            'staff',
        ]);
        return view('admin.restaurants.show', compact('restaurant'));
    }

    public function approve(Restaurant $restaurant)
    {
        $restaurant->update(['status' => 'active', 'rejection_reason' => null]);
        return back()->with('success', "Restaurant '{$restaurant->name}' approved successfully.");
    }

    public function reject(Request $request, Restaurant $restaurant)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $restaurant->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);
        return back()->with('success', "Restaurant '{$restaurant->name}' rejected.");
    }

    public function suspend(Restaurant $restaurant)
    {
        $restaurant->update(['status' => 'suspended']);
        return back()->with('success', "Restaurant '{$restaurant->name}' suspended.");
    }

    public function restore(Restaurant $restaurant)
    {
        $restaurant->update(['status' => 'active']);
        return back()->with('success', "Restaurant '{$restaurant->name}' restored.");
    }

    public function extendTrial(Request $request, Restaurant $restaurant)
    {
        $request->validate(['days' => 'required|integer|min:1|max:90']);
        $newDate = ($restaurant->trial_ends_at ?? now())->addDays($request->days);
        $restaurant->update(['trial_ends_at' => $newDate]);
        return back()->with('success', "Trial extended to {$newDate->format('M d, Y')}.");
    }
}
