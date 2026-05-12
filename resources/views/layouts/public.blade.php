<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Utama') - GKKD Jakarta</title>
    <link rel="icon" href="{{ asset('assets/img/gkkd-yellow.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-all.min.css') }}">
    <style>
        :root {
            --primary: #16395f;
            --accent: #e8a838;
            --surface: #f6f8fb;
            --card: #ffffff;
            --text: #182133;
            --muted: #687386;
            --border: #dfe5ee;
            --success: #138a5b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--surface);
            color: var(--text);
        }
        .public-header {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255,255,255,0.96);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
        }
        .public-header-inner {
            max-width: 1120px;
            margin: 0 auto;
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 0 22px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text);
            text-decoration: none;
            font-weight: 800;
        }
        .brand img { width: 42px; height: 42px; object-fit: contain; }
        .public-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .public-nav a {
            color: var(--muted);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 9px 12px;
            border-radius: 8px;
        }
        .public-nav a.active,
        .public-nav a:hover {
            color: var(--primary);
            background: rgba(22,57,95,0.08);
        }
        .public-main {
            max-width: 1120px;
            margin: 0 auto;
            padding: 34px 22px 54px;
        }
        .public-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 12px 34px rgba(20,33,52,0.06);
        }
        .public-form-control {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 11px 13px;
            color: var(--text);
            background: #fff;
        }
        .public-form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22,57,95,0.1);
        }
        .public-label {
            display: block;
            font-weight: 800;
            font-size: 0.82rem;
            margin-bottom: 7px;
        }
        .public-btn {
            border: 0;
            border-radius: 8px;
            padding: 11px 16px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }
        .public-btn-primary { background: var(--primary); color: #fff; }
        .public-btn-primary:hover { color: #fff; background: #0e2a49; }
        .public-btn-accent { background: var(--accent); color: #1d2634; }
        .alert-public {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-weight: 700;
        }
        .alert-success-public { background: rgba(19,138,91,0.1); color: #075f3c; border: 1px solid rgba(19,138,91,0.18); }
        .alert-danger-public { background: rgba(220,38,38,0.08); color: #991b1b; border: 1px solid rgba(220,38,38,0.16); }
        @media (max-width: 760px) {
            .public-header-inner { align-items: flex-start; flex-direction: column; padding: 14px 18px; }
            .public-nav { justify-content: flex-start; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="public-header">
        <div class="public-header-inner">
            <a href="{{ route('public.dashboard') }}" class="brand">
                <img src="{{ asset('assets/img/gkkd-yellow.png') }}" alt="GKKD">
                <span>GKKD Jakarta</span>
            </a>
            <nav class="public-nav">
                <a href="{{ route('public.dashboard') }}" class="{{ request()->routeIs('public.dashboard') ? 'active' : '' }}">Dashboard Utama</a>
                <a href="{{ route('public.laporan-blesscomn') }}" class="{{ request()->routeIs('public.laporan-blesscomn') ? 'active' : '' }}">Laporan Blesscomn</a>
                <a href="{{ route('public.laporan-pa') }}" class="{{ request()->routeIs('public.laporan-pa') ? 'active' : '' }}">Laporan PA</a>
                <a href="{{ route('login') }}">Login</a>
            </nav>
        </div>
    </header>

    <main class="public-main">
        @if(session('success'))
            <div class="alert-public alert-success-public">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-public alert-danger-public">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
    </script>
    @yield('scripts')
</body>
</html>
