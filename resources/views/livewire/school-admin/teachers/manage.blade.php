<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Teachers</h1>
            <p class="text-sm text-slate-500 mt-1">Manage teaching staff for your school.</p>
        </div>
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Teacher
        </button>
    </div>

    @if ($generatedPassword)
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 flex items-start justify-between gap-4">
            <div class="text-sm text-emerald-800">
                Teacher account created. Temporary password:
                <code class="font-mono font-semibold bg-white/70 px-1.5 py-0.5 rounded">{{ $generatedPassword }}</code>
                — share it securely, they should change it after first login.
            </div>
            <button type="button" wire:click="dismissGeneratedPassword" class="text-emerald-700 min-h-touch min-w-touch">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="mb-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search by name or email..."
               class="w-full sm:w-72 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Employee ID</th>
                    <th class="py-3 px-4">Type</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($teachers as $teacher)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $teacher->user->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $teacher->user->email }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $teacher->employee_id }}</td>
                        <td class="py-3 px-4 text-slate-600 capitalize">{{ str_replace('_', ' ', $teacher->employment_type) }}</td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $teacher->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $teacher->id }})" wire:confirm="Remove this teacher and their account?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-500">No teachers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $teachers->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$teacherId ? 'Edit Teacher' : 'Add Teacher'" maxWidth="xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="Full name" name="name" wire:model="name" />
                <x-floating-input label="Email" name="email" type="email" wire:model="email" />
                <x-floating-input label="Phone" name="phone" wire:model="phone" />
                <x-floating-input label="Employee ID" name="employeeId" wire:model="employeeId" />
                <x-floating-input label="Qualification" name="qualification" wire:model="qualification" />
                <x-floating-input label="Specialization" name="specialization" wire:model="specialization" />
                <x-floating-select label="Employment type" name="employmentType" wire:model="employmentType">
                    <option value="full_time">Full time</option>
                    <option value="part_time">Part time</option>
                    <option value="contract">Contract</option>
                </x-floating-select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
