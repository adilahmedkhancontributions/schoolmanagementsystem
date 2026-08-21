<div>
    @php
        $statusMeta = [
            'present' => ['label' => 'Present', 'icon' => 'fa-circle-check', 'color' => '#10b981'],
            'absent' => ['label' => 'Absent', 'icon' => 'fa-circle-xmark', 'color' => '#f43f5e'],
            'late' => ['label' => 'Late', 'icon' => 'fa-clock', 'color' => '#f59e0b'],
            'half_day' => ['label' => 'Half day', 'icon' => 'fa-hourglass-half', 'color' => '#0ea5e9'],
            'leave' => ['label' => 'Leave', 'icon' => 'fa-plane', 'color' => '#64748b'],
        ];
        $tally = collect($status)->countBy();
    @endphp

    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-id-badge text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Staff Attendance</h1>
                <p class="text-sm text-white/80 mt-0.5">Mark daily attendance for teachers and staff.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="date" wire:model.live="date" class="w-full sm:w-48 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
    </div>

    @if ($saved)
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            Attendance saved for {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}.
        </div>
    @endif

    @if ($members->isNotEmpty())
        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-5">
            @foreach ($statusMeta as $key => $meta)
                <div class="rounded-xl border border-slate-100 bg-white p-3 text-center shadow-card">
                    <p class="text-lg font-bold" style="color: {{ $meta['color'] }}">{{ $tally[$key] ?? 0 }}</p>
                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">{{ $meta['label'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Mark all:</span>
            @foreach (['present', 'absent', 'late'] as $key)
                <button type="button" wire:click="markAll('{{ $key }}')"
                    class="status-chip"
                    style="border-color: {{ $statusMeta[$key]['color'] }}33; background-color: {{ $statusMeta[$key]['color'] }}14; color: {{ $statusMeta[$key]['color'] }}">
                    <i class="fa-solid {{ $statusMeta[$key]['icon'] }}"></i> {{ $statusMeta[$key]['label'] }}
                </button>
            @endforeach
        </div>

        <form wire:submit="save" class="space-y-3 pb-32 lg:pb-0">
            <div class="card divide-y divide-slate-100 overflow-hidden">
                @foreach ($members as $member)
                    @php($current = $status[$member['user_id']] ?? 'present')
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="h-9 w-9 shrink-0 rounded-full brand-gradient text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($member['name'], 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-slate-800 truncate">{{ $member['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $member['role'] }} &middot; {{ $member['designation'] }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($statusMeta as $value => $meta)
                                <button
                                    type="button"
                                    wire:click="setStatus({{ $member['user_id'] }}, '{{ $value }}')"
                                    data-active="{{ $current === $value ? 'true' : 'false' }}"
                                    class="status-chip {{ $current === $value ? 'border-transparent' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}"
                                    style="{{ $current === $value ? 'background-color: '.$meta['color'] : '' }}"
                                >
                                    <i class="fa-solid {{ $meta['icon'] }}"></i>
                                    <span class="hidden sm:inline">{{ $meta['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="hidden lg:flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Save Attendance
                </button>
            </div>

            <div class="lg:hidden fixed bottom-16 inset-x-0 z-20 bg-white border-t border-slate-200 p-3">
                <button type="submit" class="btn-primary w-full justify-center">
                    <i class="fa-solid fa-floppy-disk"></i> Save Attendance
                </button>
            </div>
        </form>
    @else
        <p class="text-sm text-slate-500">No teachers or staff members yet.</p>
    @endif
</div>
