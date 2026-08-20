<div>
    @php
        $statusColors = [
            'paid' => 'bg-emerald-50 text-emerald-700',
            'partial' => 'bg-amber-50 text-amber-700',
            'unpaid' => 'bg-rose-50 text-rose-700',
        ];
        $currency = auth()->user()->school->currency;
    @endphp

    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Fees</h1>
                <p class="text-sm text-white/80 mt-0.5">Invoices and payment history.</p>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="stat-card">
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Billed</p>
                    <p class="text-xl font-bold text-slate-900 mt-1">{{ $currency }} {{ number_format($totals['billed'], 2) }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Paid</p>
                    <p class="text-xl font-bold text-emerald-600 mt-1">{{ $currency }} {{ number_format($totals['paid'], 2) }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <p class="text-xs font-medium text-slate-500">Balance Due</p>
                    <p class="text-xl font-bold text-rose-600 mt-1">{{ $currency }} {{ number_format($totals['due'], 2) }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>

        <!-- Mobile card list -->
        <div class="sm:hidden space-y-3">
            @forelse ($invoices as $invoice)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium text-slate-800">{{ $invoice->title }}</p>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap {{ $statusColors[$invoice->status] }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    <dl class="mt-3 grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <dt class="text-slate-400">Amount</dt>
                            <dd class="text-slate-700 font-medium mt-0.5">{{ $currency }} {{ number_format($invoice->amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Balance</dt>
                            <dd class="text-slate-700 font-medium mt-0.5">{{ $currency }} {{ number_format($invoice->balance(), 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Due</dt>
                            <dd class="text-slate-700 font-medium mt-0.5">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-500 text-sm">No invoices yet.</div>
            @endforelse
        </div>

        <!-- Desktop table -->
        <div class="hidden sm:block card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Balance</th>
                        <th class="py-3 px-4">Due</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $invoice->title }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $currency }} {{ number_format($invoice->amount, 2) }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $currency }} {{ number_format($invoice->balance(), 2) }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="py-3 px-4">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $statusColors[$invoice->status] }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-500">No invoices yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $invoices->links() }}</div>
    @else
        <p class="text-sm text-slate-500">No student profile linked to this account yet.</p>
    @endif
</div>
