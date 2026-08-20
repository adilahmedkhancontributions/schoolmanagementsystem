<x-public-site-layout :school="$school" :pages="$pages" :title="'Blog — '.$school->name">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-14">
        <h1 class="font-heading text-3xl font-extrabold text-slate-900 mb-8">Blog</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($posts as $post)
                <a href="{{ route('public.site.blog.show', [$school, $post->slug]) }}" class="card card-hoverable overflow-hidden block">
                    @if ($post->featuredImageUrl())
                        <img src="{{ $post->featuredImageUrl() }}" class="h-40 w-full object-cover" alt="{{ $post->title }}">
                    @endif
                    <div class="p-5">
                        <p class="text-xs text-slate-400">{{ $post->published_at->format('d M Y') }}</p>
                        <h2 class="font-heading font-bold text-slate-900 mt-1">{{ $post->title }}</h2>
                        @if ($post->excerpt)
                            <p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $post->excerpt }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <p class="text-slate-500 col-span-full text-center py-10">No blog posts yet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $posts->links() }}</div>
    </section>
</x-public-site-layout>
