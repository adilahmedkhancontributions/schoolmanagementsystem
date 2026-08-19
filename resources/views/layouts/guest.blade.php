<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Management System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|nunito-sans:700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="relative min-h-screen flex flex-col sm:justify-center items-center overflow-hidden pt-10 sm:pt-0 bg-slate-950">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-indigo-600/30 blur-3xl"></div>
                <div class="absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-sky-500/30 blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.08)_1px,transparent_0)] [background-size:24px_24px]"></div>
            </div>

            <div class="relative flex flex-col items-center">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-xl brand-gradient text-white flex items-center justify-center font-heading font-bold text-lg shadow-card-hover">S</div>
                    <span class="font-heading font-bold text-xl text-white">{{ config('app.name', 'School Management System') }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-400">Everything your school needs, in one place.</p>
            </div>

            <div class="relative w-full sm:max-w-md mt-8 px-6 sm:px-8 py-8 mx-4 sm:mx-0 rounded-2xl bg-white shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)]">
                {{ $slot }}
            </div>

            <p class="relative mt-8 text-xs text-slate-500">&copy; {{ date('Y') }} {{ config('app.name', 'School Management System') }}. All rights reserved.</p>
        </div>
    </body>
</html>
