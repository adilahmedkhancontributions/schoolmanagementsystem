<x-public-site-layout :school="$school" :pages="$pages" :title="$school->name" :metaDescription="$school->hero_subheadline">
    <section class="relative overflow-hidden brand-gradient text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-16 sm:py-24 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <h1 class="font-heading text-3xl sm:text-5xl font-extrabold leading-tight">
                    {{ $school->hero_headline ?: 'Welcome to '.$school->name }}
                </h1>
                <p class="mt-4 text-lg text-white/85 max-w-xl">
                    {{ $school->hero_subheadline ?: 'Nurturing every student to reach their full potential.' }}
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#contact" class="inline-flex items-center justify-center gap-2 rounded-lg px-5 min-h-touch text-sm font-semibold bg-white text-slate-900 hover:bg-slate-100 transition-colors">Get in touch</a>
                    <a href="{{ route('public.site.blog', $school) }}" class="inline-flex items-center justify-center gap-2 rounded-lg px-5 min-h-touch text-sm font-semibold border border-white/40 text-white hover:bg-white/10 transition-colors">Read our blog</a>
                </div>
            </div>
            @if ($school->heroImageUrl())
                <img src="{{ $school->heroImageUrl() }}" class="rounded-2xl shadow-card-hover w-full h-64 sm:h-80 object-cover" alt="{{ $school->name }}">
            @endif
        </div>
    </section>

    @if ($announcements->isNotEmpty())
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-14">
            <h2 class="font-heading text-2xl font-bold text-slate-900 mb-6">Announcements</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                @foreach ($announcements as $announcement)
                    <div class="card p-5">
                        <p class="text-xs text-slate-400">{{ $announcement->published_at->format('d M Y') }}</p>
                        <h3 class="font-heading font-bold text-slate-900 mt-1">{{ $announcement->title }}</h3>
                        <p class="text-sm text-slate-600 mt-2 line-clamp-3">{{ $announcement->body }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($pages->isNotEmpty())
        <section class="bg-slate-50 py-14">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <h2 class="font-heading text-2xl font-bold text-slate-900 mb-6">About us</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($pages as $page)
                        <a href="{{ route('public.site.page', [$school, $page->slug]) }}" class="card card-hoverable p-5 block">
                            <h3 class="font-heading font-bold text-slate-900">{{ $page->title }}</h3>
                            @if ($page->meta_description)
                                <p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $page->meta_description }}</p>
                            @endif
                            <span class="inline-flex items-center gap-1 text-sm font-medium brand-text mt-3">Read more <i class="fa-solid fa-arrow-right text-xs"></i></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($images->isNotEmpty())
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-14">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-heading text-2xl font-bold text-slate-900">Gallery</h2>
                <a href="{{ route('public.site.gallery', $school) }}" class="text-sm font-medium brand-text hover:underline">View all</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ($images as $image)
                    <img src="{{ $image->imageUrl() }}" class="h-32 sm:h-40 w-full object-cover rounded-xl" alt="{{ $image->caption }}">
                @endforeach
            </div>
        </section>
    @endif

    @if ($posts->isNotEmpty())
        <section class="bg-slate-50 py-14">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-heading text-2xl font-bold text-slate-900">From our blog</h2>
                    <a href="{{ route('public.site.blog', $school) }}" class="text-sm font-medium brand-text hover:underline">View all</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @foreach ($posts as $post)
                        <a href="{{ route('public.site.blog.show', [$school, $post->slug]) }}" class="card card-hoverable overflow-hidden block">
                            @if ($post->featuredImageUrl())
                                <img src="{{ $post->featuredImageUrl() }}" class="h-40 w-full object-cover" alt="{{ $post->title }}">
                            @endif
                            <div class="p-5">
                                <p class="text-xs text-slate-400">{{ $post->published_at->format('d M Y') }}</p>
                                <h3 class="font-heading font-bold text-slate-900 mt-1">{{ $post->title }}</h3>
                                @if ($post->excerpt)
                                    <p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $post->excerpt }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="contact" class="max-w-6xl mx-auto px-4 sm:px-6 py-14">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                <h2 class="font-heading text-2xl font-bold text-slate-900">Get in touch</h2>
                <p class="text-sm text-slate-500 mt-2">Questions about admissions or anything else? Send us a message.</p>

                @if (session('contactSent'))
                    <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                        Thanks for reaching out! We'll get back to you soon.
                    </div>
                @endif

                <form method="POST" action="{{ route('public.site.contact', $school) }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Leave this field empty</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-floating-input label="Your name" name="name" value="{{ old('name') }}" />
                        <x-floating-input label="Email" name="email" type="email" value="{{ old('email') }}" />
                    </div>
                    <x-floating-input label="Phone (optional)" name="phone" value="{{ old('phone') }}" />
                    <div>
                        <textarea name="message" rows="5" placeholder="Your message" required
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">{{ old('message') }}</textarea>
                    </div>
                    @error('name') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                    @error('email') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                    @error('message') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                    <button type="submit" class="btn-primary">Send message</button>
                </form>
            </div>

            <div class="card p-6">
                <h3 class="font-heading font-bold text-slate-900 mb-4">School information</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex gap-3">
                        <dt class="text-slate-400 w-24 shrink-0">Address</dt>
                        <dd class="text-slate-700">{{ $school->address }}{{ $school->city ? ', '.$school->city : '' }}{{ $school->country ? ', '.$school->country : '' }}</dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="text-slate-400 w-24 shrink-0">Email</dt>
                        <dd class="text-slate-700">{{ $school->email ?? '—' }}</dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="text-slate-400 w-24 shrink-0">Phone</dt>
                        <dd class="text-slate-700">{{ $school->phone ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>
</x-public-site-layout>
