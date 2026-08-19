<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Fee Structures</h1>
            <p class="text-sm text-slate-500 mt-1">Define recurring or one-time fee items to bill to students.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('school-admin.fees.invoices') }}" class="btn-secondary">
                <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
            </a>
            <button type="button" wire:click="openCreate" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Add Fee Structure
            </button>
        </div>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Class</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Frequency</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($structures as $structure)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $structure->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $structure->schoolClass?->name ?? 'All classes' }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ auth()->user()->school->currency }} {{ number_format($structure->amount, 2) }}</td>
                        <td class="py-3 px-4 text-slate-600 capitalize">{{ str_replace('_', ' ', $structure->frequency) }}</td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $structure->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $structure->id }})" wire:confirm="Delete this fee structure?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-500">No fee structures yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$structureId ? 'Edit Fee Structure' : 'Add Fee Structure'">
        <form wire:submit="save" class="space-y-4">
            <x-floating-input label="Name (e.g. Tuition Fee)" name="name" wire:model="name" />
            <x-floating-input label="Amount" name="amount" type="number" step="0.01" wire:model="amount" />
            <x-floating-select label="Applies to class" name="schoolClassId" wire:model="schoolClassId">
                <option value="">All classes</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </x-floating-select>
            <x-floating-select label="Frequency" name="frequency" wire:model="frequency">
                <option value="one_time">One time</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="term">Per term</option>
                <option value="annual">Annual</option>
            </x-floating-select>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
