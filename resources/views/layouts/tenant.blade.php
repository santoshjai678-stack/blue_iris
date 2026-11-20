<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Tenant Portal') - {{ tenant('name') ?? 'Blue Iris' }}</title>
    <!-- Modern Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #e0e7ff;
            --accent: #06b6d4;
            --bg: #f8fafc;
            --surface: #ffffff;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #334155;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius: 12px;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.07);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        body.sidebar-open {
            overflow: hidden;
        }

        /* Sidebar Overlay Backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(3px);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: #f8fafc;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 999;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #1e293b;
        }

        .brand-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            color: white;
            box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
            flex-shrink: 0;
        }

        .brand-text {
            min-width: 0;
            overflow: hidden;
        }

        .brand-text h2 {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-text span {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            display: block;
        }

        .btn-sidebar-close {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #cbd5e1;
            font-size: 18px;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-sidebar-close:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .sidebar-menu {
            padding: 16px 10px;
            list-style: none;
            overflow-y: auto;
            flex-grow: 1;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        .menu-header {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            padding: 12px 14px 6px;
        }

        .nav-item {
            margin-bottom: 3px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: #ffffff;
            transform: translateX(2px);
        }

        .nav-link.active {
            background: var(--primary);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .nav-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 14px 18px;
            border-top: 1px solid #1e293b;
            background: #0b1120;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-info {
            min-width: 0;
            overflow: hidden;
        }

        .user-info h4 {
            font-size: 13px;
            font-weight: 600;
            color: #f1f5f9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info p {
            font-size: 11px;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: 260px;
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
            min-width: 0;
            transition: all 0.3s ease;
        }

        /* Topbar */
        .topbar {
            height: 64px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 12px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .btn-hamburger {
            display: none;
            background: #f1f5f9;
            border: 1px solid var(--border);
            color: var(--text-main);
            width: 38px;
            height: 38px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.2s;
        }

        .btn-hamburger:hover {
            background: #e2e8f0;
        }

        .topbar-left h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .tenant-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-logout:hover {
            background: #fef2f2;
            color: var(--danger);
            border-color: #fecaca;
        }

        /* Content Body */
        .content-body {
            padding: 24px;
            flex-grow: 1;
            width: 100%;
            min-width: 0;
        }

        /* Generic Cards */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
            min-width: 0;
        }

        /* Form Controls */
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px;
            color: var(--text-main);
            background: #ffffff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--primary);
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transition: background 0.2s, transform 0.1s;
            white-space: nowrap;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            transition: opacity 0.2s;
        }

        .btn-action:hover {
            opacity: 0.85;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        /* Table Responsive container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8fafc;
            padding: 12px 14px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        td {
            padding: 12px 14px;
            font-size: 13px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:hover td {
            background: #f8fafc;
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper {
            padding: 12px 0 0 0;
            font-size: 13px;
            width: 100%;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 14px;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 6px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            margin: 0 4px;
            outline: none;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 7px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            margin-left: 8px;
            outline: none;
            transition: border-color 0.2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary);
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 14px;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 12px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 11px !important;
            margin: 0 2px !important;
            border-radius: 6px !important;
            border: 1px solid var(--border) !important;
            background: #fff !important;
            color: var(--text-main) !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: #fff !important;
            border-color: var(--primary) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
            border-color: #c7d2fe !important;
        }

        .dataTables_wrapper .dataTables_processing {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            box-shadow: var(--shadow-lg) !important;
            color: var(--primary) !important;
            font-weight: 700 !important;
            padding: 10px !important;
        }

        /* ------------------------------------------------------------- */
        /* RESPONSIVE MEDIA QUERIES (MOBILE, TABLET, DESKTOP)            */
        /* ------------------------------------------------------------- */

        /* Tablets & Mobile (Screen width <= 991px) */
        @media (max-width: 991px) {
            .sidebar {
                width: 280px;
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .btn-sidebar-close {
                display: inline-flex;
            }

            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                min-width: 0 !important;
            }

            .btn-hamburger {
                display: inline-flex;
            }

            .topbar {
                padding: 0 16px;
                height: 60px;
            }

            .topbar-left h1 {
                font-size: 16px;
            }

            .tenant-badge {
                display: none;
            }

            .content-body {
                padding: 16px 12px;
            }
        }

        /* Small Tablets & Mobile Devices (Screen width <= 768px) */
        @media (max-width: 768px) {
            .content-body {
                padding: 14px 10px;
            }

            /* Stack Filter Bars and Toolbar */
            .filter-toolbar-container {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
            }

            .filter-form-wrapper {
                flex-direction: column !important;
                width: 100% !important;
                align-items: stretch !important;
            }

            .filter-form-wrapper .form-input,
            .filter-form-wrapper select,
            .filter-form-wrapper input {
                width: 100% !important;
                min-width: 0 !important;
            }

            .action-bar-btn {
                width: 100% !important;
                justify-content: center !important;
                text-align: center !important;
            }

            /* KPI Grid Cards */
            .kpi-stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
            }

            /* DataTables Elements on Mobile */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                width: 100% !important;
                float: none !important;
                text-align: left !important;
                margin-bottom: 10px !important;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                margin-top: 4px !important;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none !important;
                text-align: center !important;
                width: 100% !important;
            }

            .card {
                padding: 14px 10px !important;
            }

            /* Modal and Form grids */
            .form-grid-2 {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }
        }

        /* Extra Small Phones (Screen width <= 480px) */
        @media (max-width: 480px) {
            .kpi-stats-grid {
                grid-template-columns: 1fr !important;
            }

            .topbar-left h1 {
                font-size: 15px;
            }

            .topbar-right .btn-logout span {
                display: none;
            }

            .topbar-right .btn-logout::before {
                content: '🚪';
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="tenantSidebar">
        <div class="sidebar-brand">
            <div class="brand-wrapper">
                <div class="brand-logo">B</div>
                <div class="brand-text">
                    <h2>{{ tenant('name') ?? 'Blue Iris' }}</h2>
                    <span>Tenant Portal</span>
                </div>
            </div>
            <button type="button" class="btn-sidebar-close" id="sidebarClose" aria-label="Close sidebar">✕</button>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">Access & Security</li>
            <li class="nav-item">
                <a href="{{ route('tenant.system-users.index') }}" class="nav-link {{ request()->routeIs('tenant.system-users.*') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span>
                    <span>System Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.roles.index') }}" class="nav-link {{ request()->routeIs('tenant.roles.*') ? 'active' : '' }}">
                    <span class="nav-icon">🔑</span>
                    <span>Roles & Permissions</span>
                </a>
            </li>

            <li class="menu-header">Overview</li>
            <li class="nav-item">
                <a href="{{ route('tenant.dashboard') }}" class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="menu-header">Store & Operations</li>
            <li class="nav-item">
                <a href="{{ route('tenant.salons.index') }}" class="nav-link {{ request()->routeIs('tenant.salons.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏪</span>
                    <span>Stores / Salons</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.employees.index') }}" class="nav-link {{ request()->routeIs('tenant.employees.*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span>
                    <span>Employees</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.designations.index') }}" class="nav-link {{ request()->routeIs('tenant.designations.*') ? 'active' : '' }}">
                    <span class="nav-icon">💼</span>
                    <span>Designations</span>
                </a>
            </li>

            <li class="menu-header">Tasks & Surveys</li>
            <li class="nav-item">
                <a href="{{ route('tenant.tasks.index') }}" class="nav-link {{ request()->routeIs('tenant.tasks.*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span>
                    <span>Daily Survey Tasks</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.task-categories.index') }}" class="nav-link {{ request()->routeIs('tenant.task-categories.*') ? 'active' : '' }}">
                    <span class="nav-icon">📂</span>
                    <span>Task Categories</span>
                </a>
            </li>

            <li class="menu-header">Reports & Analytics</li>
            <li class="nav-item">
                <a href="{{ route('tenant.reports.index') }}" class="nav-link {{ request()->routeIs('tenant.reports.index') ? 'active' : '' }}">
                    <span class="nav-icon">📈</span>
                    <span>Analytics Overview</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.reports.task-compliance') }}" class="nav-link {{ request()->routeIs('tenant.reports.task-compliance') ? 'active' : '' }}">
                    <span class="nav-icon">🗓️</span>
                    <span>Task Compliance</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.reports.survey-responses') }}" class="nav-link {{ request()->routeIs('tenant.reports.survey-responses') ? 'active' : '' }}">
                    <span class="nav-icon">📑</span>
                    <span>Audit Responses</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="avatar">{{ substr(auth()->guard('web')->user()->first_name ?? 'A', 0, 1) }}</div>
                <div class="user-info">
                    <h4>{{ auth()->guard('web')->user()->first_name ?? 'Admin' }} {{ auth()->guard('web')->user()->last_name ?? '' }}</h4>
                    <p>{{ auth()->guard('web')->user()->email ?? 'admin' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="btn-hamburger" id="sidebarToggle" aria-label="Toggle navigation">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="topbar-right">
                <span class="tenant-badge">🏢 {{ tenant('name') ?? tenant('id') }}</span>
                <form action="{{ route('tenant.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;">
                    <span>ℹ️</span> {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function() {
        // Mobile Sidebar Drawer Toggle
        $('#sidebarToggle').on('click', function() {
            $('#tenantSidebar').addClass('show');
            $('#sidebarBackdrop').addClass('show');
            $('body').addClass('sidebar-open');
        });

        $('#sidebarClose, #sidebarBackdrop').on('click', function() {
            $('#tenantSidebar').removeClass('show');
            $('#sidebarBackdrop').removeClass('show');
            $('body').removeClass('sidebar-open');
        });

        // Close sidebar on link click when on mobile
        $('.sidebar-menu .nav-link').on('click', function() {
            if ($(window).width() <= 991) {
                $('#tenantSidebar').removeClass('show');
                $('#sidebarBackdrop').removeClass('show');
                $('body').removeClass('sidebar-open');
            }
        });
    });
    </script>

    @yield('scripts')
</body>
</html>