@php
    $statusColors = [
        'paid' => 'bg-emerald-50 text-emerald-700',
        'partial' => 'bg-amber-50 text-amber-700',
        'unpaid' => 'bg-rose-50 text-rose-700',
    ];
    $currency = auth()->user()->school->currency;
@endphp
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Fee Invoices</h1>
            <p class="text-sm text-slate-500 mt-1">Generate invoices and record payments.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('school-admin.fees.structures') }}" class="btn-secondary">
                <i class="fa-solid fa-list-check"></i> Structures
            </a>
            <button type="button" wire:click="openGenerate" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Generate Invoices
            </button>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search by title or student..."
               class="w-full sm:w-72 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">

        <select wire:model.live="filterClassId" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">All classes</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterStatus" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">All statuses</option>
            <option value="unpaid">Unpaid</option>
            <option value="partial">Partial</option>
            <option value="paid">Paid</option>
        </select>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($invoices as $invoice)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $invoice->student->user->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $invoice->title }} &middot; {{ $invoice->student->schoolClass?->name ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openPayment({{ $invoice->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Record payment">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </button>
                        <button type="button" wire:click="deleteInvoice({{ $invoice->id }})" wire:confirm="Delete this invoice and its payment history?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                        <div>
                            <dt class="text-slate-400 inline">Amount:</dt>
                            <dd class="text-slate-700 font-medium inline ml-1">{{ $currency }} {{ number_format($invoice->amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 inline">Balance:</dt>
                            <dd class="text-slate-700 font-medium inline ml-1">{{ $currency }} {{ number_format($invoice->balance(), 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 inline">Due:</dt>
                            <dd class="text-slate-700 font-medium inline ml-1">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</dd>
                        </div>
                    </dl>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap {{ $statusColors[$invoice->status] }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
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
                    <th class="py-3 px-4">Student</th>
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Balance</th>
                    <th class="py-3 px-4">Due</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($invoices as $invoice)
                    <tr>
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-800">{{ $invoice->student->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $invoice->student->schoolClass?->name ?? '—' }}</p>
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $invoice->title }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $currency }} {{ number_format($invoice->amount, 2) }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $currency }} {{ number_format($invoice->balance(), 2) }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $statusColors[$invoice->status] }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openPayment({{ $invoice->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Record payment">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </button>
                            <button type="button" wire:click="deleteInvoice({{ $invoice->id }})" wire:confirm="Delete this invoice and its payment history?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-500">No invoices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $invoices->links() }}</div>

    <x-crud-modal :show="$showGenerateModal" wireClose="closeGenerateModal" title="Generate Invoices" maxWidth="xl">
        <form wire:submit="generate" class="space-y-4">
            <x-floating-select label="Fee structure (optional template)" name="generateStructureId" wire:model.live="generateStructureId">
                <option value="">— Custom —</option>
                @foreach ($structures as $structure)
                    <option value="{{ $structure->id }}">{{ $structure->name }} ({{ $currency }} {{ number_format($structure->amount, 2) }})</option>
                @endforeach
            </x-floating-select>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="Invoice title" name="generateTitle" wire:model="generateTitle" />
                <x-floating-input label="Amount" name="generateAmount" type="number" step="0.01" wire:model="generateAmount" />
                <x-floating-input label="Due date" name="generateDueDate" type="date" wire:model="generateDueDate" />
                <x-floating-select label="Class (leave blank for a single student)" name="generateClassId" wire:model.live="generateClassId">
                    <option value="">— All / single student —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </x-floating-select>
                @if ($generateClassId)
                    <x-floating-select label="Limit to one student (optional)" name="generateStudentId" wire:model="generateStudentId">
                        <option value="">Whole class</option>
                        @foreach ($generateClassStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                        @endforeach
                    </x-floating-select>
                @endif
            </div>
            <p class="text-xs text-slate-500">
                Leave "Class" blank and pick no student to bill every student in the school, or choose a class to bill
                everyone in it, or narrow down to a single student.
            </p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeGenerateModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Generate</button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal :show="$showPaymentModal" wireClose="closePaymentModal" title="Record Payment" maxWidth="lg">
        @if ($activeInvoice)
            <div class="mb-4 rounded-lg bg-slate-50 p-4 text-sm">
                <p class="font-medium text-slate-800">{{ $activeInvoice->student->user->name }} — {{ $activeInvoice->title }}</p>
                <p class="text-slate-500 mt-1">
                    Total {{ $currency }} {{ number_format($activeInvoice->amount, 2) }} &middot;
                    Paid {{ $currency }} {{ number_format($activeInvoice->paid_amount, 2) }} &middot;
                    Balance {{ $currency }} {{ number_format($activeInvoice->balance(), 2) }}
                </p>
            </div>

            @if ($activeInvoice->status !== 'paid')
                <form wire:submit="recordPayment" class="space-y-4 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-floating-input label="Amount" name="paymentAmount" type="number" step="0.01" wire:model="paymentAmount" />
                        <x-floating-input label="Date" name="paymentDate" type="date" wire:model="paymentDate" />
                        <x-floating-select label="Method" name="paymentMethod" wire:model="paymentMethod">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                            <option value="other">Other</option>
                        </x-floating-select>
                        <x-floating-input label="Notes (optional)" name="paymentNotes" wire:model="paymentNotes" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary">Record Payment</button>
                    </div>
                </form>
            @endif

            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Payment history</h4>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse ($activeInvoice->payments as $payment)
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2 text-sm">
                        <div>
                            <p class="font-medium text-slate-800">{{ $currency }} {{ number_format($payment->amount, 2) }}</p>
                            <p class="text-xs text-slate-500">{{ $payment->paid_at->format('M d, Y') }} &middot; {{ ucfirst(str_replace('_', ' ', $payment->method)) }}</p>
                        </div>
                        @if ($payment->notes)
                            <p class="text-xs text-slate-400 max-w-[40%] truncate" title="{{ $payment->notes }}">{{ $payment->notes }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No payments recorded yet.</p>
                @endforelse
            </div>
        @endif
    </x-crud-modal>
</div>
