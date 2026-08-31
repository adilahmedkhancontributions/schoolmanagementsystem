<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <h1 class="font-heading text-xl sm:text-2xl font-bold">Payroll</h1>
                    <p class="text-sm text-white/80 mt-0.5">Generate monthly payslips for {{ $periodLabel }} and record payments.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('school-admin.finance.salary-structures') }}" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                    <i class="fa-solid fa-money-check-dollar"></i> Salary Structures
                </a>
                <button type="button" wire:click="generate" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Payslips
                </button>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('message') }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-3 mb-5">
        <label class="text-sm font-medium text-slate-600">Pay period</label>
        <input type="month" wire:model.live="period" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
        @if ($structureCount > 0)
            <span class="text-xs text-slate-500">{{ $structureCount }} employee(s) with an active salary structure</span>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div>
                <p class="text-xs text-slate-500">Payslips</p>
                <p class="font-heading text-xl font-bold text-slate-900">{{ $summary['count'] }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-hourglass-half"></i></div>
            <div>
                <p class="text-xs text-slate-500">Pending payout</p>
                <p class="font-heading text-xl font-bold text-slate-900">{{ auth()->user()->school->currency }} {{ number_format($summary['draft'], 2) }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-circle-check"></i></div>
            <div>
                <p class="text-xs text-slate-500">Paid</p>
                <p class="font-heading text-xl font-bold text-slate-900">{{ auth()->user()->school->currency }} {{ number_format($summary['paid'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="py-3 px-4">Employee</th>
                        <th class="py-3 px-4 text-right">Gross</th>
                        <th class="py-3 px-4 text-right">Bonus</th>
                        <th class="py-3 px-4 text-right">Deduction</th>
                        <th class="py-3 px-4 text-right">Net</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payslips as $slip)
                        <tr>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 shrink-0 rounded-full brand-gradient text-white flex items-center justify-center text-xs font-semibold">
                                        {{ strtoupper(substr($slip->payable->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $slip->payable->user->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-slate-400">{{ $slip->payable->employee_id ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-600">{{ number_format($slip->gross(), 2) }}</td>
                            <td class="py-3 px-4">
                                @if ($slip->status === 'draft')
                                    <input type="number" step="0.01" min="0" wire:model.live="bonus.{{ $slip->id }}" wire:change="saveAdjustment({{ $slip->id }})"
                                        class="w-24 min-h-touch rounded-lg border border-slate-300 px-2 text-right text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                                @else
                                    <span class="text-xs text-slate-500 block text-right">{{ number_format($slip->bonus, 2) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if ($slip->status === 'draft')
                                    <input type="number" step="0.01" min="0" wire:model.live="deduction.{{ $slip->id }}" wire:change="saveAdjustment({{ $slip->id }})"
                                        class="w-24 min-h-touch rounded-lg border border-slate-300 px-2 text-right text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                                @else
                                    <span class="text-xs text-rose-500 block text-right">{{ number_format($slip->deduction, 2) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-semibold text-slate-800">{{ number_format($slip->net_salary, 2) }}</td>
                            <td class="py-3 px-4">
                                @if ($slip->status === 'paid')
                                    <span class="rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-1 text-xs font-medium">Paid</span>
                                @else
                                    <span class="rounded-full bg-amber-50 text-amber-700 px-2.5 py-1 text-xs font-medium">Draft</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                @if ($slip->status === 'draft')
                                    <button type="button" wire:click="markPaid({{ $slip->id }})" class="btn-primary text-xs !py-1.5 !px-3" title="Mark as paid">
                                        <i class="fa-solid fa-circle-check"></i> Mark paid
                                    </button>
                                    <button type="button" wire:click="delete({{ $slip->id }})" wire:confirm="Delete this draft payslip?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @else
                                    <button type="button" wire:click="revertToDraft({{ $slip->id }})" class="btn-secondary text-xs !py-1.5 !px-3" title="Revert to draft">
                                        <i class="fa-solid fa-rotate-left"></i> Revert
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-invoice-dollar text-3xl text-slate-300 mb-3 block"></i>
                                No payslips for {{ $periodLabel }} yet. Set up salary structures, then click "Generate Payslips".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
