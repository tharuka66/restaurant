@extends('layouts.app')
@section('title', 'QR Code — Table ' . $table->number)
@section('panel-name', 'Restaurant Owner')
@section('page-title', 'QR Code — Table {{ $table->number }}')

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
<a href="{{ route('owner.tables.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
<a href="{{ route('owner.tables.qr.download', $table) }}" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Download PNG</a>
@endsection

@section('content')
<div style="display:flex;justify-content:center;">
    <div class="card" style="max-width:420px;width:100%;text-align:center;">
        <div class="card-header" style="justify-content:center;">
            <span class="card-title">Table {{ $table->number }} — {{ $table->room->name }}</span>
        </div>
        <div class="card-body">
            <div style="background:white;border-radius:12px;padding:24px;display:inline-block;margin-bottom:20px;">
                {!! $qrCode !!}
            </div>
            <div style="margin-bottom:16px;">
                <div class="text-muted text-small" style="margin-bottom:6px;">Scan URL</div>
                <code style="background:var(--dark-3);padding:8px 12px;border-radius:8px;font-size:.78rem;word-break:break-all;display:block;">{{ $url }}</code>
            </div>
            <div style="color:var(--muted);font-size:.8rem;">
                <i class="fas fa-info-circle"></i> Customers scan this QR to start their dining session.
            </div>
        </div>
    </div>
</div>
@endsection
