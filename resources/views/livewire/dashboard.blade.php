<div>
    @php
        $metricIcons = [
            'Schools' => 'fa-school', 'Total Users' => 'fa-users', 'School Admins' => 'fa-user-shield',
            'Teachers' => 'fa-chalkboard-user', 'Students' => 'fa-user-graduate', 'Classes' => 'fa-layer-group',
            'Active Staff' => 'fa-id-badge', 'My Sections' => 'fa-layer-group', 'My Subjects' => 'fa-book',
            'Class' => 'fa-layer-group', 'Section' => 'fa-diagram-project', 'Admission No.' => 'fa-id-card',
            'My Children' => 'fa-child-reaching', 'Fees Due' => 'fa-file-invoice-dollar',
        ];
    @endphp

    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 sm:p-8 mb-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative">
            <h1 class="font-heading text-2xl sm:text-3xl font-bold">
                Welcome back, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-white/80 mt-2">
                {{ \App\Support\Navigation::roleLabel($role) }} dashboard
                @if (auth()->user()->school)
                    &middot; {{ auth()->user()->school->name }}
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @forelse ($metrics as $label => $value)
            <div class="stat-card">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $value }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg brand-gradient text-white flex items-center justify-center">
                    <i class="fa-solid {{ $metricIcons[$label] ?? 'fa-chart-simple' }}"></i>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">No data available yet.</p>
        @endforelse
    </div>

    @if ($quickLinks->isNotEmpty())
        <div class="card p-6">
            <h2 class="font-heading font-bold text-lg text-slate-900 mb-4">Quick links</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($quickLinks as $link)
                    <a href="{{ route($link['route']) }}" class="flex items-center gap-3 rounded-lg border border-slate-100 p-3 hover:border-indigo-200 hover:bg-slate-50 transition-colors">
                        <div class="h-9 w-9 rounded-lg bg-indigo-50 brand-text flex items-center justify-center">
                            <i class="fa-solid {{ $link['icon'] }}"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
