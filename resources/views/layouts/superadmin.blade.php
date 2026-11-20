<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Superadmin Portal') - Blue Iris</title>
    <!-- Google Font & Tailwind CSS CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col font-sans">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center space-x-3">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            BI
                        </div>
                        <span class="text-xl font-extrabold bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">Blue Iris SuperAdmin</span>
                    </a>
                    <nav class="hidden md:flex space-x-1">
                        <a href="{{ route('superadmin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('superadmin.tenants.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('superadmin.tenants.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Tenants
                        </a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        Central System
                    </span>
                    <div class="flex items-center space-x-3 border-l border-slate-200 pl-4">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-slate-900">{{ Auth::guard('superadmin')->user()?->name ?? 'Super Admin' }}</div>
                            <div class="text-[10px] text-slate-500">{{ Auth::guard('superadmin')->user()?->email }}</div>
                        </div>
                        <form method="POST" action="{{ route('superadmin.logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-xs font-medium rounded-lg text-slate-700 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 transition">
                                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Notifications Alert -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 w-full">
        @if(session('success'))
            <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-200 mb-6 flex items-start space-x-3 shadow-xs">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div class="text-sm text-emerald-800 font-medium">{!! session('success') !!}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl bg-rose-50 p-4 border border-rose-200 mb-6 flex items-start space-x-3 shadow-xs">
                <svg class="h-5 w-5 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <div class="text-sm text-rose-800 font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-rose-50 p-4 border border-rose-200 mb-6 shadow-xs">
                <ul class="list-disc list-inside text-sm text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 w-full">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Blue Iris Multi-Tenant Central System. Laravel {{ app()->version() }}.
        </div>
    </footer>
    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <style>
        .dataTables_wrapper { padding: 12px 0; font-size: 13px; }
        .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
            padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important; color: #fff !important; border-color: #4f46e5 !important; border-radius: 6px;
        }
    </style>

    @yield('scripts')
</body>
</html>