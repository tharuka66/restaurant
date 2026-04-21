@extends('layouts.app')
@section('title', 'Cashier Dashboard')
@section('panel-name', 'Cashier')
@section('page-title', 'Cashier Station')
@section('page-subtitle', $restaurant->name)

@section('sidebar-nav')
<div class="nav-section">Cashier</div>
<a href="{{ route('cashier.index') }}" class="nav-item active"><i class="fas fa-cash-register"></i> Active Sessions</a>
@endsection

@section('content')
@if($activeSessions->isEmpty())
<div style="text-align:center;padding:80px;color:var(--muted)">
    <i class="fas fa-chair" style="font-size:3rem;display:block;margin-bottom:16px;"></i>
    <h3>No Active Sessions</h3>
    <p>All tables are free. New sessions will appear here when customers start dining.</p>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @foreach($activeSessions as $session)
    <div class="card" style="margin-bottom:0">
        <div class="card-header">
            <div>
                <div class="card-title">Table {{ $session->table->number }}</div>
                <div class="text-muted text-small">{{ $session->table->room->name }} · {{ $session->guests }} guest(s)</div>
            </div>
            <span class="badge badge-success">Active</span>
        </div>
        <div class="card-body" style="padding:16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
                <div><div class="text-muted text-small">Orders</div><div style="font-weight:700;font-size:1.1rem;">{{ $session->orders->count() }}</div></div>
                <div><div class="text-muted text-small">Since</div><div style="font-weight:600;font-size:.85rem;">{{ $session->opened_at->diffForHumans() }}</div></div>
            </div>
            @if($session->bill)
            <div style="background:var(--dark-3);border-radius:8px;padding:10px;margin-bottom:12px;">
                <div class="flex" style="justify-content:space-between;"><span class="text-muted text-small">Total</span><strong style="color:var(--primary)">${{ number_format($session->bill->total_amount,2) }}</strong></div>
            </div>
            @endif
            <a href="{{ route('cashier.session', $session) }}" class="btn btn-primary" style="width:100%;justify-content:center;">
                <i class="fas fa-receipt"></i> View Bill & Checkout
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
