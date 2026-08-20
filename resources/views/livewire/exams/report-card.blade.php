<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-graduation-cap text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Grades</h1>
                <p class="text-sm text-white/80 mt-0.5">Exam results and report card.</p>
            </div>
        </div>
    </div>

    @if ($children->isNotEmpty())
        <div class="mb-4">
            <select wire:model.live="studentId" class="w-full sm:w-64 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                @foreach ($children as $child)
                    <option value="{{ $child->id }}">{{ $child->user->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if ($studentId)
        @if ($exams->isNotEmpty())
            <div class="mb-4">
                <select wire:model.live="examId" class="w-full sm:w-64 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                    @foreach ($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="stat-card">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Marks Obtained</p>
                        <p class="text-xl font-bold text-slate-900 mt-1">{{ number_format($totals['obtained'], 2) }} / {{ number_format($totals['max'], 2) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-list-ol"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Percentage</p>
                        <p class="text-xl font-bold text-slate-900 mt-1">{{ $totals['percentage'] !== null ? $totals['percentage'].'%' : '—' }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Overall Grade</p>
                        <p class="text-xl font-bold text-emerald-600 mt-1">{{ $totals['grade'] ?? '—' }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-award"></i>
                    </div>
                </div>
            </div>

            @php
                $resultBadge = function ($result) {
                    if ($result->isPass() === null) {
                        return ['label' => 'Pending', 'class' => 'bg-slate-100 text-slate-500'];
                    }
                    return $result->isPass()
                        ? ['label' => 'Pass', 'class' => 'bg-emerald-50 text-emerald-700']
                        : ['label' => 'Fail', 'class' => 'bg-rose-50 text-rose-700'];
                };
            @endphp

            <!-- Mobile card list -->
            <div class="sm:hidden space-y-3">
                @forelse ($results as $result)
                    @php($badge = $resultBadge($result))
                    <div class="card p-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-medium text-slate-800">{{ $result->examSubject->subject->name }}</p>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </div>
                        <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <dt class="text-slate-400">Marks</dt>
                                <dd class="text-slate-700 font-medium mt-0.5">
                                    {{ $result->marks_obtained !== null ? number_format($result->marks_obtained, 2) : '—' }} / {{ number_format($result->examSubject->max_marks, 2) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-400">Grade</dt>
                                <dd class="text-slate-700 font-medium mt-0.5">{{ $result->grade() ?? '—' }}</dd>
                            </div>
                            @if ($result->remarks)
                                <div class="col-span-2">
                                    <dt class="text-slate-400">Remarks</dt>
                                    <dd class="text-slate-700 mt-0.5">{{ $result->remarks }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @empty
                    <div class="card p-8 text-center text-slate-500 text-sm">No subjects set up for this exam yet.</div>
                @endforelse
            </div>

            <!-- Desktop table -->
            <div class="hidden sm:block card overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Marks</th>
                            <th class="py-3 px-4">Grade</th>
                            <th class="py-3 px-4">Result</th>
                            <th class="py-3 px-4">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($results as $result)
                            <tr>
                                <td class="py-3 px-4 font-medium text-slate-800">{{ $result->examSubject->subject->name }}</td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $result->marks_obtained !== null ? number_format($result->marks_obtained, 2) : '—' }} / {{ number_format($result->examSubject->max_marks, 2) }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $result->grade() ?? '—' }}</td>
                                <td class="py-3 px-4">
                                    @if ($result->isPass() === null)
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-500">Pending</span>
                                    @elseif ($result->isPass())
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">Pass</span>
                                    @else
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-rose-50 text-rose-700">Fail</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-500">{{ $result->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-500">No subjects set up for this exam yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-slate-500">No exams have been scheduled for this class yet.</p>
        @endif
    @else
        <p class="text-sm text-slate-500">No student profile linked to this account yet.</p>
    @endif
</div>
