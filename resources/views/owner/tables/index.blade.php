@extends('layouts.app')
@section('title', 'Rooms & Tables')
@section('panel-name', 'Restaurant Owner')
@section('page-title', 'Rooms & Tables')
@section('page-subtitle', $restaurant->name)

@section('sidebar-nav')
<div class="nav-section">Home</div>
<a href="{{ route('owner.dashboard') }}" class="nav-item"><i class="fas fa-chart-bar"></i> Dashboard</a>
<div class="nav-section">Manage</div>
<a href="{{ route('owner.menu.index') }}" class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
<a href="{{ route('owner.tables.index') }}" class="nav-item active"><i class="fas fa-chair"></i> Rooms & Tables</a>
<a href="{{ route('owner.staff.index') }}" class="nav-item"><i class="fas fa-users"></i> Staff</a>
<a href="{{ route('owner.orders.index') }}" class="nav-item"><i class="fas fa-receipt"></i> Orders</a>
@endsection

@section('topbar-actions')
<button class="btn btn-secondary btn-sm" onclick="document.getElementById('add-room-modal').classList.add('open')"><i class="fas fa-plus"></i> Add Room</button>
<button class="btn btn-primary btn-sm" onclick="document.getElementById('add-table-modal').classList.add('open')"><i class="fas fa-chair"></i> Add Table</button>
@endsection

@section('content')

@foreach($rooms as $room)
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-door-open" style="color:var(--primary)"></i> {{ $room->name }}</span>
        <div class="flex gap-2">
            <span class="badge badge-muted">{{ $room->tables->count() }} tables</span>
            <form method="POST" action="{{ route('owner.tables.room.destroy', $room) }}" style="display:inline">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger" onclick="return confirm('Delete room and all its tables?')"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
            @forelse($room->tables as $table)
            <div style="background:var(--dark-3);border:1px solid var(--border);border-radius:10px;padding:16px;position:relative;">
                <div style="font-size:1.4rem;font-weight:800;color:{{ $table->status==='occupied'?'var(--warning)':'var(--success)' }}">T{{ $table->number }}</div>
                <div class="text-small text-muted">Cap: {{ $table->capacity }} · {{ ucfirst($table->status) }}</div>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('owner.tables.qr', $table) }}" class="btn btn-xs btn-info" title="View QR"><i class="fas fa-qrcode"></i></a>
                    <a href="{{ route('owner.tables.qr.download', $table) }}" class="btn btn-xs btn-secondary" title="Download QR"><i class="fas fa-download"></i></a>
                    <form method="POST" action="{{ route('owner.tables.destroy', $table) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-danger" onclick="return confirm('Delete table?')"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div style="color:var(--muted);font-size:.85rem;padding:20px;grid-column:1/-1;text-align:center;">No tables in this room yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endforeach

@if($rooms->isEmpty())
<div class="card">
    <div class="card-body" style="text-align:center;padding:60px;">
        <i class="fas fa-chair" style="font-size:3rem;color:var(--muted);margin-bottom:16px;display:block;"></i>
        <p class="text-muted">No rooms yet. Add your first room to start managing tables.</p>
        <button class="btn btn-primary mt-4" onclick="document.getElementById('add-room-modal').classList.add('open')"><i class="fas fa-plus"></i> Add First Room</button>
    </div>
</div>
@endif

{{-- Add Room Modal --}}
<div class="modal-overlay" id="add-room-modal">
    <div class="modal">
        <div class="modal-header">Add Room <button class="close-btn" onclick="document.getElementById('add-room-modal').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="{{ route('owner.tables.room.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Room Name</label><input type="text" name="name" class="form-control" required placeholder="e.g. Main Hall, Terrace"></div>
            <div class="form-group"><label class="form-label">Description (optional)</label><input type="text" name="description" class="form-control" placeholder="e.g. Outdoor seating area"></div>
            <button type="submit" class="btn btn-primary" style="width:100%">Add Room</button>
        </form>
    </div>
</div>

{{-- Add Table Modal --}}
<div class="modal-overlay" id="add-table-modal">
    <div class="modal">
        <div class="modal-header">Add Table <button class="close-btn" onclick="document.getElementById('add-table-modal').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="{{ route('owner.tables.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Room</label>
                <select name="room_id" class="form-control" required>
                    @foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->name }}</option>@endforeach
                </select>
            </div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Table Number</label><input type="text" name="number" class="form-control" required placeholder="e.g. 1, A, T1"></div>
                <div class="form-group"><label class="form-label">Capacity</label><input type="number" name="capacity" class="form-control" value="4" min="1"></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Add Table</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); }));
</script>
@endpush
