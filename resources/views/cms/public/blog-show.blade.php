<x-public-site-layout :school="$school" :pages="$pages" :title="$post->title.' — '.$school->name" :metaDescription="$post->excerpt">
    <article class="max-w-3xl mx-auto px-4 sm:px-6 py-14">
        <a href="{{ route('public.site.blog', $school) }}" class="text-sm font-medium brand-text hover:underline">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to blog
        </a>

        <p class="text-xs text-slate-400 mt-4">{{ $post->published_at->format('d M Y') }}{{ $post->author ? ' · By '.$post->author->name : '' }}</p>
        <h1 class="font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 mt-1">{{ $post->title }}</h1>

        @if ($post->featuredImageUrl())
            <img src="{{ $post->featuredImageUrl() }}" class="mt-6 rounded-2xl w-full h-64 sm:h-80 object-cover" alt="{{ $post->title }}">
        @endif

        <div class="cms-content mt-6">
            {!! $post->content !!}
        </div>
    </article>
</x-public-site-layout>
