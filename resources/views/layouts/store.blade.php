<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title.' | '.config('app.name') : config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-800 antialiased">
        <header class="border-b border-slate-200 bg-white/90 backdrop-blur-sm sticky top-0 z-40">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-2xl font-black tracking-tight text-slate-900">NexaCart</a>

                <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex">
                    <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
                    <a href="{{ route('products.index') }}" class="hover:text-slate-900">Shop</a>
                    <a href="{{ route('cart.index') }}" class="hover:text-slate-900">Cart</a>
                    @auth
                        <a href="{{ route('orders.index') }}" class="hover:text-slate-900">Orders</a>
                    @endauth
                    @auth
                        @if (Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900">Admin</a>
                        @endif
                    @endauth
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center rounded-full border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:border-slate-300">
                        Cart
                        <span class="ml-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-900 px-1 text-xs text-white">
                            {{ count(session('cart', [])) }}
                        </span>
                    </a>

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300">Login</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Register</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-slate-600 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <p>© {{ date('Y') }} NexaCart. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="{{ route('products.index') }}" class="hover:text-slate-900">Shop</a>
                    <a href="{{ route('orders.index') }}" class="hover:text-slate-900">My Orders</a>
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900">Admin</a>
                </div>
            </div>
        </footer>
    </body>
</html>
