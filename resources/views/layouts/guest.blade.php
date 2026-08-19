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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50">
            <div class="flex items-center gap-2">
                <div class="h-10 w-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-heading font-bold text-lg">S</div>
                <span class="font-heading font-bold text-xl text-slate-900">{{ config('app.name', 'School Management System') }}</span>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
