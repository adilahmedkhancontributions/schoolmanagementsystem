<div>
    @include('livewire.school-admin.reports._tabs', ['active' => 'attendance'])

    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 mb-6">
        <input type="date" wire:model.live="startDate" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
        <input type="date" wire:model.live="endDate" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
        <select wire:model.live="classId" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">All classes</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>
        <button type="button" wire:click="export" class="btn-secondary sm:ml-auto">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div>
                <p class="text-xs font-medium text-slate-500">Students</p>
                <p class="text-xl font-bold text-slate-900 mt-1">{{ $summary['students'] }}</p>
            </div>
            <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <p class="text-xs font-medium text-slate-500">Average Attendance Rate</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">{{ $summary['averageRate'] !== null ? $summary['averageRate'].'%' : '—' }}</p>
            </div>
            <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Absences</p>
                <p class="text-xl font-bold text-rose-600 mt-1">{{ $summary['totalAbsent'] }}</p>
            </div>
            <div class="h-10 w-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Student</th>
                    <th class="py-3 px-4">Present</th>
                    <th class="py-3 px-4">Absent</th>
                    <th class="py-3 px-4">Late</th>
                    <th class="py-3 px-4">Half Day</th>
                    <th class="py-3 px-4">Leave</th>
                    <th class="py-3 px-4">Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rows as $row)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800 whitespace-nowrap">{{ $row['student']->user->name }}</td>
                        <td class="py-3 px-4 text-emerald-600">{{ $row['present'] }}</td>
                        <td class="py-3 px-4 text-rose-600">{{ $row['absent'] }}</td>
                        <td class="py-3 px-4 text-amber-600">{{ $row['late'] }}</td>
                        <td class="py-3 px-4 text-sky-600">{{ $row['half_day'] }}</td>
                        <td class="py-3 px-4 text-slate-500">{{ $row['leave'] }}</td>
                        <td class="py-3 px-4 font-semibold text-slate-800">{{ $row['rate'] !== null ? $row['rate'].'%' : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-500">No attendance records in this range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
