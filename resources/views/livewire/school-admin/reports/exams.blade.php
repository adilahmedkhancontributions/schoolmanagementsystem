<div>
    @include('livewire.school-admin.reports._tabs', ['active' => 'exams'])

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
        <select wire:model.live="examId" class="w-full sm:w-64 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            @foreach ($exams as $exam)
                <option value="{{ $exam->id }}">{{ $exam->name }} — {{ $exam->schoolClass->name }}</option>
            @endforeach
        </select>
        @if ($examId)
            <button type="button" wire:click="export" class="btn-secondary sm:ml-auto">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </button>
        @endif
    </div>

    @if ($examId)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="stat-card">
                <div>
                    <p class="text-xs font-medium text-slate-500">Class Average</p>
                    <p class="text-xl font-bold text-slate-900 mt-1">{{ $summary['classAverage'] !== null ? $summary['classAverage'].'%' : '—' }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <p class="text-xs font-medium text-slate-500">Pass Rate</p>
                    <p class="text-xl font-bold text-emerald-600 mt-1">{{ $summary['passRate'] !== null ? $summary['passRate'].'%' : '—' }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <p class="text-xs font-medium text-slate-500">Top Scorer</p>
                    <p class="text-lg font-bold text-slate-900 mt-1 truncate">{{ $summary['topScorer']['student']->user->name ?? '—' }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-trophy"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card overflow-hidden overflow-x-auto">
                <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 text-sm">Student Rankings</div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Marks</th>
                            <th class="py-3 px-4">%</th>
                            <th class="py-3 px-4">Result</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($studentRows as $row)
                            <tr>
                                <td class="py-3 px-4 font-medium text-slate-800 whitespace-nowrap">{{ $row['student']->user->name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ number_format($row['obtained'], 2) }} / {{ number_format($row['max'], 2) }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $row['percentage'] }}%</td>
                                <td class="py-3 px-4">
                                    @if ($row['passed'])
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">Pass</span>
                                    @else
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-rose-50 text-rose-700">Fail</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-slate-500">No grades entered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card overflow-hidden overflow-x-auto">
                <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 text-sm">Subject Breakdown</div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Average</th>
                            <th class="py-3 px-4">Highest</th>
                            <th class="py-3 px-4">Lowest</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($subjectRows as $row)
                            <tr>
                                <td class="py-3 px-4 font-medium text-slate-800">{{ $row['subject']->name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $row['average'] }} / {{ number_format($row['max'], 2) }}</td>
                                <td class="py-3 px-4 text-emerald-600">{{ $row['highest'] }}</td>
                                <td class="py-3 px-4 text-rose-600">{{ $row['lowest'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-slate-500">No grades entered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p class="text-sm text-slate-500">No exams have been set up yet.</p>
    @endif
</div>
