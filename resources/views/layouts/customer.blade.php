<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Order') — {{ $restaurant->name ?? 'FoodFlow' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        :root { --primary:#f97316;--primary-dark:#ea580c;--dark:#0f172a;--dark-2:#1e293b;--dark-3:#334155;--text:#f1f5f9;--muted:#94a3b8;--border:#334155;--success:#22c55e;--warning:#eab308;--danger:#ef4444;--info:#3b82f6; }
        * { box-sizing:border-box;margin:0;padding:0; }
        body { font-family:'Inter',sans-serif;background:var(--dark);color:var(--text);min-height:100vh;max-width:480px;margin:0 auto; }
        .top-bar { background:var(--dark-2);border-bottom:1px solid var(--border);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100; }
        .restaurant-name { font-weight:800;font-size:1rem;color:var(--primary); }
        .table-info { font-size:.75rem;color:var(--muted); }
        .content { padding:20px; }
        .btn { display:inline-flex;align-items:center;gap:6px;padding:12px 20px;border-radius:10px;font-size:.9rem;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .2s;justify-content:center; }
        .btn-primary { background:var(--primary);color:white; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-secondary { background:var(--dark-3);color:var(--text); }
        .btn-success { background:var(--success);color:white; }
        .btn-danger { background:var(--danger);color:white; }
        .btn-full { width:100%; }
        .card { background:var(--dark-2);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:16px; }
        .form-control { width:100%;padding:12px 14px;background:var(--dark-3);border:1px solid var(--border);border-radius:10px;color:var(--text);font-size:.9rem;font-family:inherit; }
        .form-control:focus { outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(249,115,22,.1); }
        .form-label { display:block;font-size:.8rem;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px; }
        .form-group { margin-bottom:14px; }
        .badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:.7rem;font-weight:600; }
        .badge-success { background:rgba(34,197,94,.15);color:var(--success); }
        .badge-warning { background:rgba(234,179,8,.15);color:var(--warning); }
        .badge-danger  { background:rgba(239,68,68,.15);color:var(--danger); }
        .badge-info    { background:rgba(59,130,246,.15);color:var(--info); }
        .badge-primary { background:rgba(249,115,22,.15);color:var(--primary); }
        .badge-muted   { background:var(--dark-3);color:var(--muted); }
        .badge-purple  { background:rgba(168,85,247,.15);color:#a855f7; }
        .alert { padding:12px 16px;border-radius:8px;font-size:.85rem;margin-bottom:14px;display:flex;align-items:center;gap:8px; }
        .alert-success { background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:var(--success); }
        .alert-danger  { background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:var(--danger); }
        .cart-fab { position:fixed;bottom:24px;right:24px;background:var(--primary);color:white;border:none;border-radius:50px;padding:14px 22px;font-size:.9rem;font-weight:700;cursor:pointer;box-shadow:0 8px 24px rgba(249,115,22,.4);display:flex;align-items:center;gap:8px;z-index:200;transition:all .2s; }
        .cart-fab:hover { background:var(--primary-dark);transform:translateY(-2px); }
        .cart-badge { background:white;color:var(--primary);border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800; }
    </style>
</head>
<body>
<div class="top-bar">
    <div>
        <div class="restaurant-name">{{ $restaurant->name ?? 'Restaurant' }}</div>
        @if(isset($session))<div class="table-info">Table {{ $session->table->number }} · {{ $session->customer_name }}</div>@endif
    </div>
    @if(isset($session))
    <a href="{{ route('customer.order.status') }}" class="btn btn-secondary" style="padding:6px 12px;font-size:.8rem;"><i class="fas fa-receipt"></i> My Orders</a>
    @endif
</div>

<div class="content">
    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif
    @yield('content')
</div>
@stack('scripts')
</body>
</html>
