<x-public-site-layout :school="$school" :pages="$pages" :title="'Gallery — '.$school->name">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-14">
        <h1 class="font-heading text-3xl font-extrabold text-slate-900 mb-8">Gallery</h1>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse ($images as $image)
                <figure class="rounded-xl overflow-hidden">
                    <img src="{{ $image->imageUrl() }}" class="h-40 sm:h-48 w-full object-cover" alt="{{ $image->caption }}">
                    @if ($image->caption)
                        <figcaption class="text-xs text-slate-500 mt-1.5">{{ $image->caption }}</figcaption>
                    @endif
                </figure>
            @empty
                <p class="text-slate-500 col-span-full text-center py-10">No images yet.</p>
            @endforelse
        </div>
    </section>
</x-public-site-layout>
