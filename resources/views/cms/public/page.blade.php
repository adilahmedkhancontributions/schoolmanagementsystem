<x-public-site-layout :school="$school" :pages="$pages" :title="($page->meta_title ?: $page->title).' — '.$school->name" :metaDescription="$page->meta_description">
    <article class="max-w-3xl mx-auto px-4 sm:px-6 py-14">
        <h1 class="font-heading text-3xl sm:text-4xl font-extrabold text-slate-900">{{ $page->title }}</h1>
        <div class="cms-content mt-6">
            {!! $page->content !!}
        </div>
    </article>
</x-public-site-layout>
