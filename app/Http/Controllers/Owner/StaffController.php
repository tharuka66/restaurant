<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    private function restaurant() { return Auth::user()->ownedRestaurant; }

    public function index()
    {
        $restaurant = $this->restaurant();
        $staff = $restaurant->staff()->latest()->get();
        return view('owner.staff.index', compact('restaurant', 'staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|in:kitchen,cashier',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'role'          => $data['role'],
            'restaurant_id' => $this->restaurant()->id,
            'password'      => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Staff member added.');
    }

    public function destroy(User $user)
    {
        if ($user->restaurant_id !== $this->restaurant()->id) abort(403);
        $user->delete();
        return back()->with('success', 'Staff member removed.');
    }
}
