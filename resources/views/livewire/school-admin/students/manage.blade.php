<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Students</h1>
            <p class="text-sm text-slate-500 mt-1">Manage student enrollment and class placement.</p>
        </div>
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Student
        </button>
    </div>

    @if ($generatedPassword)
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 flex items-start justify-between gap-4">
            <div class="text-sm text-emerald-800">
                Student account created. Temporary password:
                <code class="font-mono font-semibold bg-white/70 px-1.5 py-0.5 rounded">{{ $generatedPassword }}</code>
                — share it securely, they should change it after first login.
            </div>
            <button type="button" wire:click="dismissGeneratedPassword" class="text-emerald-700 min-h-touch min-w-touch">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search by name, email or admission no..."
               class="w-full sm:w-72 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">

        <select wire:model.live="filterClassId" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">All classes</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>

        @if ($filterClassId)
            <select wire:model.live="filterSectionId" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                <option value="">All sections</option>
                @foreach ($filterSections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($students as $student)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-10 w-10 shrink-0 rounded-full brand-gradient text-white flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr($student->user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-slate-800 truncate">{{ $student->user->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $student->user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openEdit({{ $student->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $student->id }})" wire:confirm="Remove this student and their account?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <dt class="text-slate-400">Admission No.</dt>
                        <dd class="text-slate-700 font-medium mt-0.5">{{ $student->admission_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Class / Section</dt>
                        <dd class="text-slate-700 font-medium mt-0.5">{{ $student->schoolClass?->name ?? '—' }}{{ $student->section ? ' / '.$student->section->name : '' }}</dd>
                    </div>
                </dl>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No students yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Admission No.</th>
                    <th class="py-3 px-4">Class / Section</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $student->user->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $student->admission_number }}</td>
                        <td class="py-3 px-4 text-slate-600">
                            {{ $student->schoolClass?->name ?? '—' }}{{ $student->section ? ' / '.$student->section->name : '' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $student->user->email }}</td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $student->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $student->id }})" wire:confirm="Remove this student and their account?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-500">No students yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $students->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$studentId ? 'Edit Student' : 'Add Student'" maxWidth="xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="Full name" name="name" wire:model="name" />
                <x-floating-input label="Email" name="email" type="email" wire:model="email" />
                <x-floating-input label="Phone" name="phone" wire:model="phone" />
                <x-floating-input label="Admission number" name="admissionNumber" wire:model="admissionNumber" />
                <x-floating-input label="Date of birth" name="dateOfBirth" type="date" wire:model="dateOfBirth" />
                <x-floating-select label="Gender" name="gender" wire:model="gender">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </x-floating-select>
                <x-floating-select label="Class" name="schoolClassId" wire:model.live="schoolClassId">
                    <option value="">— None —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </x-floating-select>
                <x-floating-select label="Section" name="sectionId" wire:model="sectionId">
                    <option value="">— None —</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </x-floating-select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
