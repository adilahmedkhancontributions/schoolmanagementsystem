<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-minus text-lg"></i>
                </div>
                <div>
                    <h1 class="font-heading text-xl sm:text-2xl font-bold">Leave Requests</h1>
                    <p class="text-sm text-white/80 mt-0.5">
                        {{ auth()->user()->hasRole('parent') ? "Request leave for your child." : 'Request your own leave.' }}
                    </p>
                </div>
            </div>
            <button type="button" wire:click="openForm" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                <i class="fa-solid fa-plus"></i> New Request
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
            {{ session('message') }}
        </div>
    @endif

    @if ($children->isNotEmpty())
        <div class="mb-4">
            <select wire:model.live="studentId" class="w-full sm:w-64 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                @foreach ($children as $child)
                    <option value="{{ $child->id }}">{{ $child->user->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if ($requests->isEmpty())
        <div class="card p-8 text-center text-slate-500 text-sm">No leave requests yet.</div>
    @else
        <div class="space-y-3">
            @foreach ($requests as $request)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $request->from_date->format('d M Y') }} &ndash; {{ $request->to_date->format('d M Y') }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $request->days() }} day{{ $request->days() > 1 ? 's' : '' }}</p>
                        </div>
                        <span @class([
                            'text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap',
                            'bg-amber-100 text-amber-700' => $request->status === 'pending',
                            'bg-emerald-100 text-emerald-700' => $request->status === 'approved',
                            'bg-rose-100 text-rose-700' => $request->status === 'rejected',
                        ])>{{ ucfirst($request->status) }}</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-600 italic">"{{ $request->reason }}"</p>
                    @if ($request->admin_note)
                        <p class="mt-2 text-xs text-slate-500">Admin note: {{ $request->admin_note }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <x-crud-modal :show="$showForm" wireClose="closeForm" title="New Leave Request">
        <form wire:submit="submit" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
                    <input type="date" wire:model="fromDate" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                    @error('fromDate') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
                    <input type="date" wire:model="toDate" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                    @error('toDate') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Reason</label>
                <textarea wire:model="reason" rows="3" placeholder="Reason for leave" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"></textarea>
                @error('reason') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeForm" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Submit Request</button>
            </div>
        </form>
    </x-crud-modal>
</div>
