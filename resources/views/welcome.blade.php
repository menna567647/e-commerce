<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main style="min-height: 100vh; display: grid; place-items: center; padding: 2rem;">
            <div style="max-width: 36rem; text-align: center; background: #fff; border: 1px solid #e2e8f0; border-radius: 1.5rem; box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08); padding: 2rem;">
                <p style="margin: 0 0 0.75rem; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #4f46e5;">NexaCart</p>
                <h1 style="margin: 0 0 1rem; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.1;">Welcome to your storefront</h1>
                <p style="margin: 0 0 1.5rem; color: #475569; line-height: 1.6;">This project has been migrated away from Tailwind to a standard CSS setup while keeping the Laravel storefront and API working as normal.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Continue to storefront</a>
            </div>
        </main>
    </body>
</html>
