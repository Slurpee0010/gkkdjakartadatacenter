<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GKKD Jakarta - Sistem Manajemen Gereja">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'GKKD Jakarta') — Sistem Manajemen Gereja</title>
    <link rel="icon" href="{{ asset('assets/img/gkkd-yellow.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-all.min.css') }}">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2a5298;
            --primary-dark: #0f1f3a;
            --accent: #e8a838;
            --accent-light: #f0c060;
            --surface: #f8f9fc;
            --surface-card: #ffffff;
            --text-primary: #1a1d29;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --sidebar-width: 280px;
            --header-height: 70px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.07);
            --shadow-lg: 0 8px 30px rgba(0,0,0,0.1);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--surface);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .gkkd-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            filter: brightness(1.1);
        }

        .sidebar-brand-text {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .sidebar-brand-text small {
            display: block;
            font-size: 0.7rem;
            font-weight: 400;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .sidebar-nav-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 16px 16px 8px;
        }

        .sidebar-nav-item {
            display: block;
            padding: 11px 16px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            opacity: 0.8;
        }

        .sidebar-nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
            text-decoration: none;
            transform: translateX(3px);
        }

        .sidebar-nav-item.active {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--primary-dark);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(232, 168, 56, 0.3);
        }

        .sidebar-nav-item.active i {
            opacity: 1;
        }

        .sidebar-nav-group {
            margin-bottom: 2px;
        }

        .sidebar-nav-toggle {
            width: 100%;
            border: 0;
            background: transparent;
            cursor: pointer;
            font-family: inherit;
            text-align: left;
        }

        .sidebar-nav-toggle .fa-chevron-down {
            margin-left: auto;
            font-size: 0.72rem;
            transition: transform 0.2s ease;
        }

        .sidebar-nav-group.open .sidebar-nav-toggle {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .sidebar-nav-group.open .sidebar-nav-toggle .fa-chevron-down {
            transform: rotate(180deg);
        }

        .sidebar-nav-submenu {
            display: none;
            padding: 2px 0 8px 28px;
        }

        .sidebar-nav-group.open .sidebar-nav-submenu {
            display: block;
        }

        .sidebar-nav-submenu .sidebar-nav-item {
            padding: 9px 14px;
            font-size: 0.82rem;
        }

        .sidebar-nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            min-width: 22px;
            text-align: center;
        }

        .sidebar-nav-item.active .sidebar-nav-badge {
            background: rgba(0,0,0,0.15);
            color: var(--primary-dark);
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-footer-text {
            color: rgba(255,255,255,0.3);
            font-size: 0.7rem;
            text-align: center;
        }

        /* ========== MAIN CONTENT ========== */
        .gkkd-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* ========== HEADER ========== */
        .gkkd-header {
            height: var(--header-height);
            background: var(--surface-card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover {
            background: var(--surface);
            color: var(--text-primary);
        }

        .header-breadcrumb {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .header-breadcrumb a {
            color: var(--primary-light);
            text-decoration: none;
        }

        .header-breadcrumb a:hover {
            text-decoration: underline;
        }

        .header-breadcrumb span {
            margin: 0 6px;
            color: var(--text-muted);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-date {
            font-size: 0.82rem;
            color: var(--text-secondary);
            padding: 6px 14px;
            background: var(--surface);
            border-radius: 20px;
            font-weight: 500;
        }

        .header-date i {
            margin-right: 6px;
            color: var(--accent);
        }

        .header-notification {
            position: relative;
            min-width: 38px;
            height: 38px;
            justify-content: center;
            padding: 0;
        }

        .notification-dot {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--danger);
            color: #fff;
            font-size: 0.68rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--surface-card);
        }

        /* ========== PAGE CONTENT ========== */
        .gkkd-content {
            padding: 28px 32px 40px;
        }

        .page-header {
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.03em;
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 0.88rem;
            font-weight: 400;
        }

        /* ========== CARDS ========== */
        .gkkd-card {
            background: var(--surface-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.25s ease, transform 0.25s ease;
        }

        .gkkd-card:hover {
            box-shadow: var(--shadow-md);
        }

        .gkkd-card-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .gkkd-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        .gkkd-card-body {
            padding: 24px;
        }

        /* ========== STAT CARDS ========== */
        .stat-card {
            background: var(--surface-card);
            border-radius: var(--radius-lg);
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card.stat-primary::before { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
        .stat-card.stat-success::before { background: linear-gradient(90deg, var(--success), #34d399); }
        .stat-card.stat-warning::before { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .stat-card.stat-info::before { background: linear-gradient(90deg, var(--info), #60a5fa); }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 16px;
        }

        .stat-primary .stat-icon { background: rgba(30, 58, 95, 0.08); color: var(--primary); }
        .stat-success .stat-icon { background: rgba(16, 185, 129, 0.08); color: var(--success); }
        .stat-warning .stat-icon { background: rgba(245, 158, 11, 0.08); color: var(--warning); }
        .stat-info .stat-icon { background: rgba(59, 130, 246, 0.08); color: var(--info); }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-label {
            font-size: 0.82rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ========== TABLE ========== */
        .gkkd-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .gkkd-table thead th {
            background: var(--surface);
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .gkkd-table thead th:first-child { border-radius: var(--radius-sm) 0 0 0; }
        .gkkd-table thead th:last-child { border-radius: 0 var(--radius-sm) 0 0; }

        .gkkd-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 0.88rem;
            color: var(--text-primary);
            vertical-align: middle;
        }

        .gkkd-table tbody tr {
            transition: background 0.15s ease;
        }

        .gkkd-table tbody tr:hover {
            background: rgba(30, 58, 95, 0.02);
        }

        .gkkd-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ========== BUTTONS ========== */
        .btn-gkkd {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            line-height: 1.4;
        }

        .btn-gkkd:hover { text-decoration: none; }

        .btn-primary-gkkd {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            box-shadow: 0 2px 8px rgba(30, 58, 95, 0.25);
        }

        .btn-primary-gkkd:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(30, 58, 95, 0.35);
            color: #fff;
        }

        .btn-accent-gkkd {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--primary-dark);
            box-shadow: 0 2px 8px rgba(232, 168, 56, 0.25);
        }

        .btn-accent-gkkd:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(232, 168, 56, 0.35);
            color: var(--primary-dark);
        }

        .btn-sm-gkkd {
            padding: 6px 12px;
            font-size: 0.78rem;
            border-radius: 6px;
        }

        .btn-outline-gkkd {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-secondary);
        }

        .btn-outline-gkkd:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(30, 58, 95, 0.03);
        }

        .btn-edit-gkkd {
            background: rgba(59, 130, 246, 0.08);
            color: var(--info);
            border: none;
        }

        .btn-edit-gkkd:hover {
            background: rgba(59, 130, 246, 0.15);
            color: var(--info);
        }

        .btn-delete-gkkd {
            background: rgba(239, 68, 68, 0.08);
            color: var(--danger);
            border: none;
        }

        .btn-delete-gkkd:hover {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        /* ========== FORMS ========== */
        .gkkd-form-group {
            margin-bottom: 20px;
        }

        .gkkd-form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
            letter-spacing: -0.01em;
        }

        .gkkd-form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: var(--surface-card);
            transition: all 0.2s ease;
            outline: none;
        }

        .gkkd-form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
        }

        .gkkd-form-control::placeholder {
            color: var(--text-muted);
        }

        select.gkkd-form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8.825L1.175 4 2.238 2.938 6 6.7 9.763 2.937 10.825 4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }

        /* ========== ALERTS ========== */
        .gkkd-alert {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .gkkd-alert-success {
            background: rgba(16, 185, 129, 0.08);
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        .gkkd-alert-danger {
            background: rgba(239, 68, 68, 0.08);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.15);
        }

        .gkkd-alert-notification {
            background: rgba(59, 130, 246, 0.08);
            color: #1e3a8a;
            border: 1px solid rgba(59, 130, 246, 0.16);
            justify-content: space-between;
            align-items: flex-start;
        }

        .gkkd-alert-notification strong {
            color: #172554;
        }

        /* ========== BADGE ========== */
        .gkkd-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.73rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .badge-primary { background: rgba(30, 58, 95, 0.08); color: var(--primary); }
        .badge-success { background: rgba(16, 185, 129, 0.08); color: var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.08); color: var(--warning); }
        .badge-info { background: rgba(59, 130, 246, 0.08); color: var(--info); }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            opacity: 0.4;
        }

        .empty-state p {
            font-size: 0.88rem;
            margin-top: 4px;
        }

        /* ========== BACK LINK ========== */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            margin-bottom: 20px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
            text-decoration: none;
        }

        /* ========== ANIMATION ========== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeInUp 0.4s ease forwards;
        }

        .fade-in-delay-1 { animation-delay: 0.05s; }
        .fade-in-delay-2 { animation-delay: 0.1s; }
        .fade-in-delay-3 { animation-delay: 0.15s; }
        .fade-in-delay-4 { animation-delay: 0.2s; }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 991.98px) {
            .gkkd-sidebar {
                transform: translateX(-100%);
            }

            .gkkd-sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.4);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }

            .sidebar-toggle {
                display: block;
            }

            .gkkd-main {
                margin-left: 0;
            }

            .gkkd-content {
                padding: 20px 16px 30px;
            }

            .gkkd-header {
                padding: 0 16px;
            }
        }

        @media (max-width: 575.98px) {
            .page-title { font-size: 1.3rem; }
            .stat-value { font-size: 1.6rem; }
            .gkkd-card-body { padding: 16px; }
            .gkkd-table { font-size: 0.82rem; }
            .gkkd-table thead th,
            .gkkd-table tbody td { padding: 10px 12px; }
        }

        /* Scrollbar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
    </style>
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="gkkd-sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('assets/img/gkkd-yellow.png') }}" alt="GKKD Logo">
            <div class="sidebar-brand-text">
                GKKD Jakarta
                <small>Sistem Manajemen Gereja</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            @php
                $laporanPaOpen = request()->is('pembimbing*')
                    || request()->is('anak_bimbingan*')
                    || request()->is('laporan_pa*')
                    || request()->is('laporan-pa/report*');

                $blesscomnOpen = request()->is('pengurus_blesscomn*')
                    || request()->is('master_blesscomn*')
                    || request()->is('laporan_blesscomn*');

                $masterDataOpen = request()->is('pelayanan*') || request()->is('wilayah*') || request()->is('master_buku_pa*');
                $currentUser = auth()->user();
                $canPa = $currentUser?->hasPermissionTo('pa', 'read') ?? false;
                $canBlesscomn = $currentUser?->hasPermissionTo('blesscomn', 'read') ?? false;
                $canKehadiran = $currentUser?->hasPermissionTo('kehadiran_ibadah', 'read') ?? false;
                $canMasterData = $currentUser?->hasPermissionTo('master_data', 'read') ?? false;
                $canMasterBukuPa = $currentUser?->hasPermissionTo('master_buku_pa', 'read') ?? false;
                $canUsers = $currentUser?->hasPermissionTo('users', 'read') ?? false;
                $canNotifications = $currentUser?->hasPermissionTo('notifications', 'read') ?? false;
                $canAuditLogs = $currentUser?->hasPermissionTo('audit_logs', 'read') ?? false;
                $visibleNotifications = collect();
                $latestNotification = null;

                if ($canNotifications && $currentUser?->role) {
                    $visibleNotifications = \App\Models\AppNotification::with('sender')
                        ->where(function ($query) use ($currentUser) {
                            $query->where('target_user_id', $currentUser->id)
                                ->orWhereJsonContains('target_roles', $currentUser->role->name);
                        })
                        ->latest('sent_at')
                        ->limit(5)
                        ->get();
                    $latestNotification = $visibleNotifications->first();
                }
            @endphp

            <div class="sidebar-nav-label">Menu Utama</div>

            @if($canPa)
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->is('dashboard-pa') || request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    Dashboard Laporan PA
                </a>
            @endif

            @if($canBlesscomn)
                <a href="{{ route('dashboard_blesscomn') }}" class="sidebar-nav-item {{ request()->is('dashboard-blesscomn*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Dashboard Blesscomn
                </a>
            @endif

            @if($canKehadiran)
                <a href="{{ route('dashboard_kehadiran_ibadah') }}" class="sidebar-nav-item {{ request()->is('dashboard-kehadiran-ibadah*') ? 'active' : '' }}">
                    <i class="fas fa-chart-area"></i>
                    Dashboard Ibadah
                </a>
            @endif

            @if($canPa)
                <div class="sidebar-nav-group {{ $laporanPaOpen ? 'open' : '' }}">
                    <button type="button" class="sidebar-nav-item sidebar-nav-toggle" onclick="toggleSidebarGroup(this)">
                        <i class="fas fa-file-alt"></i>
                        Laporan PA
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="sidebar-nav-submenu">
                        <a href="{{ route('pembimbing.index') }}" class="sidebar-nav-item {{ request()->is('pembimbing*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            Pembimbing
                        </a>

                        <a href="{{ route('anak_bimbingan.index') }}" class="sidebar-nav-item {{ request()->is('anak_bimbingan*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            Anak PA
                        </a>

                        <a href="{{ route('laporan_pa.index') }}" class="sidebar-nav-item {{ request()->is('laporan_pa*') && !request()->is('laporan-pa/report*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            Laporan PA
                        </a>

                        <a href="{{ route('laporan_pa.report') }}" class="sidebar-nav-item {{ request()->is('laporan-pa/report*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            Report Keaktifan
                        </a>
                    </div>
                </div>
            @endif

            @if($canBlesscomn)
                <div class="sidebar-nav-group {{ $blesscomnOpen ? 'open' : '' }}">
                    <button type="button" class="sidebar-nav-item sidebar-nav-toggle" onclick="toggleSidebarGroup(this)">
                        <i class="fas fa-church"></i>
                        Blesscomn
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="sidebar-nav-submenu">
                        <a href="{{ route('pengurus_blesscomn.index') }}" class="sidebar-nav-item {{ request()->is('pengurus_blesscomn*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i>
                            Pengurus Blesscomn
                        </a>

                        <a href="{{ route('master_blesscomn.index') }}" class="sidebar-nav-item {{ request()->is('master_blesscomn*') ? 'active' : '' }}">
                            <i class="fas fa-church"></i>
                            Master Blesscomn
                        </a>

                        <a href="{{ route('laporan_blesscomn.index') }}" class="sidebar-nav-item {{ request()->is('laporan_blesscomn*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            Laporan Blesscomn
                        </a>
                    </div>
                </div>
            @endif

            @if($canKehadiran)
                <a href="{{ route('kehadiran_ibadah.index') }}" class="sidebar-nav-item {{ request()->is('kehadiran_ibadah*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    Kehadiran Ibadah
                </a>
            @endif

            @if($canMasterData || $canMasterBukuPa)
                <div class="sidebar-nav-group {{ $masterDataOpen ? 'open' : '' }}">
                    <button type="button" class="sidebar-nav-item sidebar-nav-toggle" onclick="toggleSidebarGroup(this)">
                        <i class="fas fa-database"></i>
                        Master Data
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="sidebar-nav-submenu">
                        @if($canMasterData)
                            <a href="{{ route('pelayanan.index') }}" class="sidebar-nav-item {{ request()->is('pelayanan*') ? 'active' : '' }}">
                                <i class="fas fa-hand-holding-heart"></i>
                                Pelayanan
                            </a>

                            <a href="{{ route('wilayah.index') }}" class="sidebar-nav-item {{ request()->is('wilayah*') ? 'active' : '' }}">
                                <i class="fas fa-map-marked-alt"></i>
                                Wilayah
                            </a>
                        @endif

                        @if($canMasterBukuPa)
                            <a href="{{ route('master_buku_pa.index') }}" class="sidebar-nav-item {{ request()->is('master_buku_pa*') ? 'active' : '' }}">
                                <i class="fas fa-book"></i>
                                Master Buku PA
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="sidebar-nav-label">Akun</div>

            @if($canUsers)
                <a href="{{ route('users.index') }}" class="sidebar-nav-item {{ request()->is('users*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    Manajemen User
                </a>
            @endif

            @if($canNotifications)
                <a href="{{ route('notifications.index') }}" class="sidebar-nav-item {{ request()->is('inbox*') ? 'active' : '' }}">
                    <i class="fas fa-inbox"></i>
                    Inbox Broadcast
                    @if($visibleNotifications->count() > 0)
                        <span class="sidebar-nav-badge">{{ $visibleNotifications->count() }}</span>
                    @endif
                </a>
            @endif

            <a href="{{ route('password.edit') }}" class="sidebar-nav-item {{ request()->is('password') ? 'active' : '' }}">
                <i class="fas fa-key"></i>
                Ganti Password
            </a>

            @if($canAuditLogs)
                <a href="{{ route('audit_logs.index') }}" class="sidebar-nav-item {{ request()->is('audit-logs*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    System Event Log
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <p class="sidebar-footer-text">&copy; {{ date('Y') }} GKKD Jakarta</p>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="gkkd-main">
        <!-- Header -->
        <header class="gkkd-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-breadcrumb">
                    <a href="{{ route('dashboard') }}">GKKD</a>
                    <span>/</span>
                    @yield('breadcrumb', 'Dashboard')
                </div>
            </div>
            <div class="header-right">
                <div class="header-date">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                </div>
                @auth
                    @if($canNotifications)
                        <a href="{{ route('notifications.index') }}" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd header-notification" title="Inbox Broadcast">
                            <i class="fas fa-bell"></i>
                            @if($visibleNotifications->count() > 0)
                                <span class="notification-dot">{{ $visibleNotifications->count() }}</span>
                            @endif
                        </a>
                    @endif
                    <div class="header-date" title="{{ auth()->user()->email }}">
                        <i class="fas fa-user-shield"></i>
                        {{ auth()->user()->name }}
                        @if(auth()->user()->role)
                            <span style="color: var(--text-muted);">({{ auth()->user()->role->label }})</span>
                        @endif
                        @if(session('impersonator_id'))
                            <span style="color: var(--warning); font-weight: 700;">Mode Impersonate</span>
                        @endif
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                            <i class="fas fa-sign-out-alt"></i>
                            Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        <!-- Page Content -->
        <main class="gkkd-content">
            @if(session('impersonator_id'))
                <div class="gkkd-alert" style="background: rgba(245, 158, 11, 0.1); color: #92400e; border: 1px solid rgba(245, 158, 11, 0.22); justify-content: space-between; align-items: center;">
                    <div>
                        <i class="fas fa-user-secret"></i>
                        Anda sedang melihat sistem sebagai {{ auth()->user()->name }}. Superadmin asal: {{ session('impersonator_name') }}.
                    </div>
                    <form action="{{ route('impersonate.stop') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                            <i class="fas fa-undo"></i>
                            Kembali
                        </button>
                    </form>
                </div>
            @endif

            @if($latestNotification)
                <div class="gkkd-alert gkkd-alert-notification" id="newNotificationAlert" data-notification-id="{{ $latestNotification->id }}" style="display: none;">
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">Pesan Baru</div>
                        <strong>{{ $latestNotification->title }}</strong>
                        <div style="margin-top: 4px;">{{ \Illuminate\Support\Str::limit($latestNotification->message, 180) }}</div>
                        <div style="font-size: 0.76rem; color: #475569; margin-top: 6px;">
                            Dari {{ $latestNotification->sender?->name ?? 'System' }} - {{ optional($latestNotification->sent_at)->diffForHumans() }}
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('notifications.index') }}" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd">
                            <i class="fas fa-inbox"></i>
                            Buka
                        </a>
                        <button type="button" class="btn-gkkd btn-sm-gkkd btn-outline-gkkd" onclick="dismissNotificationAlert({{ $latestNotification->id }})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="gkkd-alert gkkd-alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="gkkd-alert gkkd-alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // CSRF setup for jQuery AJAX
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        function toggleSidebarGroup(button) {
            button.closest('.sidebar-nav-group').classList.toggle('open');
        }

        function dismissNotificationAlert(notificationId) {
            localStorage.setItem('gkkd:last-dismissed-notification-id', String(notificationId));
            const alert = document.getElementById('newNotificationAlert');
            if (alert) {
                alert.style.display = 'none';
            }
        }

        (function showNewNotificationAlert() {
            const alert = document.getElementById('newNotificationAlert');
            if (!alert) {
                return;
            }

            const notificationId = Number(alert.dataset.notificationId || 0);
            const dismissedId = Number(localStorage.getItem('gkkd:last-dismissed-notification-id') || 0);

            if (notificationId > dismissedId) {
                alert.style.display = 'flex';
            }
        })();

        // Close sidebar on window resize if desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('show');
            }
        });

        /**
         * Ticket 4: Global SweetAlert Delete Handler.
         * Usage: <button class="btn-delete-swal" data-url="/route/id" data-name="Item">
         */
        $(document).on('click', '.btn-delete-swal', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var name = $(this).data('name') || 'data ini';
            var row = $(this).closest('tr');

            Swal.fire({
                title: 'Hapus Data?',
                html: 'Apakah Anda yakin ingin menghapus <strong>' + name + '</strong>?<br><small style="color:#888;">Data yang dihapus dapat dipulihkan oleh admin.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(res) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: res.message || 'Data berhasil dihapus.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // Animate row removal
                            row.fadeOut(400, function() { $(this).remove(); });
                        },
                        error: function(xhr) {
                            var msg = 'Terjadi kesalahan saat menghapus data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                text: msg,
                                icon: 'error',
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    });
                }
            });
        });

        function refreshBulkDeleteState(form) {
            var $form = $(form);
            var total = $form.find('.js-row-select').length;
            var checked = $form.find('.js-row-select:checked').length;

            $form.find('.bulk-selected-count').text(checked);
            $form.find('.js-bulk-delete-button').prop('disabled', checked === 0);
            $form.find('.js-select-all')
                .prop('checked', total > 0 && checked === total)
                .prop('indeterminate', checked > 0 && checked < total);
        }

        $(document).on('change', '.js-select-all', function() {
            var form = $(this).closest('form.bulk-delete-form');
            form.find('.js-row-select').prop('checked', this.checked);
            refreshBulkDeleteState(form);
        });

        $(document).on('change', '.js-row-select', function() {
            refreshBulkDeleteState($(this).closest('form.bulk-delete-form'));
        });

        $(document).on('submit', 'form.bulk-delete-form', function(e) {
            e.preventDefault();

            var form = $(this);
            var selected = form.find('.js-row-select:checked');
            var count = selected.length;
            var label = form.data('resource-label') || 'data';

            if (count === 0) {
                Swal.fire({
                    title: 'Belum ada data dipilih',
                    text: 'Pilih minimal satu data terlebih dahulu.',
                    icon: 'info',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Data Terpilih?',
                html: 'Apakah Anda yakin ingin menghapus <strong>' + count + '</strong> data ' + label + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: res.message || 'Data terpilih berhasil dihapus.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        selected.closest('tr').fadeOut(400, function() {
                            $(this).remove();
                            refreshBulkDeleteState(form);

                            if (form.find('.js-row-select').length === 0) {
                                window.location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        var msg = 'Terjadi kesalahan saat menghapus data terpilih.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Gagal!',
                            text: msg,
                            icon: 'error',
                            confirmButtonColor: '#2563eb'
                        });
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
