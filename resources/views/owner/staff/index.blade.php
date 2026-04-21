@extends('layouts.app')
@section('title', 'Staff Management')
@section('panel-name', 'Restaurant Owner')
@section('page-title', 'Staff Management')
@section('page-subtitle', $restaurant->name)

@section('sidebar-nav')
<div class="nav-section">Home</div>
<a href="{{ route('owner.dashboard') }}" class="nav-item"><i class="fas fa-chart-bar"></i> Dashboard</a>
<div class="nav-section">Manage</div>
<a href="{{ route('owner.menu.index') }}" class="nav-item"><i class="fas fa-utensils"></i> Menu</a>
<a href="{{ route('owner.tables.index') }}" class="nav-item"><i class="fas fa-chair"></i> Rooms & Tables</a>
<a href="{{ route('owner.staff.index') }}" class="nav-item active"><i class="fas fa-users"></i> Staff</a>
<a href="{{ route('owner.orders.index') }}" class="nav-item"><i class="fas fa-receipt"></i> Orders</a>
@endsection

@section('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="document.getElementById('add-staff-modal').classList.add('open')"><i class="fas fa-user-plus"></i> Add Staff</button>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-users"></i> Team Members</span>
        <span class="badge badge-muted">{{ $staff->count() }} members</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($staff as $member)
            <tr>
                <td>
                    <div class="flex gap-3">
                        <div class="user-avatar">{{ strtoupper(substr($member->name,0,1)) }}</div>
                        <strong>{{ $member->name }}</strong>
                    </div>
                </td>
                <td>{{ $member->email }}</td>
                <td>
                    <span class="badge badge-{{ $member->role === 'kitchen' ? 'warning' : 'info' }}">
                        <i class="fas {{ $member->role === 'kitchen' ? 'fa-fire' : 'fa-cash-register' }}"></i>
                        {{ ucfirst($member->role) }}
                    </span>
                </td>
                <td>{{ $member->created_at->format('M d, Y') }}</td>
                <td>
                    <form method="POST" action="{{ route('owner.staff.destroy', $member) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-danger" onclick="return confirm('Remove this staff member?')"><i class="fas fa-user-minus"></i> Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:50px;color:var(--muted);">
                <i class="fas fa-users" style="font-size:2.5rem;display:block;margin-bottom:10px;"></i>
                No staff members yet.
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Staff Modal --}}
<div class="modal-overlay" id="add-staff-modal">
    <div class="modal">
        <div class="modal-header">Add Staff Member <button class="close-btn" onclick="document.getElementById('add-staff-modal').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="{{ route('owner.staff.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required placeholder="Staff member's name"></div>
            <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required placeholder="staff@email.com"></div>
            <div class="form-group"><label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="kitchen">🔥 Kitchen Staff</option>
                    <option value="cashier">💳 Cashier</option>
                </select>
            </div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="8"></div>
                <div class="form-group"><label class="form-label">Confirm</label><input type="password" name="password_confirmation" class="form-control" required></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%"><i class="fas fa-user-plus"></i> Add Staff Member</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); }));
</script>
@endpush
