<div>
    @php
        $statusMeta = [
            'present' => ['label' => 'Present', 'icon' => 'fa-circle-check', 'color' => '#10b981'],
            'absent' => ['label' => 'Absent', 'icon' => 'fa-circle-xmark', 'color' => '#f43f5e'],
            'late' => ['label' => 'Late', 'icon' => 'fa-clock', 'color' => '#f59e0b'],
            'half_day' => ['label' => 'Half day', 'icon' => 'fa-hourglass-half', 'color' => '#0ea5e9'],
            'leave' => ['label' => 'Leave', 'icon' => 'fa-plane', 'color' => '#64748b'],
        ];
        $total = array_sum($summary);
        $rate = $total ? round((($summary['present'] ?? 0) / $total) * 100) : 0;
    @endphp

    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-id-badge text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">My Attendance</h1>
                <p class="text-sm text-white/80 mt-0.5">Your attendance history and monthly summary.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="month" wire:model.live="month" class="w-full sm:w-48 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card p-6 flex items-center gap-5 sm:col-span-1">
            <div class="relative h-24 w-24 shrink-0 rounded-full" style="background: conic-gradient(var(--brand-primary) {{ $rate * 3.6 }}deg, #e2e8f0 0deg)">
                <div class="absolute inset-2 rounded-full bg-white flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-slate-900">{{ $rate }}%</span>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wide">Present</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-700">This month</p>
                <p class="text-xs text-slate-500 mt-1">{{ $total }} day{{ $total === 1 ? '' : 's' }} recorded</p>
            </div>
        </div>

        <div class="sm:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach ($statusMeta as $key => $meta)
                <div class="rounded-xl border border-slate-100 bg-white p-4 text-center shadow-card">
                    <div class="mx-auto mb-1.5 h-8 w-8 rounded-full flex items-center justify-center text-white" style="background-color: {{ $meta['color'] }}">
                        <i class="fa-solid {{ $meta['icon'] }} text-xs"></i>
                    </div>
                    <p class="text-lg font-bold text-slate-900">{{ $summary[$key] }}</p>
                    <p class="text-[11px] font-medium text-slate-500">{{ $meta['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Date</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($records as $record)
                    <tr>
                        <td class="py-3 px-4 text-slate-700">{{ $record->date->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full text-white"
                                  style="background-color: {{ $statusMeta[$record->status]['color'] ?? '#64748b' }}">
                                <i class="fa-solid {{ $statusMeta[$record->status]['icon'] ?? 'fa-circle' }}"></i>
                                {{ $statusMeta[$record->status]['label'] ?? str_replace('_', ' ', $record->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500">{{ $record->remarks ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-10 text-center text-slate-500">No attendance records for this month.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $records->links() }}</div>
</div>
