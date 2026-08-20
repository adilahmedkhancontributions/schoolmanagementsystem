@props(['school', 'pages' => [], 'title' => null, 'metaDescription' => null])

<!DOCTYPE html>
<html lang="en" style="--brand-primary: {{ $school->primary_color }}; --brand-secondary: {{ $school->secondary_color }};">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="{{ $school->primary_color }}">

        <title>{{ $title ?? $school->name }}</title>
        @if ($metaDescription)
            <meta name="description" content="{{ $metaDescription }}">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|nunito-sans:700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-white">
        <header class="border-b border-slate-100 sticky top-0 bg-white/90 backdrop-blur z-30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <a href="{{ route('public.site.home', $school) }}" class="flex items-center gap-2 min-w-0">
                    @if ($school->logoUrl())
                        <img src="{{ $school->logoUrl() }}" class="h-9 w-9 rounded-lg object-cover" alt="{{ $school->name }}">
                    @else
                        <div class="h-9 w-9 rounded-lg brand-gradient text-white flex items-center justify-center font-heading font-bold">{{ strtoupper(substr($school->name, 0, 1)) }}</div>
                    @endif
                    <span class="font-heading font-bold text-slate-900 truncate">{{ $school->name }}</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('public.site.home', $school) }}" class="hover:text-slate-900">Home</a>
                    @foreach ($pages as $page)
                        <a href="{{ route('public.site.page', [$school, $page->slug]) }}" class="hover:text-slate-900">{{ $page->title }}</a>
                    @endforeach
                    <a href="{{ route('public.site.blog', $school) }}" class="hover:text-slate-900">Blog</a>
                    <a href="{{ route('public.site.gallery', $school) }}" class="hover:text-slate-900">Gallery</a>
                    <a href="{{ route('public.site.home', $school) }}#contact" class="hover:text-slate-900">Contact</a>
                </nav>

                <a href="{{ route('login') }}" class="btn-primary hidden sm:inline-flex">Sign in</a>
            </div>
        </header>

        {{ $slot }}

        <footer class="border-t border-slate-100 mt-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                <div>
                    <p class="font-heading font-bold text-slate-900">{{ $school->name }}</p>
                    <p class="text-slate-500 mt-2">{{ $school->address }}{{ $school->city ? ', '.$school->city : '' }}{{ $school->country ? ', '.$school->country : '' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-slate-700">Contact</p>
                    <p class="text-slate-500 mt-2">{{ $school->email }}</p>
                    <p class="text-slate-500">{{ $school->phone }}</p>
                </div>
                <div>
                    <p class="font-semibold text-slate-700">Links</p>
                    <div class="flex flex-col gap-1 mt-2 text-slate-500">
                        <a href="{{ route('public.site.blog', $school) }}" class="hover:text-slate-900">Blog</a>
                        <a href="{{ route('public.site.gallery', $school) }}" class="hover:text-slate-900">Gallery</a>
                        <a href="{{ route('login') }}" class="hover:text-slate-900">Sign in</a>
                    </div>
                </div>
            </div>
            <p class="text-center text-xs text-slate-400 pb-6">&copy; {{ date('Y') }} {{ $school->name }}. All rights reserved.</p>
        </footer>
    </body>
</html>
