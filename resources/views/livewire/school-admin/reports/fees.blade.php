<div>
    @include('livewire.school-admin.reports._tabs', ['active' => 'fees'])

    @php($currency = auth()->user()->school->currency)

    <div class="flex justify-end mb-4">
        <button type="button" wire:click="export" class="btn-secondary">
            <i class="fa-solid fa-file-csv"></i> Export Overdue CSV
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Billed</p>
                <p class="text-xl font-bold text-slate-900 mt-1">{{ $currency }} {{ number_format($summary['billed'], 2) }}</p>
            </div>
            <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Collected</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">{{ $currency }} {{ number_format($summary['paid'], 2) }}</p>
            </div>
            <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <p class="text-xs font-medium text-slate-500">Outstanding</p>
                <p class="text-xl font-bold text-rose-600 mt-1">{{ $currency }} {{ number_format($summary['due'], 2) }}</p>
            </div>
            <div class="h-10 w-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <p class="text-xs font-medium text-slate-500">Collection Rate</p>
                <p class="text-xl font-bold text-slate-900 mt-1">{{ $summary['collectionRate'] !== null ? $summary['collectionRate'].'%' : '—' }}</p>
            </div>
            <div class="h-10 w-10 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                <i class="fa-solid fa-percent"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card overflow-hidden overflow-x-auto">
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 text-sm">By Class</div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="py-3 px-4">Class</th>
                        <th class="py-3 px-4">Billed</th>
                        <th class="py-3 px-4">Paid</th>
                        <th class="py-3 px-4">Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($classRows as $row)
                        <tr>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $row['class'] }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $currency }} {{ number_format($row['billed'], 2) }}</td>
                            <td class="py-3 px-4 text-emerald-600">{{ $currency }} {{ number_format($row['paid'], 2) }}</td>
                            <td class="py-3 px-4 text-rose-600">{{ $currency }} {{ number_format($row['due'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-500">No invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card overflow-hidden overflow-x-auto">
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 text-sm">Overdue Invoices</div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Balance</th>
                        <th class="py-3 px-4">Due Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($overdue as $invoice)
                        <tr>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $invoice->student->user->name }}</td>
                            <td class="py-3 px-4 text-rose-600">{{ $currency }} {{ number_format($invoice->balance(), 2) }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $invoice->due_date->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-slate-500">No overdue invoices.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
