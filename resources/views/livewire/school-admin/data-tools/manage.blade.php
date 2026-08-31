<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-file-arrow-up text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Data Import / Export</h1>
                <p class="text-sm text-white/80 mt-0.5">Bulk-import students from a CSV file, or export your school's records.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <div class="card p-4">
            <p class="text-xs text-slate-500">Students</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $counts['students'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500">Teachers</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $counts['teachers'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500">Staff</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $counts['staff'] }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500">Fee Invoices</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $counts['invoices'] }}</p>
        </div>
    </div>

    <div class="card p-5 mb-8">
        <h2 class="font-heading text-lg font-semibold text-slate-900 mb-1">Import Students</h2>
        <p class="text-sm text-slate-500 mb-4">
            Upload a CSV with columns: <code class="text-xs bg-slate-100 px-1 py-0.5 rounded">name, email, phone, admission_number, class_name, section_name, gender, date_of_birth</code>.
            Class/section names must already exist. Each valid row creates a Student login with a one-time password.
        </p>

        <button type="button" wire:click="downloadStudentTemplate" class="btn-secondary mb-4">
            <i class="fa-solid fa-download"></i> Download CSV Template
        </button>

        @if ($importCommitted)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-sm text-emerald-800 mb-3">
                    Imported {{ count($importedCredentials) }} student(s) successfully. Download the credentials list
                    below to share temporary passwords securely — they will not be shown again.
                </p>
                <div class="flex gap-2">
                    <button type="button" wire:click="downloadCredentials" class="btn-primary">
                        <i class="fa-solid fa-file-csv"></i> Download Credentials CSV
                    </button>
                    <button type="button" wire:click="cancelImport" class="btn-secondary">Done</button>
                </div>
            </div>
        @else
            <div class="space-y-4">
                <input type="file" wire:model="importFile" accept=".csv,text/csv" class="block w-full text-sm">
                @error('importFile') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

                <div wire:loading wire:target="importFile" class="text-sm text-slate-500">Parsing file...</div>

                @if (!empty($importRows))
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="status-chip bg-emerald-50 text-emerald-700">{{ $importValidCount }} valid</span>
                        <span class="status-chip bg-rose-50 text-rose-700">{{ $importErrorCount }} with errors</span>
                    </div>

                    <div class="overflow-x-auto border border-slate-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                                    <th class="py-2 px-3">Name</th>
                                    <th class="py-2 px-3">Email</th>
                                    <th class="py-2 px-3">Admission No.</th>
                                    <th class="py-2 px-3">Class / Section</th>
                                    <th class="py-2 px-3">Issues</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($importRows as $row)
                                    <tr class="{{ !empty($row['errors']) ? 'bg-rose-50/50' : '' }}">
                                        <td class="py-2 px-3">{{ $row['name'] }}</td>
                                        <td class="py-2 px-3">{{ $row['email'] }}</td>
                                        <td class="py-2 px-3">{{ $row['admission_number'] }}</td>
                                        <td class="py-2 px-3">
                                            {{ \App\Models\SchoolClass::find($row['school_class_id'])?->name ?? '—' }}
                                        </td>
                                        <td class="py-2 px-3 text-rose-600 text-xs">
                                            @foreach ($row['errors'] as $error)
                                                <div>{{ $error }}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" wire:click="commitImport" @if($importValidCount === 0) disabled @endif
                                class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-upload"></i> Import {{ $importValidCount }} Valid Row(s)
                        </button>
                        <button type="button" wire:click="cancelImport" class="btn-secondary">Cancel</button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="card p-5">
        <h2 class="font-heading text-lg font-semibold text-slate-900 mb-1">Export Data</h2>
        <p class="text-sm text-slate-500 mb-4">Download your school's records as CSV files.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button type="button" wire:click="exportStudents" class="btn-secondary justify-center">
                <i class="fa-solid fa-file-csv"></i> Students
            </button>
            <button type="button" wire:click="exportTeachers" class="btn-secondary justify-center">
                <i class="fa-solid fa-file-csv"></i> Teachers
            </button>
            <button type="button" wire:click="exportStaff" class="btn-secondary justify-center">
                <i class="fa-solid fa-file-csv"></i> Staff
            </button>
            <button type="button" wire:click="exportFeeInvoices" class="btn-secondary justify-center">
                <i class="fa-solid fa-file-csv"></i> Fee Invoices
            </button>
            <button type="button" wire:click="exportAttendance" class="btn-secondary justify-center">
                <i class="fa-solid fa-file-csv"></i> Attendance
            </button>
            <button type="button" wire:click="exportExamResults" class="btn-secondary justify-center">
                <i class="fa-solid fa-file-csv"></i> Exam Results
            </button>
        </div>
    </div>
</div>
