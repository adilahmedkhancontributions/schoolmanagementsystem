<!DOCTYPE html>
@php($school = auth()->user()->school)
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--brand-primary: {{ $school?->primary_color ?? '#4f46e5' }}; --brand-secondary: {{ $school?->secondary_color ?? '#0ea5e9' }};">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="{{ $school?->primary_color ?? '#4f46e5' }}">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="{{ $school?->name ?? config('app.name', 'SMS') }}">
        <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
        @PwaHead

        <title>{{ $title ?? config('app.name', 'School Management System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800|nunito-sans:400,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
        @php($role = auth()->user()->getRoleNames()->first())

        <div class="min-h-screen flex">
            <!-- Desktop sidebar -->
            <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 bg-slate-900 text-white sticky top-0 h-screen">
                <div class="flex items-center gap-2 px-5 h-16 border-b border-white/10">
                    @if ($school?->logoUrl())
                        <img src="{{ $school->logoUrl() }}" alt="{{ $school->name }}" class="h-8 w-8 rounded-lg object-cover">
                    @else
                        <div class="h-8 w-8 rounded-lg brand-gradient flex items-center justify-center font-heading font-bold">S</div>
                    @endif
                    <span class="font-heading font-bold text-lg tracking-tight truncate">{{ $school?->name ?? config('app.name', 'SMS') }}</span>
                </div>
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    @foreach (\App\Support\Navigation::forRole($role) as $item)
                        @if ($item['route'] && \Illuminate\Support\Facades\Route::has($item['route']))
                            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @else
                            <span class="sidebar-link opacity-40 cursor-not-allowed" title="Coming soon">
                                <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                                <span>{{ $item['label'] }}</span>
                            </span>
                        @endif
                    @endforeach
                </nav>
                <div class="px-3 py-4 border-t border-white/10">
                    <span class="sidebar-link opacity-70">
                        <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                        <span>{{ \App\Support\Navigation::roleLabel($role) }}</span>
                    </span>
                </div>
            </aside>

            <!-- Mobile sidebar overlay -->
            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden" role="dialog">
                <div class="fixed inset-0 bg-slate-900/60" @click="sidebarOpen = false"></div>
                <aside class="fixed inset-y-0 left-0 w-72 bg-slate-900 text-white flex flex-col">
                    <div class="flex items-center justify-between gap-2 px-5 h-16 border-b border-white/10">
                        <div class="flex items-center gap-2">
                            @if ($school?->logoUrl())
                                <img src="{{ $school->logoUrl() }}" alt="{{ $school->name }}" class="h-8 w-8 rounded-lg object-cover">
                            @else
                                <div class="h-8 w-8 rounded-lg brand-gradient flex items-center justify-center font-heading font-bold">S</div>
                            @endif
                            <span class="font-heading font-bold text-lg truncate">{{ $school?->name ?? config('app.name', 'SMS') }}</span>
                        </div>
                        <button @click="sidebarOpen = false" class="min-h-touch min-w-touch text-slate-300">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                        @foreach (\App\Support\Navigation::forRole($role) as $item)
                            @if ($item['route'] && \Illuminate\Support\Facades\Route::has($item['route']))
                                <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @else
                                <span class="sidebar-link opacity-40 cursor-not-allowed">
                                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                                    <span>{{ $item['label'] }}</span>
                                </span>
                            @endif
                        @endforeach
                    </nav>
                </aside>
            </div>

            <div class="flex-1 flex flex-col min-w-0">
                <!-- Topbar -->
                <header class="sticky top-0 z-30 bg-white border-b border-slate-200 h-16 flex items-center gap-4 px-4 sm:px-6">
                    <button @click="sidebarOpen = true" class="lg:hidden min-h-touch min-w-touch text-slate-600">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>

                    <div class="flex-1 min-w-0">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 min-h-touch rounded-lg px-2 hover:bg-slate-100">
                            <div class="h-9 w-9 rounded-full brand-gradient text-white flex items-center justify-center font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>

                        <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 rounded-lg bg-white shadow-card-hover border border-slate-100 py-1 z-40">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-user mr-2 text-slate-400"></i> {{ __('Profile') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50">
                                    <i class="fa-solid fa-right-from-bracket mr-2"></i> {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Page content -->
                <main class="flex-1 p-4 sm:p-6 pb-24 lg:pb-6">
                    {{ $slot }}
                </main>

                <!-- Mobile bottom nav -->
                <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-200 flex">
                    @foreach (array_slice(\App\Support\Navigation::forRole($role), 0, 5) as $item)
                        @if ($item['route'] && \Illuminate\Support\Facades\Route::has($item['route']))
                            <a href="{{ route($item['route']) }}" class="bottom-nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                                <i class="fa-solid {{ $item['icon'] }} text-lg"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @else
                            <span class="bottom-nav-link opacity-40">
                                <i class="fa-solid {{ $item['icon'] }} text-lg"></i>
                                <span>{{ $item['label'] }}</span>
                            </span>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
