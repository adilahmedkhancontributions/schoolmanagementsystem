<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'School Management System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|nunito-sans:700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-white">
        <div class="relative overflow-hidden bg-slate-950 text-white">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -top-40 -left-32 h-96 w-96 rounded-full bg-indigo-600/30 blur-3xl"></div>
                <div class="absolute top-1/3 -right-24 h-96 w-96 rounded-full bg-sky-500/30 blur-3xl"></div>
            </div>

            <header class="relative max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-10 w-10 rounded-xl brand-gradient text-white flex items-center justify-center font-heading font-bold text-lg">S</div>
                    <span class="font-heading font-bold text-lg">{{ config('app.name', 'School Management System') }}</span>
                </div>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-200 hover:text-white px-3 py-2">Log in</a>
                        <a href="{{ route('login') }}" class="btn-primary">Get Started</a>
                    @endauth
                </nav>
            </header>

            <section class="relative max-w-5xl mx-auto px-6 pt-16 pb-24 text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-indigo-200">
                    <i class="fa-solid fa-star"></i> All-in-one school platform
                </span>
                <h1 class="mt-6 font-heading text-4xl sm:text-6xl font-extrabold leading-tight">
                    Run your entire school<br class="hidden sm:block"> from one modern dashboard.
                </h1>
                <p class="mt-6 text-lg text-slate-300 max-w-2xl mx-auto">
                    Attendance, fees, examinations, communication and CMS &mdash; built for
                    admins, teachers, students and parents, with role-based dashboards for everyone.
                </p>
                <div class="mt-8 flex items-center justify-center gap-3">
                    <a href="{{ route('login') }}" class="btn-primary px-6">Sign in to your account</a>
                </div>
            </section>
        </div>

        <section class="max-w-7xl mx-auto px-6 py-20">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="font-heading text-3xl font-bold text-slate-900">Built for every role in your school</h2>
                <p class="mt-3 text-slate-500">Each person gets a focused dashboard with exactly what they need.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                @foreach ([
                    ['icon' => 'fa-user-shield', 'title' => 'Super Admin', 'desc' => 'Manage every school, branding and system settings from one place.'],
                    ['icon' => 'fa-school', 'title' => 'School Admin', 'desc' => 'Students, teachers, classes, fees, exams, CMS and announcements.'],
                    ['icon' => 'fa-chalkboard-user', 'title' => 'Teacher', 'desc' => 'Mark attendance, manage grades and stay in touch with parents.'],
                    ['icon' => 'fa-user-graduate', 'title' => 'Student', 'desc' => 'Timetable, attendance, grades and fee status at a glance.'],
                    ['icon' => 'fa-child-reaching', 'title' => 'Parent', 'desc' => 'Track your children\'s attendance, performance and payments.'],
                ] as $role)
                    <div class="stat-card flex-col items-start gap-4">
                        <div class="h-11 w-11 rounded-xl brand-gradient text-white flex items-center justify-center">
                            <i class="fa-solid {{ $role['icon'] }}"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-slate-900">{{ $role['title'] }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $role['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <footer class="border-t border-slate-100 py-8 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'School Management System') }}. All rights reserved.
        </footer>
    </body>
</html>
