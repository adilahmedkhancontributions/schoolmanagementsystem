<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-bullhorn text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Announcements</h1>
                <p class="text-sm text-white/80 mt-0.5">Notices and updates from your school.</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($announcements as $announcement)
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="font-heading font-bold text-slate-900">{{ $announcement->title }}</h2>
                    <span class="text-xs text-slate-400 whitespace-nowrap">{{ $announcement->published_at->format('d M Y') }}</span>
                </div>
                <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $announcement->body }}</p>
                <div class="flex items-center gap-2 mt-3 text-xs text-slate-400">
                    @if ($announcement->schoolClass)
                        <span class="px-2 py-0.5 rounded-full bg-slate-100">{{ $announcement->schoolClass->name }}</span>
                    @endif
                    <span>By {{ $announcement->author?->name ?? 'School Admin' }}</span>
                </div>
            </div>
        @empty
            <div class="card p-10 text-center text-slate-500">No announcements yet.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $announcements->links() }}</div>
</div>
