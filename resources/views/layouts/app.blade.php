<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Restaurant System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        :root {
            --primary: #f97316; --primary-dark: #ea580c; --primary-light: #fed7aa;
            --dark: #0f172a; --dark-2: #1e293b; --dark-3: #334155;
            --text: #f1f5f9; --muted: #94a3b8;
            --surface: #1e293b; --surface-2: #273548; --border: #334155;
            --success: #22c55e; --warning: #eab308; --danger: #ef4444; --info: #3b82f6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--dark); color: var(--text); min-height: 100vh; }
        .sidebar { width: 260px; min-height: 100vh; background: var(--dark-2); border-right: 1px solid var(--border); position: fixed; top: 0; left: 0; z-index: 100; display: flex; flex-direction: column; }
        .sidebar-logo { padding: 24px 20px; border-bottom: 1px solid var(--border); }
        .sidebar-logo h1 { font-size: 1.2rem; font-weight: 800; color: var(--primary); }
        .sidebar-logo span { font-size: 0.7rem; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; }
        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .nav-section { padding: 8px 20px 4px; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); font-weight: 600; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 20px; color: var(--muted); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all .2s; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { color: var(--text); background: rgba(249,115,22,.12); border-left-color: var(--primary); }
        .nav-item i { width: 18px; text-align: center; }
        .sidebar-user { padding: 16px 20px; border-top: 1px solid var(--border); }
        .sidebar-user .user-info { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: white; flex-shrink: 0; }
        .user-name { font-size: 0.85rem; font-weight: 600; }
        .user-role { font-size: 0.7rem; color: var(--muted); text-transform: capitalize; }
        .logout-btn { display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px 12px; background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.2); border-radius: 8px; color: #ef4444; font-size: 0.8rem; cursor: pointer; text-decoration: none; transition: all .2s; }
        .logout-btn:hover { background: rgba(239,68,68,.2); }
        .main-content { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { background: var(--dark-2); border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; }
        .topbar-title { font-size: 1.1rem; font-weight: 700; }
        .topbar-subtitle { font-size: 0.75rem; color: var(--muted); }
        .page-body { padding: 32px; flex: 1; }
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-size: 1rem; font-weight: 700; }
        .card-body { padding: 24px; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .stat-icon.orange { background: rgba(249,115,22,.15); color: var(--primary); }
        .stat-icon.green  { background: rgba(34,197,94,.15);  color: var(--success); }
        .stat-icon.blue   { background: rgba(59,130,246,.15); color: var(--info); }
        .stat-icon.yellow { background: rgba(234,179,8,.15);  color: var(--warning); }
        .stat-icon.red    { background: rgba(239,68,68,.15);  color: var(--danger); }
        .stat-value { font-size: 1.8rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-secondary { background: var(--dark-3); color: var(--text); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #16a34a; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning { background: var(--warning); color: var(--dark); }
        .btn-info { background: var(--info); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 0.78rem; }
        .btn-xs { padding: 4px 8px; font-size: 0.72rem; }
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 100px; font-size: 0.7rem; font-weight: 600; }
        .badge-success { background: rgba(34,197,94,.15); color: var(--success); }
        .badge-warning { background: rgba(234,179,8,.15); color: var(--warning); }
        .badge-danger  { background: rgba(239,68,68,.15); color: var(--danger); }
        .badge-info    { background: rgba(59,130,246,.15); color: var(--info); }
        .badge-primary { background: rgba(249,115,22,.15); color: var(--primary); }
        .badge-muted   { background: var(--dark-3); color: var(--muted); }
        .badge-purple  { background: rgba(168,85,247,.15); color: #a855f7; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 10px 16px; text-align: left; font-size: 0.72rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; border-bottom: 1px solid var(--border); background: rgba(0,0,0,.2); }
        td { padding: 14px 16px; font-size: 0.85rem; border-bottom: 1px solid rgba(51,65,85,.5); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
        .form-control { width: 100%; padding: 10px 14px; background: var(--dark-3); border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-size: 0.875rem; font-family: inherit; transition: border-color .2s; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(249,115,22,.1); }
        .form-control::placeholder { color: var(--muted); }
        select.form-control option { background: var(--dark-2); }
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.3); color: var(--success); }
        .alert-danger  { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3); color: var(--danger); }
        .alert-warning { background: rgba(234,179,8,.12); border: 1px solid rgba(234,179,8,.3); color: var(--warning); }
        .alert-info    { background: rgba(59,130,246,.12); border: 1px solid rgba(59,130,246,.3); color: var(--info); }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.7); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.open { display: flex; }
        .modal { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 28px; width: 100%; max-width: 500px; }
        .modal-header { font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
        .close-btn { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 1.2rem; transition: color .2s; }
        .close-btn:hover { color: var(--text); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .flex { display: flex; align-items: center; }
        .gap-2 { gap: 8px; } .gap-3 { gap: 12px; } .gap-4 { gap: 16px; }
        .mt-4 { margin-top: 16px; } .mb-4 { margin-bottom: 16px; }
        .text-muted { color: var(--muted); } .text-small { font-size: 0.78rem; }
        .divider { border: none; border-top: 1px solid var(--border); margin: 20px 0; }
        .order-card { background: var(--surface-2); border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-bottom: 12px; }
        .notification-bell { position:relative;color:var(--text);margin-right:15px;text-decoration:none;font-size:1.2rem; }
        .notification-bell.active { color:var(--primary);animation:ring 2s infinite; }
        .notification-bell .badge { position:absolute;top:-6px;right:-8px;background:var(--danger);color:white;border-radius:50%;font-size:.65rem;width:16px;height:16px;display:flex;align-items:center;justify-content:center; }
        @keyframes ring { 0% { transform: rotate(0); } 10% { transform: rotate(15deg); } 20% { transform: rotate(-10deg); } 30% { transform: rotate(5deg); } 40% { transform: rotate(-5deg); } 50% { transform: rotate(0); } 100% { transform: rotate(0); } }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-logo">
        <h1><i class="fas fa-utensils"></i> FoodFlow</h1>
        <span>@yield('panel-name', 'System')</span>
    </div>
    <nav class="sidebar-nav">@yield('sidebar-nav')</nav>
    <div class="sidebar-user">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->role }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Sign Out</button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-subtitle">@yield('page-subtitle', '')</div>
        </div>
        <div class="flex gap-3" style="align-items:center;">
            @if(in_array(auth()->user()->role ?? '', ['owner', 'kitchen']))
            <a href="#" class="notification-bell" id="global-nav-bell" style="display:none;">
                <i class="fas fa-bell"></i>
                <span class="badge" id="global-nav-bell-count">0</span>
            </a>
            @endif
            @yield('topbar-actions')
        </div>
    </div>
    <div class="page-body">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
        @endif
        @yield('content')
    </div>
</div>
{{-- Global Order Alert Modal --}}
@auth
@if(in_array(auth()->user()->role, ['owner', 'kitchen']))
<div class="modal-overlay" id="global-order-modal">
    <div class="modal" style="max-width:400px;text-align:center;">
        <div style="font-size:3rem;color:var(--primary);margin-bottom:10px;"><i class="fas fa-bell"></i></div>
        <h2 style="margin-bottom:10px;">New Order Alert!</h2>
        <p class="text-muted" style="margin-bottom:20px;">Table <strong id="global-order-table" style="color:white;font-size:1.2rem;">?</strong> just placed a new order (<span id="global-order-items"></span> items).</p>
        <div style="display:flex;gap:10px;">
            <button class="btn btn-success" style="flex:1" onclick="acceptGlobalOrder()"><i class="fas fa-check"></i> Accept</button>
            <button class="btn btn-danger" style="flex:1" onclick="rejectGlobalOrder()"><i class="fas fa-times"></i> Reject</button>
        </div>
        <button class="btn btn-secondary btn-full" style="margin-top:10px;" onclick="document.getElementById('global-order-modal').classList.remove('open');">Dismiss (Keep Pending)</button>
    </div>
</div>

<script>
let currentAlertOrderId = null;
let knownOrders = new Set();

async function pollIncomingOrders() {
    try {
        const res = await fetch('/api/new-orders', { headers: {'Accept':'application/json'} });
        if(res.ok) {
            const data = await res.json();
            const count = data.orders.length;
            
            const bell = document.getElementById('global-nav-bell');
            const bellCount = document.getElementById('global-nav-bell-count');
            if (count > 0) {
                bell.style.display = 'inline-flex';
                bell.classList.add('active');
                bellCount.textContent = count;
                
                // Show modal for the first one we haven't seen 
                const newOrder = data.orders.find(o => !knownOrders.has(o.id));
                if(newOrder) {
                    knownOrders.add(newOrder.id);
                    currentAlertOrderId = newOrder.id;
                    document.getElementById('global-order-table').textContent = newOrder.table_number;
                    document.getElementById('global-order-items').textContent = newOrder.items_count;
                    document.getElementById('global-order-modal').classList.add('open');
                    
                    // If we are on the owner dashboard or kitchen dashboard, it might be good to reload or dynamically append
                    // We will just let the user accept/reject through the modal directly
                }
            } else {
                bell.style.display = 'none';
                bell.classList.remove('active');
            }
            
            // Reconcile known orders (remove ones no longer pending)
            const currentIds = data.orders.map(o => o.id);
            knownOrders.forEach(id => { if(!currentIds.includes(id)) knownOrders.delete(id); });
        }
    } catch(e) {}
}

async function acceptGlobalOrder() {
    if(!currentAlertOrderId) return;
    document.getElementById('global-order-modal').classList.remove('open');
    await fetch('/api/orders/' + currentAlertOrderId + '/accept', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept':'application/json'}
    });
    pollIncomingOrders();
    // if on a page viewing orders, reload
    if(window.location.pathname.includes('orders') || window.location.pathname.includes('kitchen')) location.reload();
}

async function rejectGlobalOrder() {
    if(!currentAlertOrderId) return;
    let reason = prompt('Reason for rejection?');
    if(reason === null) return;
    document.getElementById('global-order-modal').classList.remove('open');
    await fetch('/api/orders/' + currentAlertOrderId + '/reject', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type':'application/json', 'Accept':'application/json'},
        body: JSON.stringify({reason: reason || 'Rejected by kitchen'})
    });
    pollIncomingOrders();
    if(window.location.pathname.includes('orders') || window.location.pathname.includes('kitchen')) location.reload();
}

// Poll every 10 seconds
setInterval(pollIncomingOrders, 10000);
pollIncomingOrders();
</script>
@endif
@endauth

@stack('scripts')
</body>
</html>
