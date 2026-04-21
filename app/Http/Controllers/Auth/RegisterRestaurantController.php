<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterRestaurantController extends Controller
{
    public function create()
    {
        return view('auth.register-restaurant');
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_name'  => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:8|confirmed',
            'restaurant_name' => 'required|string|max:200',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:500',
        ]);

        $owner = User::create([
            'name'     => $request->owner_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'owner',
        ]);

        $restaurant = Restaurant::create([
            'name'     => $request->restaurant_name,
            'slug'     => Str::slug($request->restaurant_name) . '-' . Str::random(4),
            'owner_id' => $owner->id,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'status'   => 'pending',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $owner->update(['restaurant_id' => $restaurant->id]);

        Auth::login($owner);
        return redirect()->route('owner.pending')->with('success', 'Registration submitted! Awaiting admin approval.');
    }
}
