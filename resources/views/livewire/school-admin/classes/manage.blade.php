<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Classes & Sections</h1>
            <p class="text-sm text-slate-500 mt-1">Set up the classes and sections used across your school.</p>
        </div>
        <button type="button" wire:click="openCreateClass" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Class
        </button>
    </div>

    <div class="mb-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search classes..."
               class="w-full sm:w-72 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
    </div>

    <div class="space-y-4">
        @forelse ($classes as $class)
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="font-heading font-bold text-slate-900">{{ $class->name }}</h2>
                        <p class="text-xs text-slate-500">{{ $class->students_count }} student(s) &middot; {{ $class->sections->count() }} section(s)</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="openCreateSection({{ $class->id }})" class="btn-secondary text-xs">
                            <i class="fa-solid fa-plus"></i> Section
                        </button>
                        <button type="button" wire:click="openEditClass({{ $class->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="deleteClass({{ $class->id }})" wire:confirm="Delete this class and all its sections?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>

                @if ($class->sections->isEmpty())
                    <p class="text-sm text-slate-400">No sections yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-slate-400 border-b border-slate-100">
                                    <th class="py-2 pr-4">Section</th>
                                    <th class="py-2 pr-4">Class Teacher</th>
                                    <th class="py-2 pr-4">Capacity</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($class->sections as $section)
                                    <tr class="border-b border-slate-50 last:border-0">
                                        <td class="py-2 pr-4 font-medium text-slate-800">{{ $section->name }}</td>
                                        <td class="py-2 pr-4 text-slate-600">{{ $section->classTeacher?->user?->name ?? '—' }}</td>
                                        <td class="py-2 pr-4 text-slate-600">{{ $section->capacity }}</td>
                                        <td class="py-2 pr-4 text-right">
                                            <button type="button" wire:click="openEditSection({{ $section->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" wire:click="deleteSection({{ $section->id }})" wire:confirm="Delete this section?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @empty
            <div class="card p-10 text-center text-slate-500">
                No classes yet. Click "Add Class" to create your first one.
            </div>
        @endforelse
    </div>

    <x-crud-modal :show="$showClassModal" wireClose="closeClassModal" :title="$classId ? 'Edit Class' : 'Add Class'">
        <form wire:submit="saveClass" class="space-y-4">
            <x-floating-input label="Class name (e.g. Grade 5)" name="className" wire:model="className" />
            <x-floating-input label="Sort order" name="sortOrder" type="number" wire:model="sortOrder" />
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeClassModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal :show="$showSectionModal" wireClose="closeSectionModal" :title="$sectionId ? 'Edit Section' : 'Add Section'">
        <form wire:submit="saveSection" class="space-y-4">
            <x-floating-input label="Section name (e.g. A)" name="sectionName" wire:model="sectionName" />
            <x-floating-input label="Capacity" name="sectionCapacity" type="number" wire:model="sectionCapacity" />
            <x-floating-select label="Class teacher" name="sectionTeacherId" wire:model="sectionTeacherId">
                <option value="">— None —</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                @endforeach
            </x-floating-select>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeSectionModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
