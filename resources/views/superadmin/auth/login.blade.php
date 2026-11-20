<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Login - Blue Iris</title>
    <!-- Google Fonts & Tailwind -->
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex h-14 w-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 items-center justify-center text-white font-extrabold text-2xl shadow-xl shadow-blue-500/20 mb-3 ring-4 ring-white/10">
                BI
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Blue Iris SuperAdmin</h1>
            <p class="text-sm text-slate-400 mt-1">Sign in to manage multi-tenant platforms & databases</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-8 space-y-6">
            <!-- Flash Message -->
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-200 text-sm font-medium flex items-center space-x-2">
                    <svg class="h-5 w-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-200 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <div class="flex items-center space-x-2">
                            <svg class="h-4 w-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Superadmin Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', 'superadmin@demo.com') }}" required autofocus
                        class="w-full bg-slate-900/60 border border-slate-700/80 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Password</label>
                    </div>
                    <input type="password" name="password" id="password" required value="Test@123"
                        class="w-full bg-slate-900/60 border border-slate-700/80 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-900/60 border-slate-700 text-blue-600 focus:ring-blue-500 h-4 w-4">
                        <span class="ml-2">Remember me</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-blue-600/30 transition duration-200">
                    Sign In to SuperAdmin
                </button>
            </form>

            <div class="pt-2 border-t border-white/5 text-center text-xs text-slate-400">
                <span class="inline-block px-2 py-1 bg-white/5 rounded-lg font-mono">Default Demo: superadmin@demo.com / Test@123</span>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">&copy; {{ date('Y') }} Blue Iris Multi-Tenant Central System</p>
    </div>
</body>
</html>