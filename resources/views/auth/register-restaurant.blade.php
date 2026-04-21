<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register Restaurant — FoodFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root { --primary:#f97316;--dark:#0f172a;--dark-2:#1e293b;--dark-3:#334155;--text:#f1f5f9;--muted:#94a3b8;--border:#334155; }
        * { box-sizing:border-box;margin:0;padding:0; }
        body { font-family:'Inter',sans-serif;background:var(--dark);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px; }
        .auth-box { background:var(--dark-2);border:1px solid var(--border);border-radius:20px;padding:40px;width:100%;max-width:500px; }
        .auth-logo { text-align:center;margin-bottom:28px; }
        .auth-logo h1 { font-size:1.8rem;font-weight:800;color:var(--primary); }
        .auth-logo p { font-size:.85rem;color:var(--muted);margin-top:4px; }
        .form-group { margin-bottom:16px; }
        .form-label { display:block;font-size:.78rem;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px; }
        .form-control { width:100%;padding:11px 14px;background:var(--dark-3);border:1px solid var(--border);border-radius:10px;color:var(--text);font-size:.875rem;font-family:inherit; }
        .form-control:focus { outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(249,115,22,.1); }
        .btn { display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:13px;border-radius:10px;font-size:.95rem;font-weight:700;cursor:pointer;border:none;transition:all .2s; }
        .btn-primary { background:var(--primary);color:white; }
        .btn-primary:hover { background:#ea580c;transform:translateY(-1px); }
        .divider { display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--muted);font-size:.8rem; }
        .divider::before,.divider::after { content:'';flex:1;border-top:1px solid var(--border); }
        .section-title { font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--primary);margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border); }
        .grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
        .alert { padding:10px 14px;border-radius:8px;font-size:.82rem;margin-bottom:14px;color:#ef4444;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2); }
        a { color:var(--primary);text-decoration:none; }
    </style>
</head>
<body>
<div class="auth-box">
    <div class="auth-logo">
        <h1><i class="fas fa-utensils"></i> FoodFlow</h1>
        <p>Register your restaurant — get started in minutes</p>
    </div>

    @if($errors->any())
    <div class="alert">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <form method="POST" action="{{ route('register.restaurant') }}">
        @csrf
        <div class="section-title"><i class="fas fa-user"></i> Owner Account</div>
        <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="owner_name" class="form-control" value="{{ old('owner_name') }}" required placeholder="Your full name"></div>
        <div class="grid-2">
            <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="you@email.com"></div>
        </div>
        <div class="grid-2">
            <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="8" placeholder="Min 8 chars"></div>
            <div class="form-group"><label class="form-label">Confirm</label><input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password"></div>
        </div>

        <div class="section-title" style="margin-top:8px;"><i class="fas fa-store"></i> Restaurant Info</div>
        <div class="form-group"><label class="form-label">Restaurant Name</label><input type="text" name="restaurant_name" class="form-control" value="{{ old('restaurant_name') }}" required placeholder="e.g. The Grand Bistro"></div>
        <div class="grid-2">
            <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1-555-0100"></div>
        </div>
        <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2" placeholder="Restaurant address">{{ old('address') }}</textarea></div>

        <button type="submit" class="btn btn-primary" style="margin-top:8px;"><i class="fas fa-rocket"></i> Submit for Approval</button>
    </form>

    <div class="divider">or</div>
    <div style="text-align:center;font-size:.85rem;color:var(--muted);">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
</div>
</body>
</html>
