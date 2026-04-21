@extends('layouts.app')
@section('title', 'Awaiting Approval')
@section('panel-name', 'Restaurant Owner')
@section('page-title', 'Application Pending')

@section('sidebar-nav')
<div class="nav-section">Account</div>
<a href="{{ route('owner.pending') }}" class="nav-item active"><i class="fas fa-clock"></i> Status</a>
@endsection

@section('content')
<div style="display:flex;justify-content:center;padding:40px 0;">
    <div class="card" style="max-width:500px;width:100%;text-align:center;">
        <div class="card-body" style="padding:50px 40px;">
            @if(!$restaurant)
            <i class="fas fa-store-slash" style="font-size:3rem;color:var(--muted);display:block;margin-bottom:20px;"></i>
            <h2 style="margin-bottom:10px;">No Restaurant Found</h2>
            <p class="text-muted">You haven't registered a restaurant yet.</p>
            <a href="{{ route('register.restaurant') }}" class="btn btn-primary mt-4"><i class="fas fa-plus"></i> Register Restaurant</a>
            @elseif($restaurant->status === 'pending')
            <div style="width:80px;height:80px;background:rgba(234,179,8,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;color:var(--warning);">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <h2 style="margin-bottom:10px;">Under Review</h2>
            <p class="text-muted" style="margin-bottom:16px;">Your restaurant <strong style="color:var(--text)">{{ $restaurant->name }}</strong> is being reviewed by our admin team. You'll be notified once approved.</p>
            <div style="background:var(--dark-3);border-radius:10px;padding:16px;text-align:left;font-size:.82rem;">
                <div style="margin-bottom:6px;"><span class="text-muted">Submitted:</span> {{ $restaurant->created_at->format('M d, Y') }}</div>
                <div><span class="text-muted">Trial Period:</span> {{ $restaurant->trial_ends_at?->format('M d, Y') ?? 'N/A' }}</div>
            </div>
            @elseif($restaurant->status === 'rejected')
            <div style="width:80px;height:80px;background:rgba(239,68,68,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;color:var(--danger);">
                <i class="fas fa-times-circle"></i>
            </div>
            <h2 style="margin-bottom:10px;">Application Rejected</h2>
            @if($restaurant->rejection_reason)
            <div class="alert alert-danger"><i class="fas fa-info-circle"></i> {{ $restaurant->rejection_reason }}</div>
            @endif
            <p class="text-muted">Please contact support or resubmit with corrections.</p>
            @elseif($restaurant->status === 'suspended')
            <div style="width:80px;height:80px;background:rgba(239,68,68,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;color:var(--danger);">
                <i class="fas fa-ban"></i>
            </div>
            <h2 style="margin-bottom:10px;">Account Suspended</h2>
            <p class="text-muted">Your restaurant has been suspended. Please contact support.</p>
            @endif
        </div>
    </div>
</div>
@endsection
