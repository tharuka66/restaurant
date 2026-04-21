<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRestaurantActive
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();
        if ($user && $user->isOwner()) {
            $restaurant = $user->ownedRestaurant;
            if (!$restaurant || !$restaurant->isActive()) {
                return redirect()->route('owner.pending');
            }
        }
        return $next($request);
    }
}
