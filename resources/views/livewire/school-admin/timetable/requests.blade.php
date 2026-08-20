<div>
    @include('livewire.school-admin.timetable._tabs', ['active' => 'requests'])

    @if (session('message'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-2 mb-4">
        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $value => $label)
            <button wire:click="$set('statusFilter', '{{ $value }}')"
                class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $statusFilter === $value ? 'brand-gradient text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if ($requests->isEmpty())
        <div class="card p-8 text-center text-slate-500 text-sm">No {{ $statusFilter === 'all' ? '' : $statusFilter }} requests found.</div>
    @else
        <div class="space-y-3">
            @foreach ($requests as $request)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $request->teacher->user->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Current: {{ $request->currentSubject?->name }} &middot;
                                {{ $request->currentSection?->schoolClass?->name }} {{ $request->currentSection?->name }} &middot;
                                {{ $request->currentSlot?->name }} &middot;
                                {{ \App\Models\TimetableEntry::DAYS[$request->current_day_of_week] ?? '' }}
                            </p>
                        </div>
                        <span @class([
                            'text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap',
                            'bg-amber-100 text-amber-700' => $request->status === 'pending',
                            'bg-emerald-100 text-emerald-700' => $request->status === 'approved',
                            'bg-rose-100 text-rose-700' => $request->status === 'rejected',
                        ])>{{ ucfirst($request->status) }}</span>
                    </div>

                    <div class="mt-3 bg-slate-50 rounded-lg p-3 text-xs text-slate-600 space-y-1">
                        <p class="font-medium text-slate-700">Requested changes:</p>
                        @if ($request->requestedSubject)
                            <p>Subject &rarr; {{ $request->requestedSubject->name }}</p>
                        @endif
                        @if ($request->requestedSection)
                            <p>Class / Section &rarr; {{ $request->requestedSection->schoolClass->name }} {{ $request->requestedSection->name }}</p>
                        @endif
                        @if ($request->requestedSlot)
                            <p>Period &rarr; {{ $request->requestedSlot->name }} ({{ $request->requestedSlot->start_time->format('h:i A') }}–{{ $request->requestedSlot->end_time->format('h:i A') }})</p>
                        @endif
                        @if ($request->requested_day_of_week)
                            <p>Day &rarr; {{ \App\Models\TimetableEntry::DAYS[$request->requested_day_of_week] }}</p>
                        @endif
                        <p class="italic text-slate-500 mt-2">"{{ $request->reason }}"</p>
                    </div>

                    @if ($request->admin_note)
                        <p class="text-xs text-slate-500 mt-2">Admin note: {{ $request->admin_note }}</p>
                    @endif

                    @if ($request->status === 'pending')
                        @if ($reviewingId === $request->id)
                            <div class="mt-3 space-y-2">
                                <textarea wire:model="adminNote" rows="2" placeholder="Reason for rejection (optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"></textarea>
                                <div class="flex gap-2">
                                    <button wire:click="reject" class="flex-1 min-h-touch rounded-lg bg-rose-600 text-white text-xs font-medium hover:bg-rose-700">Confirm Reject</button>
                                    <button wire:click="cancelReject" class="flex-1 min-h-touch rounded-lg border border-slate-300 text-slate-600 text-xs font-medium hover:bg-slate-50">Cancel</button>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-2 mt-3">
                                <button wire:click="approve({{ $request->id }})" class="flex-1 sm:flex-none min-h-touch px-4 rounded-lg bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700">
                                    <i class="fa-solid fa-check"></i> Approve
                                </button>
                                <button wire:click="startReject({{ $request->id }})" class="flex-1 sm:flex-none min-h-touch px-4 rounded-lg border border-rose-300 text-rose-600 text-xs font-medium hover:bg-rose-50">
                                    <i class="fa-solid fa-xmark"></i> Reject
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
