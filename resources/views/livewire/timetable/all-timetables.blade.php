<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-calendar-days text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Timetables</h1>
                <p class="text-sm text-white/80 mt-0.5">Browse every class schedule and request changes to your periods.</p>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
            {{ session('message') }}
        </div>
    @endif

    <!-- My Periods -->
    <div class="card p-4 sm:p-5 mb-6">
        <h2 class="font-heading font-bold text-slate-900 mb-3">My Periods</h2>

        @if ($myEntries->isEmpty())
            <p class="text-sm text-slate-400">You have no periods assigned yet.</p>
        @else
            <div class="space-y-2">
                @foreach ($myEntries as $entry)
                    <div class="flex items-center justify-between gap-3 py-2 px-3 rounded-lg bg-slate-50">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">
                                {{ $entry->subject->name }}
                                <span class="text-slate-400 font-normal">&middot; {{ $entry->section->schoolClass->name }} - {{ $entry->section->name }}</span>
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ \App\Models\TimetableEntry::DAYS[$entry->day_of_week] ?? '' }},
                                {{ $entry->slot->name }} ({{ $entry->slot->start_time->format('h:i A') }}–{{ $entry->slot->end_time->format('h:i A') }})
                            </p>
                        </div>
                        <button wire:click="openRequestModal({{ $entry->id }})"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-white border border-slate-300 text-slate-600 hover:bg-slate-100">
                            <i class="fa-solid fa-pen"></i> Request Change
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- My Requests -->
    @if ($myRequests->isNotEmpty())
        <div class="card p-4 sm:p-5 mb-6">
            <h2 class="font-heading font-bold text-slate-900 mb-3">My Requests</h2>
            <div class="space-y-2">
                @foreach ($myRequests as $request)
                    <div class="py-2 px-3 rounded-lg bg-slate-50">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium text-slate-800">
                                {{ $request->currentSubject?->name }}
                                <span class="text-slate-400 font-normal">&middot; {{ $request->currentSection?->schoolClass?->name }} - {{ $request->currentSection?->name }}</span>
                            </p>
                            <span @class([
                                'text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap',
                                'bg-amber-100 text-amber-700' => $request->status === 'pending',
                                'bg-emerald-100 text-emerald-700' => $request->status === 'approved',
                                'bg-rose-100 text-rose-700' => $request->status === 'rejected',
                            ])>{{ ucfirst($request->status) }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Requested:
                            @if ($request->requestedSubject) subject &rarr; {{ $request->requestedSubject->name }}, @endif
                            @if ($request->requestedSection) class &rarr; {{ $request->requestedSection->schoolClass->name }} {{ $request->requestedSection->name }}, @endif
                            @if ($request->requestedSlot) period &rarr; {{ $request->requestedSlot->name }}, @endif
                            @if ($request->requested_day_of_week) day &rarr; {{ \App\Models\TimetableEntry::DAYS[$request->requested_day_of_week] }}, @endif
                        </p>
                        <p class="text-xs text-slate-400 mt-1 italic">"{{ $request->reason }}"</p>
                        @if ($request->admin_note)
                            <p class="text-xs text-slate-500 mt-1">Admin note: {{ $request->admin_note }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Browse all timetables -->
    <div class="card p-4 sm:p-5">
        <h2 class="font-heading font-bold text-slate-900 mb-3">Browse Timetables</h2>

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
            <div class="p-8 text-center text-slate-500 text-sm">No time slots have been set up yet.</div>
        @elseif (! $sectionId)
            <div class="p-8 text-center text-slate-500 text-sm">This class has no sections yet.</div>
        @else
            <div class="overflow-x-auto">
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
                                            <p class="text-xs text-slate-500">{{ $entry->teacher?->user?->name }}</p>
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

    <!-- Request Change Modal -->
    @if ($showRequestModal)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/50 p-0 sm:p-4">
            <div class="bg-white w-full sm:max-w-lg sm:rounded-2xl rounded-t-2xl p-5 sm:p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold text-slate-900">Request Timetable Change</h3>
                    <button wire:click="closeRequestModal" class="h-8 w-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-500">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <p class="text-xs text-slate-500 mb-4">Leave a field blank if you don't want to change it. Your request will be sent to the school admin for approval.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">New Subject</label>
                        <select wire:model="requestedSubjectId" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            <option value="">No change</option>
                            @foreach ($allSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">New Class / Section</label>
                        <select wire:model="requestedSectionId" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            <option value="">No change</option>
                            @foreach ($allSections as $section)
                                <option value="{{ $section->id }}">{{ $section->schoolClass->name }} - {{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">New Period / Time</label>
                        <select wire:model="requestedSlotId" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            <option value="">No change</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }} ({{ $period->start_time->format('h:i A') }}–{{ $period->end_time->format('h:i A') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">New Day</label>
                        <select wire:model="requestedDay" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            <option value="">No change</option>
                            @foreach (\App\Models\TimetableEntry::DAYS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Reason</label>
                        <textarea wire:model="reason" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none" placeholder="Explain why you need this change"></textarea>
                        @error('reason') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button wire:click="closeRequestModal" class="flex-1 min-h-touch rounded-lg border border-slate-300 text-slate-600 text-sm font-medium hover:bg-slate-50">Cancel</button>
                    <button wire:click="submitRequest" class="flex-1 min-h-touch rounded-lg brand-gradient text-white text-sm font-medium">Submit Request</button>
                </div>
            </div>
        </div>
    @endif
</div>
