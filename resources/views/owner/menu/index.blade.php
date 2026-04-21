@extends('layouts.app')
@section('title', 'Menu Management')
@section('panel-name', 'Restaurant Owner')
@section('page-title', 'Menu Management')
@section('page-subtitle', $restaurant->name)

@section('sidebar-nav')
<div class="nav-section">Home</div>
<a href="{{ route('owner.dashboard') }}" class="nav-item"><i class="fas fa-chart-bar"></i> Dashboard</a>
<div class="nav-section">Manage</div>
<a href="{{ route('owner.menu.index') }}" class="nav-item active"><i class="fas fa-utensils"></i> Menu</a>
<a href="{{ route('owner.tables.index') }}" class="nav-item"><i class="fas fa-chair"></i> Rooms & Tables</a>
<a href="{{ route('owner.staff.index') }}" class="nav-item"><i class="fas fa-users"></i> Staff</a>
<a href="{{ route('owner.orders.index') }}" class="nav-item"><i class="fas fa-receipt"></i> Orders</a>
@endsection

@section('topbar-actions')
<button class="btn btn-secondary btn-sm" onclick="document.getElementById('add-category-modal').classList.add('open')"><i class="fas fa-folder-plus"></i> Add Category</button>
<button class="btn btn-primary btn-sm" onclick="document.getElementById('add-item-modal').classList.add('open')"><i class="fas fa-plus"></i> Add Item</button>
@endsection

@section('content')

@foreach($categories as $category)
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-tag" style="color:var(--primary)"></i> {{ $category->name }}</span>
        <div class="flex gap-2">
            <span class="badge badge-muted">{{ $category->allItems->count() }} items</span>
            <button class="btn btn-xs btn-danger" onclick="deleteCategory({{ $category->id }})"><i class="fas fa-trash"></i></button>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Item</th><th>Price</th><th>Prep Time</th><th>Available</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($category->allItems as $item)
            <tr>
                <td>
                    <strong>{{ $item->name }}</strong>
                    @if($item->is_veg) <span class="badge badge-success" style="font-size:0.6rem;padding:2px 6px;">VEG</span> @else <span class="badge badge-danger" style="font-size:0.6rem;padding:2px 6px;">NON-VEG</span> @endif
                    @if($item->description)<div class="text-muted text-small">{{ Str::limit($item->description, 60) }}</div>@endif
                </td>
                <td><strong style="color:var(--primary)">${{ number_format($item->price, 2) }}</strong></td>
                <td>{{ $item->prep_time_minutes }} min</td>
                <td>
                    <label class="toggle" style="cursor:pointer">
                        <input type="checkbox" {{ $item->available ? 'checked' : '' }}
                            onchange="toggleItem({{ $item->id }}, this)"
                            style="accent-color:var(--primary)">
                        <span style="margin-left:6px;font-size:.8rem;color:var(--muted)">{{ $item->available ? 'Yes' : 'No' }}</span>
                    </label>
                </td>
                <td>
                    <div class="flex gap-2">
                        <button class="btn btn-xs btn-secondary" onclick="editItem({{ $item->id }}, {{ json_encode($item) }})"><i class="fas fa-edit"></i></button>
                        <form method="POST" action="{{ route('owner.menu.item.destroy', $item) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">No items in this category.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach

@if($categories->isEmpty())
<div class="card">
    <div class="card-body" style="text-align:center;padding:60px;">
        <i class="fas fa-utensils" style="font-size:3rem;color:var(--muted);margin-bottom:16px;display:block;"></i>
        <p class="text-muted">No menu categories yet. Add your first category to get started.</p>
        <button class="btn btn-primary mt-4" onclick="document.getElementById('add-category-modal').classList.add('open')">
            <i class="fas fa-plus"></i> Add First Category
        </button>
    </div>
</div>
@endif

