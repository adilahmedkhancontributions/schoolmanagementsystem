<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Grade Entry</h1>
                <p class="text-sm text-white/80 mt-0.5">Enter marks for an exam subject.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <select wire:model.live="examId" class="w-full sm:w-64 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">Select an exam</option>
            @foreach ($exams as $exam)
                <option value="{{ $exam->id }}">{{ $exam->name }} — {{ $exam->schoolClass->name }}</option>
            @endforeach
        </select>

        @if ($examId)
            <select wire:model.live="examSubjectId" class="w-full sm:w-64 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                <option value="">Select a subject</option>
                @foreach ($examSubjects as $examSubject)
                    <option value="{{ $examSubject->id }}">{{ $examSubject->subject->name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    @if ($activeExamSubject)
        @if ($saved)
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                Grades saved for {{ $activeExamSubject->subject->name }}.
            </div>
        @endif

        @if ($students->isNotEmpty())
            <form wire:submit="save" class="space-y-3 pb-24 lg:pb-0">
                <div class="card divide-y divide-slate-100 overflow-hidden">
                    @foreach ($students as $student)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="h-9 w-9 shrink-0 rounded-full brand-gradient text-white flex items-center justify-center text-xs font-semibold">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-800 truncate">{{ $student->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $student->admission_number }}</p>
                                </div>
                            </div>

                            <div class="flex gap-2 w-full sm:w-80">
                                <input type="number" step="0.01" min="0" max="{{ $activeExamSubject->max_marks }}"
                                    wire:model="marks.{{ $student->id }}"
                                    placeholder="/ {{ (float) $activeExamSubject->max_marks }}"
                                    class="w-28 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                                <input type="text" wire:model="remarks.{{ $student->id }}" placeholder="Remarks (optional)"
                                    class="flex-1 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            </div>
                            @error('marks.'.$student->id)
                                <p class="text-xs text-rose-600 sm:ml-auto">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="hidden lg:flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Save Grades
                    </button>
                </div>

                <div class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-200 p-3">
                    <button type="submit" class="btn-primary w-full justify-center">
                        <i class="fa-solid fa-floppy-disk"></i> Save Grades
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-slate-500">No students in this class yet.</p>
        @endif
    @else
        <p class="text-sm text-slate-500">Select an exam and subject to start entering grades.</p>
    @endif
</div>
