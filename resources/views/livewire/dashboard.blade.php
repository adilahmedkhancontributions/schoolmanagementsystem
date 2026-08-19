<div>
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-slate-900">
            Welcome back, {{ auth()->user()->name }}
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            {{ \App\Support\Navigation::roleLabel($role) }} dashboard
            @if (auth()->user()->school)
                &middot; {{ auth()->user()->school->name }}
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @forelse ($metrics as $label => $value)
            <div class="stat-card">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $value }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">No data available yet.</p>
        @endforelse
    </div>

    <div class="card p-6">
        <h2 class="font-heading font-bold text-lg text-slate-900 mb-2">More is on the way</h2>
        <p class="text-sm text-slate-500">
            Attendance, fees, examinations, CMS and communication modules are being
            built next. See <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">PROGRESS.md</code>
            for the full roadmap and current status.
        </p>
    </div>
</div>
