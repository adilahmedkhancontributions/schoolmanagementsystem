<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-calendar-days text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Timetable</h1>
                <p class="text-sm text-white/80 mt-0.5">Your weekly class schedule.</p>
            </div>
        </div>
    </div>

    @if ($periods->isEmpty())
        <div class="card p-8 text-center text-slate-500 text-sm">No timetable has been set up yet.</div>
    @else
        <!-- Mobile: one card per day -->
        <div class="sm:hidden space-y-4">
            @foreach ($days as $day => $label)
                @php($dayEntries = $periods->filter(fn ($period) => isset($grid[$period->id][$day])))
                <div class="card p-4">
                    <h2 class="font-heading font-bold text-slate-900 mb-3">{{ $label }}</h2>
                    @forelse ($dayEntries as $period)
                        @php($entry = $grid[$period->id][$day])
                        <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $entry->subject->name }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $isTeacher ? ($entry->section->schoolClass->name.' - '.$entry->section->name) : $entry->teacher?->user?->name }}
                                </p>
                            </div>
                            <span class="text-xs text-slate-400 whitespace-nowrap ml-3">{{ $period->start_time->format('h:i A') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No periods scheduled.</p>
                    @endforelse
                </div>
            @endforeach
        </div>

        <!-- Desktop: grid -->
        <div class="hidden sm:block card overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="py-3 px-4 sticky left-0 bg-slate-50">Time</th>
                        @foreach ($days as $day => $label)
                            <th class="py-3 px-4 whitespace-nowrap">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($periods as $period)
                        <tr>
                            <td class="py-2 px-4 whitespace-nowrap font-medium text-slate-700 sticky left-0 bg-white">
                                {{ $period->name }}
                                <p class="text-xs text-slate-400 font-normal">{{ $period->start_time->format('h:i A') }}</p>
                            </td>
                            @foreach ($days as $day => $label)
                                @php($entry = $grid[$period->id][$day] ?? null)
                                <td class="py-2 px-4 min-w-[140px]">
                                    @if ($entry)
                                        <p class="text-sm font-medium text-slate-800">{{ $entry->subject->name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $isTeacher ? ($entry->section->schoolClass->name.' - '.$entry->section->name) : $entry->teacher?->user?->name }}
                                        </p>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
