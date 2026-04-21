<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    private function restaurant() { return Auth::user()->ownedRestaurant; }

    // --- Categories ---
    public function index()
    {
        $restaurant = $this->restaurant();
        $categories = $restaurant->categories()->with('allItems')->get();
        return view('owner.menu.index', compact('restaurant', 'categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'sort_order' => 'integer']);
        $this->restaurant()->categories()->create($data);
        return back()->with('success', 'Category added.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        $data = $request->validate(['name' => 'required|string|max:100', 'sort_order' => 'integer']);
        $category->update($data);
        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    // --- Menu Items ---
    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:200',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'prep_time_minutes' => 'required|integer|min:1',
            'available'         => 'boolean',
            'image'             => 'nullable|image|max:2048',
            'sort_order'        => 'integer',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $data['restaurant_id'] = $this->restaurant()->id;
        $data['available'] = $request->boolean('available', true);

        MenuItem::create($data);
        return back()->with('success', 'Menu item added.');
    }

    public function updateItem(Request $request, MenuItem $item)
    {
        $this->authorize('update', $item);
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:200',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'prep_time_minutes' => 'required|integer|min:1',
            'available'         => 'boolean',
            'image'             => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete($item->image);
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }
        $data['available'] = $request->boolean('available');
        $item->update($data);
        return back()->with('success', 'Menu item updated.');
    }

    public function destroyItem(MenuItem $item)
    {
        $this->authorize('delete', $item);
        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();
        return back()->with('success', 'Menu item deleted.');
    }

    public function toggleAvailability(MenuItem $item)
    {
        $item->update(['available' => !$item->available]);
        return response()->json(['available' => $item->available]);
    }
}