{{-- Add Category Modal --}}
<div class="modal-overlay" id="add-category-modal">
    <div class="modal">
        <div class="modal-header">Add Category <button class="close-btn" onclick="document.getElementById('add-category-modal').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="{{ route('owner.menu.category.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Category Name</label><input type="text" name="name" class="form-control" required placeholder="e.g. Starters, Main Course"></div>
            <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div>
            <button type="submit" class="btn btn-primary" style="width:100%">Add Category</button>
        </form>
    </div>
</div>

{{-- Delete Category Form (hidden) --}}
<form method="POST" id="delete-category-form" style="display:none">@csrf @method('DELETE')</form>

{{-- Add Item Modal --}}
<div class="modal-overlay" id="add-item-modal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">Add Menu Item <button class="close-btn" onclick="document.getElementById('add-item-modal').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="{{ route('owner.menu.item.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group"><label class="form-label">Category</label>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div class="form-group"><label class="form-label">Item Name</label><input type="text" name="name" class="form-control" required placeholder="e.g. Grilled Salmon"></div>
            <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2" placeholder="Optional description..."></textarea></div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Price ($)</label><input type="number" name="price" class="form-control" step="0.01" min="0" required></div>
                <div class="form-group"><label class="form-label">Prep Time (min)</label><input type="number" name="prep_time_minutes" class="form-control" value="15" min="1" required></div>
            </div>
            <div class="form-group"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="available" id="available" value="1" checked style="accent-color:var(--primary)">
                <label for="available" class="form-label" style="margin-bottom:0">Available</label>

                <input type="checkbox" name="is_veg" id="is_veg" value="1" style="accent-color:var(--success);margin-left:15px;">
                <label for="is_veg" class="form-label" style="margin-bottom:0;color:var(--success);">Vegetarian</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Add Item</button>
        </form>
    </div>
</div>

{{-- Edit Item Modal --}}
<div class="modal-overlay" id="edit-item-modal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">Edit Menu Item <button class="close-btn" onclick="document.getElementById('edit-item-modal').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" id="edit-item-form" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group"><label class="form-label">Category</label>
                <select name="category_id" id="edit-category-id" class="form-control" required>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div class="form-group"><label class="form-label">Item Name</label><input type="text" id="edit-name" name="name" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Description</label><textarea id="edit-description" name="description" class="form-control" rows="2"></textarea></div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Price ($)</label><input type="number" id="edit-price" name="price" class="form-control" step="0.01" min="0" required></div>
                <div class="form-group"><label class="form-label">Prep Time (min)</label><input type="number" id="edit-prep" name="prep_time_minutes" class="form-control" min="1" required></div>
            </div>
            <div class="form-group"><label class="form-label">Image (leave blank to keep)</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="available" id="edit-available" value="1" style="accent-color:var(--primary)">
                <label for="edit-available" class="form-label" style="margin-bottom:0">Available</label>

                <input type="checkbox" name="is_veg" id="edit-is-veg" value="1" style="accent-color:var(--success);margin-left:15px;">
                <label for="edit-is-veg" class="form-label" style="margin-bottom:0;color:var(--success);">Vegetarian</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Save Changes</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); }));

function deleteCategory(id) {
    if (!confirm('Delete this category and all its items?')) return;
    const form = document.getElementById('delete-category-form');
    form.action = '/owner/menu/categories/' + id;
    form.submit();
}

function editItem(id, item) {
    const form = document.getElementById('edit-item-form');
    form.action = '/owner/menu/items/' + id;
    document.getElementById('edit-name').value = item.name;
    document.getElementById('edit-description').value = item.description || '';
    document.getElementById('edit-price').value = item.price;
    document.getElementById('edit-prep').value = item.prep_time_minutes;
    document.getElementById('edit-available').checked = !!item.available;
    document.getElementById('edit-is-veg').checked = !!item.is_veg;
    document.getElementById('edit-category-id').value = item.category_id;
    document.getElementById('edit-item-modal').classList.add('open');
}

async function toggleItem(id, checkbox) {
    try {
        const res = await fetch('/owner/menu/items/' + id + '/toggle', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        });
        const data = await res.json();
        checkbox.nextElementSibling.textContent = data.available ? 'Yes' : 'No';
    } catch(e) { checkbox.checked = !checkbox.checked; }
}
</script>
@endpush
