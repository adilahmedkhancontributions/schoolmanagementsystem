<div>
    @include('livewire.school-admin.timetable._tabs', ['active' => 'manage'])

    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <select wire:model.live="schoolClassId" class="w-full sm:w-56 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="sectionId" class="w-full sm:w-56 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            @foreach ($sections as $section)
                <option value="{{ $section->id }}">{{ $section->name }}</option>
            @endforeach
        </select>
    </div>

    @if ($periods->isEmpty())
        <div class="card p-8 text-center text-slate-500 text-sm">
            No time slots set up yet. Go to the <a href="{{ route('school-admin.timetable.slots') }}" class="brand-text hover:underline font-medium">Time Slots</a> tab to add periods first.
        </div>
    @elseif (! $sectionId)
        <div class="card p-8 text-center text-slate-500 text-sm">This class has no sections yet.</div>
    @else
        <div class="card overflow-x-auto">
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
                                <p class="text-xs text-slate-400 font-normal">{{ $period->start_time->format('h:i A') }}–{{ $period->end_time->format('h:i A') }}</p>
                            </td>
                            @foreach ($days as $day => $label)
                                @php($entry = $grid[$period->id][$day] ?? null)
                                <td class="py-2 px-4">
                                    <select
                                        wire:change="assign({{ $period->id }}, {{ $day }}, $event.target.value)"
                                        class="w-40 min-h-touch rounded-lg border border-slate-300 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                                        <option value="" @selected(! $entry)>—</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}" @selected($entry?->subject_id === $subject->id)>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
