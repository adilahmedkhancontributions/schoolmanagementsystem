<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Subjects</h1>
            <p class="text-sm text-slate-500 mt-1">Manage the subjects taught across your school.</p>
        </div>
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Subject
        </button>
    </div>

    <div class="mb-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search subjects..."
               class="w-full sm:w-72 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Code</th>
                    <th class="py-3 px-4">Class</th>
                    <th class="py-3 px-4">Teacher</th>
                    <th class="py-3 px-4">Elective</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($subjects as $subject)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $subject->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $subject->code ?? '—' }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $subject->schoolClass?->name ?? 'All classes' }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $subject->teacher?->user?->name ?? '—' }}</td>
                        <td class="py-3 px-4">
                            @if ($subject->is_elective)
                                <span class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 px-2 py-0.5 text-xs font-medium">Elective</span>
                            @else
                                <span class="text-slate-400 text-xs">Core</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $subject->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $subject->id }})" wire:confirm="Delete this subject?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-500">No subjects yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $subjects->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$subjectId ? 'Edit Subject' : 'Add Subject'">
        <form wire:submit="save" class="space-y-4">
            <x-floating-input label="Subject name" name="name" wire:model="name" />
            <x-floating-input label="Code (optional)" name="code" wire:model="code" />
            <x-floating-select label="Class (optional)" name="schoolClassId" wire:model="schoolClassId">
                <option value="">— All classes —</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </x-floating-select>
            <x-floating-select label="Teacher (optional)" name="teacherId" wire:model="teacherId">
                <option value="">— Unassigned —</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                @endforeach
            </x-floating-select>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" wire:model="isElective" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Elective subject
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
