<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-money-check-dollar text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">My Pay</h1>
                <p class="text-sm text-white/80 mt-0.5">Review your monthly payslips.</p>
            </div>
        </div>
    </div>

    @if ($latest)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="card p-4">
                <p class="text-xs text-slate-500">Latest period</p>
                <p class="font-heading text-sm font-bold text-slate-900 mt-1">{{ $latest->periodLabel() }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-slate-500">Gross</p>
                <p class="font-heading text-sm font-bold text-slate-900 mt-1">{{ auth()->user()->school->currency }} {{ number_format($latest->gross(), 2) }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-slate-500">Net salary</p>
                <p class="font-heading text-sm font-bold text-emerald-600 mt-1">{{ auth()->user()->school->currency }} {{ number_format($latest->net_salary, 2) }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-slate-500">Status</p>
                <p class="mt-1">
                    @if ($latest->status === 'paid')
                        <span class="rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-1 text-xs font-medium">Paid</span>
                    @else
                        <span class="rounded-full bg-amber-50 text-amber-700 px-2.5 py-1 text-xs font-medium">Pending</span>
                    @endif
                </p>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($payslips as $slip)
            <div class="card p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="font-heading font-bold text-slate-900">{{ $slip->periodLabel() }}</p>
                        @if ($slip->paid_at)
                            <p class="text-xs text-slate-400 mt-0.5">Paid on {{ $slip->paid_at->format('d M Y') }}</p>
                        @else
                            <p class="text-xs text-amber-600 mt-0.5">Awaiting payment</p>
                        @endif
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs text-slate-500">Net salary</p>
                        <p class="font-heading text-lg font-bold text-emerald-600">{{ auth()->user()->school->currency }} {{ number_format($slip->net_salary, 2) }}</p>
                    </div>
                </div>
                <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm border-t border-slate-100 pt-4">
                    <div>
                        <dt class="text-xs text-slate-400">Basic</dt>
                        <dd class="text-slate-800 font-medium mt-0.5">{{ number_format($slip->basic_salary, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Allowances</dt>
                        <dd class="text-slate-800 font-medium mt-0.5">{{ number_format($slip->house_allowance + $slip->transport_allowance + $slip->other_allowance, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Bonus</dt>
                        <dd class="text-emerald-600 font-medium mt-0.5">+{{ number_format($slip->bonus, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Deduction</dt>
                        <dd class="text-rose-600 font-medium mt-0.5">-{{ number_format($slip->deduction, 2) }}</dd>
                    </div>
                </dl>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-500">No payslips have been generated for you yet.</div>
        @endforelse
    </div>
</div>
