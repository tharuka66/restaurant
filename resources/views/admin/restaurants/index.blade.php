@extends('layouts.app')
@section('title', 'Restaurants — Admin')
@section('panel-name', 'Super Admin')
@section('page-title', 'Restaurants')
@section('page-subtitle', 'Manage all restaurant accounts')

@section('sidebar-nav')
<div class="nav-section">Main</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
<a href="{{ route('admin.restaurants.index') }}" class="nav-item active"><i class="fas fa-store"></i> Restaurants</a>
<a href="{{ route('admin.restaurants.index') }}?status=pending" class="nav-item"><i class="fas fa-clock"></i> Pending Approval</a>
<a href="{{ route('admin.restaurants.index') }}?status=suspended" class="nav-item"><i class="fas fa-ban"></i> Suspended</a>
@endsection

@section('topbar-actions')
<form method="GET" style="display:flex;gap:8px;">
    <select name="status" class="form-control" style="width:auto;" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="pending"   {{ request('status')=='pending'   ? 'selected':'' }}>Pending</option>
        <option value="active"    {{ request('status')=='active'    ? 'selected':'' }}>Active</option>
        <option value="suspended" {{ request('status')=='suspended' ? 'selected':'' }}>Suspended</option>
        <option value="rejected"  {{ request('status')=='rejected'  ? 'selected':'' }}>Rejected</option>
    </select>
    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}" style="width:200px;">
    <button class="btn btn-secondary btn-sm"><i class="fas fa-search"></i></button>
</form>
@endsection

@section('content')
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Restaurant</th><th>Owner</th><th>Contact</th><th>Status</th><th>Trial Ends</th><th>Created</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($restaurants as $r)
            <tr>
                <td>
                    <strong>{{ $r->name }}</strong>
                    <div class="text-muted text-small">{{ $r->slug }}</div>
                </td>
                <td>{{ $r->owner->name }}<br><span class="text-muted text-small">{{ $r->owner->email }}</span></td>
                <td>{{ $r->phone ?? '—' }}</td>
                <td>
                    <span class="badge badge-{{ $r->status === 'active' ? 'success' : ($r->status === 'pending' ? 'warning' : ($r->status === 'rejected' ? 'danger' : 'muted')) }}">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td>{{ $r->trial_ends_at?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $r->created_at->format('M d, Y') }}</td>
                <td>
                    <div class="flex gap-2" style="flex-wrap:wrap;">
                        <a href="{{ route('admin.restaurants.show', $r) }}" class="btn btn-xs btn-secondary"><i class="fas fa-eye"></i></a>
                        @if($r->status === 'pending')
                            <form method="POST" action="{{ route('admin.restaurants.approve', $r) }}">@csrf
                                <button class="btn btn-xs btn-success"><i class="fas fa-check"></i> Approve</button>
                            </form>
                            <button class="btn btn-xs btn-danger" onclick="openReject({{ $r->id }})"><i class="fas fa-times"></i> Reject</button>
                        @elseif($r->status === 'active')
                            <form method="POST" action="{{ route('admin.restaurants.suspend', $r) }}">@csrf
                                <button class="btn btn-xs btn-warning"><i class="fas fa-ban"></i> Suspend</button>
                            </form>
                        @elseif($r->status === 'suspended' || $r->status === 'rejected')
                            <form method="POST" action="{{ route('admin.restaurants.restore', $r) }}">@csrf
                                <button class="btn btn-xs btn-success"><i class="fas fa-redo"></i> Restore</button>
                            </form>
                        @endif
                        <button class="btn btn-xs btn-info" onclick="openTrial({{ $r->id }})"><i class="fas fa-calendar-plus"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">No restaurants found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $restaurants->withQueryString()->links() }}</div>

{{-- Reject Modal --}}
@foreach($restaurants as $r)
<div class="modal-overlay" id="reject-modal-{{ $r->id }}">
    <div class="modal">
        <div class="modal-header">Reject Restaurant <button class="close-btn" onclick="document.getElementById('reject-modal-{{ $r->id }}').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="{{ route('admin.restaurants.reject', $r) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Reason for Rejection</label>
                <textarea name="reason" class="form-control" rows="3" required placeholder="Provide a clear reason..."></textarea>
            </div>
            <button class="btn btn-danger" style="width:100%">Confirm Rejection</button>
        </form>
    </div>
</div>
<div class="modal-overlay" id="trial-modal-{{ $r->id }}">
    <div class="modal">
        <div class="modal-header">Extend Trial — {{ $r->name }} <button class="close-btn" onclick="document.getElementById('trial-modal-{{ $r->id }}').classList.remove('open')"><i class="fas fa-times"></i></button></div>
        <form method="POST" action="{{ route('admin.restaurants.extend-trial', $r) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Additional Days</label>
                <input type="number" name="days" class="form-control" value="14" min="1" max="90">
            </div>
            <button class="btn btn-primary" style="width:100%">Extend Trial</button>
        </form>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function openReject(id) { document.getElementById('reject-modal-' + id).classList.add('open'); }
function openTrial(id)  { document.getElementById('trial-modal-'  + id).classList.add('open'); }
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});
</script>
@endpush
