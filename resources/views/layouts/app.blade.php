<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 font-sans text-slate-800 antialiased">
        <div class="min-h-screen">
            <div class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('home') }}" class="text-xl font-black text-slate-900">NexaCart</a>
                        <nav class="hidden items-center gap-5 text-sm font-medium text-slate-600 md:flex">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900">Dashboard</a>
                            <a href="{{ route('admin.products') }}" class="hover:text-slate-900">Products</a>
                            <a href="{{ route('admin.categories') }}" class="hover:text-slate-900">Categories</a>
                            <a href="{{ route('admin.orders') }}" class="hover:text-slate-900">Orders</a>
                        </nav>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Storefront</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

            @isset($header)
                <header class="bg-white shadow-sm">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </body>
</html>
