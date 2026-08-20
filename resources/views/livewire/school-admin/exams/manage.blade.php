<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Exams</h1>
            <p class="text-sm text-slate-500 mt-1">Set up exams per class, then choose subjects and pass marks.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('school-admin.exams.grades') }}" class="btn-secondary">
                <i class="fa-solid fa-pen-to-square"></i> Enter Grades
            </a>
            <button type="button" wire:click="openCreate" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Add Exam
            </button>
        </div>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($exams as $exam)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $exam->name }}</p>
                        <p class="text-xs text-slate-500">{{ $exam->schoolClass->name }}{{ $exam->term ? ' · '.$exam->term : '' }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openSubjects({{ $exam->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Subjects & marks">
                            <i class="fa-solid fa-list-check"></i>
                        </button>
                        <button type="button" wire:click="openEdit({{ $exam->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $exam->id }})" wire:confirm="Delete this exam and all its results?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-500">
                    <i class="fa-solid fa-calendar-days mr-1"></i>
                    @if ($exam->start_date)
                        {{ $exam->start_date->format('d M') }} – {{ $exam->end_date?->format('d M Y') }}
                    @else
                        No dates set
                    @endif
                </p>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No exams yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Class</th>
                    <th class="py-3 px-4">Term</th>
                    <th class="py-3 px-4">Dates</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($exams as $exam)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $exam->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $exam->schoolClass->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $exam->term ?: '—' }}</td>
                        <td class="py-3 px-4 text-slate-600">
                            @if ($exam->start_date)
                                {{ $exam->start_date->format('d M') }} – {{ $exam->end_date?->format('d M Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openSubjects({{ $exam->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Subjects & marks">
                                <i class="fa-solid fa-list-check"></i>
                            </button>
                            <button type="button" wire:click="openEdit({{ $exam->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $exam->id }})" wire:confirm="Delete this exam and all its results?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-500">No exams yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$examId ? 'Edit Exam' : 'Add Exam'">
        <form wire:submit="save" class="space-y-4">
            <x-floating-input label="Exam name (e.g. Mid Term)" name="name" wire:model="name" />
            <x-floating-select label="Class" name="schoolClassId" wire:model="schoolClassId">
                <option value="">Select a class</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </x-floating-select>
            <x-floating-input label="Term (optional)" name="term" wire:model="term" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-floating-input label="Start date" name="startDate" type="date" wire:model="startDate" />
                <x-floating-input label="End date" name="endDate" type="date" wire:model="endDate" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal :show="$showSubjectsModal" wireClose="closeSubjectsModal" :title="'Subjects — '.($activeExam->name ?? '')" maxWidth="xl">
        @if (empty($subjectRows))
            <p class="text-sm text-slate-500">No subjects exist for this class yet. Add subjects first.</p>
        @else
            <form wire:submit="saveSubjects" class="space-y-4">
                <div class="divide-y divide-slate-100 border border-slate-100 rounded-lg overflow-hidden">
                    @foreach ($subjectRows as $subjectId => $row)
                        <div class="p-3 flex flex-col sm:flex-row sm:items-center gap-3">
                            <label class="flex items-center gap-2 flex-1 min-w-0">
                                <input type="checkbox" wire:model="subjectRows.{{ $subjectId }}.included" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="font-medium text-slate-800 truncate">{{ $row['name'] }}</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2 w-full sm:w-64">
                                <input type="number" step="0.01" wire:model="subjectRows.{{ $subjectId }}.max_marks" placeholder="Max marks" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                                <input type="number" step="0.01" wire:model="subjectRows.{{ $subjectId }}.pass_marks" placeholder="Pass marks" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeSubjectsModal" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        @endif
    </x-crud-modal>
</div>
