<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Offline Work Order Manager')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Yu Gothic UI", "Meiryo", sans-serif; margin: 0; background: #f4f5f7; color: #1f2937; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; padding: 24px 16px; }
        nav { background: #1f2937; padding: 12px 0; }
        nav .container { display: flex; align-items: center; gap: 24px; padding-top: 0; padding-bottom: 0; }
        nav a { color: #e5e7eb; text-decoration: none; font-size: 14px; }
        nav a:hover { color: #fff; }
        nav .brand { font-size: 16px; font-weight: 700; color: #fff; }
        h1 { font-size: 24px; margin: 0 0 16px; }
        .alert { padding: 12px 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; margin-bottom: 16px; }
        .alert--error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f9fafb; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        .btn { display: inline-block; padding: 8px 16px; font: inherit; font-size: 14px; border-radius: 6px; border: 1px solid transparent; cursor: pointer; text-decoration: none; line-height: 1.4; }
        .btn--primary { background: #2563eb; color: #fff; border-color: #2563eb; }
        .btn--primary:hover { background: #1d4ed8; }
        .btn--secondary { background: #fff; color: #374151; border-color: #d1d5db; }
        .btn--secondary:hover { background: #f9fafb; }
        .btn--danger { background: #dc2626; color: #fff; border-color: #dc2626; }
        .btn--danger:hover { background: #b91c1c; }
        .btn--sm { padding: 4px 10px; font-size: 13px; }
        .actions { display: flex; gap: 6px; align-items: center; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px 12px; font: inherit; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group .error { color: #dc2626; font-size: 13px; margin-top: 2px; }
        .card { background: #fff; border: 1px solid #d1d5db; border-radius: 8px; padding: 24px; margin-bottom: 16px; }
        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 2px 8px; font-size: 12px; border-radius: 4px; background: #e5e7eb; color: #374151; }
        .badge--active { background: #dbeafe; color: #1e40af; }
        .badge--done { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
<nav>
    <div class="container">
        <a href="/" class="brand">Work Order Manager</a>
        <a href="{{ route('customers.index') }}">顧客</a>
        <a href="{{ route('work_orders.index') }}">受注</a>
    </div>
</nav>
<div class="container">
    @if (session('status'))
        <div class="alert">{{ session('status') }}</div>
    @endif
    @yield('content')
</div>
</body>
</html>
