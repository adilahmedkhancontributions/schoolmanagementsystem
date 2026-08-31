<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-lg"></i>
                </div>
                <div>
                    <h1 class="font-heading text-xl sm:text-2xl font-bold">Homework</h1>
                    <p class="text-sm text-white/80 mt-0.5">Assign homework per class and subject, then review submissions and grade them.</p>
                </div>
            </div>
            <button type="button" wire:click="openCreate" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                <i class="fa-solid fa-plus"></i> Assign Homework
            </button>
        </div>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($homeworks as $homework)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $homework->title }}</p>
                        <p class="text-xs text-slate-500">{{ $homework->schoolClass->name }} · {{ $homework->subject->name }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openSubmissions({{ $homework->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Submissions & grading">
                            <i class="fa-solid fa-list-check"></i>
                        </button>
                        <button type="button" wire:click="openEdit({{ $homework->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $homework->id }})" wire:confirm="Delete this homework and all its submissions?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-500">
                    <i class="fa-solid fa-calendar-days mr-1"></i> Due {{ $homework->due_date->format('d M Y') }}
                    &middot; <span class="font-medium text-indigo-600">{{ $homework->submissions_count }}</span> submission{{ $homework->submissions_count === 1 ? '' : 's' }}
                </p>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No homework assigned yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Class</th>
                    <th class="py-3 px-4">Subject</th>
                    <th class="py-3 px-4">Submissions</th>
                    <th class="py-3 px-4">Due Date</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($homeworks as $homework)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $homework->title }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $homework->schoolClass->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $homework->subject->name }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full {{ $homework->submissions_count > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500' }} px-2.5 py-1 text-xs font-medium">
                                <i class="fa-solid fa-file-circle-check"></i>
                                {{ $homework->submissions_count }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $homework->due_date->format('d M Y') }}</td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openSubmissions({{ $homework->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Submissions & grading">
                                <i class="fa-solid fa-list-check"></i>
                            </button>
                            <button type="button" wire:click="openEdit({{ $homework->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $homework->id }})" wire:confirm="Delete this homework and all its submissions?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-500">No homework assigned yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$homeworkId ? 'Edit Homework' : 'Assign Homework'">
        <form wire:submit="save" class="space-y-4">
            <x-floating-input label="Title" name="title" wire:model="title" />

            <div>
                <textarea wire:model="description" rows="3" placeholder="Description / instructions (optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"></textarea>
                @error('description') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <x-floating-select label="Class" name="schoolClassId" wire:model.live="schoolClassId">
                <option value="">Select a class</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </x-floating-select>

            <x-floating-select label="Subject" name="subjectId" wire:model="subjectId">
                <option value="">Select a subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </x-floating-select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-floating-input label="Due date" name="dueDate" type="date" wire:model="dueDate" />
                <x-floating-input label="Max marks (optional)" name="maxMarks" type="number" wire:model="maxMarks" />
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Attachment (optional)</label>
                @if ($existingAttachmentPath && ! $attachment)
                    <div class="flex items-center justify-between gap-2 rounded-lg border border-slate-200 px-3 py-2 mb-2">
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingAttachmentPath) }}" target="_blank" class="text-sm text-indigo-600 hover:underline truncate">
                            <i class="fa-solid fa-paperclip mr-1"></i> View current attachment
                        </a>
                        <button type="button" wire:click="removeAttachment" class="text-xs text-rose-600 hover:underline shrink-0">Remove</button>
                    </div>
                @endif
                <input type="file" wire:model="attachment" class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                <div wire:loading wire:target="attachment" class="text-xs text-slate-400 mt-1">Uploading…</div>
                @error('attachment') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal :show="$showSubmissionsModal" wireClose="closeSubmissionsModal" :title="'Submissions — '.($activeHomework->title ?? '')" maxWidth="2xl">
        @if ($activeHomework)
            <form wire:submit="saveGrades" class="space-y-4">
                <div class="divide-y divide-slate-100 border border-slate-100 rounded-lg overflow-hidden max-h-[60vh] overflow-y-auto">
                    @forelse ($activeHomework->schoolClass->students as $student)
                        @php($submission = $submissions->get($student->id))
                        <div class="p-3 space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-medium text-slate-800 truncate">{{ $student->user->name }}</span>
                                @if ($submission && $submission->status() === 'graded')
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 shrink-0">Graded</span>
                                @elseif ($submission && $submission->status() === 'submitted')
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-sky-50 text-sky-700 shrink-0">Submitted</span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-500 shrink-0">Not submitted</span>
                                @endif
                            </div>

                            @if ($submission?->submission_text)
                                <p class="text-xs text-slate-500">{{ $submission->submission_text }}</p>
                            @endif
                            @if ($submission?->fileUrl())
                                <a href="{{ $submission->fileUrl() }}" target="_blank" class="text-xs text-indigo-600 hover:underline inline-flex items-center gap-1">
                                    <i class="fa-solid fa-paperclip"></i> View submitted file
                                </a>
                            @endif

                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" step="0.01" wire:model="marksObtained.{{ $student->id }}" placeholder="Marks" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                                <input type="text" wire:model="feedback.{{ $student->id }}" placeholder="Feedback" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            </div>
                            @error("marksObtained.{$student->id}") <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    @empty
                        <p class="p-4 text-sm text-slate-500">No students in this class yet.</p>
                    @endforelse
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeSubmissionsModal" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Grades</button>
                </div>
            </form>
        @endif
    </x-crud-modal>
</div>
