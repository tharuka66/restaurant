@extends('layouts.customer')
@section('title', 'Welcome')

@section('content')
<div style="text-align:center;padding:40px 20px;">
    <div style="font-size:4rem;margin-bottom:16px;">🍽️</div>
    <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:8px;">Welcome to<br>{{ $restaurant->name }}</h1>
    <p style="color:var(--muted);margin-bottom:8px;">Table <strong style="color:var(--primary)">{{ $table->number }}</strong> · Room: {{ $table->room->name }}</p>
    <p style="font-size:.85rem;color:var(--muted);margin-bottom:32px;">Fill in your name to start your session and browse our menu.</p>
</div>

<div class="card">
    <div style="padding:24px;">
        <form method="POST" action="{{ route('customer.session.start') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Your Name (optional)</label>
                <input type="text" name="customer_name" class="form-control" placeholder="e.g. John" maxlength="100">
            </div>
            <div class="form-group">
                <label class="form-label">Number of Guests</label>
                <select name="guests" class="form-control">
                    @for($i=1;$i<=10;$i++)<option value="{{ $i }}">{{ $i }} {{ $i===1?'guest':'guests' }}</option>@endfor
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px;font-size:1rem;padding:14px;">
                <i class="fas fa-arrow-right"></i> Start Dining & View Menu
            </button>
        </form>
    </div>
</div>
@endsection
