<div>
    @include('livewire.school-admin.timetable._tabs', ['active' => 'slots'])

    <div class="flex justify-end mb-4">
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Time Slot
        </button>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($periods as $period)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $period->name }}</p>
                        <p class="text-xs text-slate-500">{{ $period->start_time->format('h:i A') }} – {{ $period->end_time->format('h:i A') }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openEdit({{ $period->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $period->id }})" wire:confirm="Delete this time slot and any timetable entries using it?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No time slots yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Start</th>
                    <th class="py-3 px-4">End</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($periods as $period)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $period->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $period->start_time->format('h:i A') }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $period->end_time->format('h:i A') }}</td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $period->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $period->id }})" wire:confirm="Delete this time slot and any timetable entries using it?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-500">No time slots yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$slotId ? 'Edit Time Slot' : 'Add Time Slot'">
        <form wire:submit="save" class="space-y-4">
            <x-floating-input label="Name (e.g. Period 1)" name="name" wire:model="name" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="Start time" name="startTime" type="time" wire:model="startTime" />
                <x-floating-input label="End time" name="endTime" type="time" wire:model="endTime" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
