<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                    <i class="fa-solid fa-money-check-dollar text-lg"></i>
                </div>
                <div>
                    <h1 class="font-heading text-xl sm:text-2xl font-bold">Salary Structures</h1>
                    <p class="text-sm text-white/80 mt-0.5">Set monthly pay for teachers and staff. Payroll is generated from these structures.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('school-admin.finance.payroll') }}" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Payroll
                </a>
                <button type="button" wire:click="openCreate('teacher')" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                    <i class="fa-solid fa-plus"></i> New Structure
                </button>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('message') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="py-3 px-4">Employee</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4 text-right">Basic</th>
                        <th class="py-3 px-4 text-right">Allowances</th>
                        <th class="py-3 px-4 text-right">Deduction</th>
                        <th class="py-3 px-4 text-right">Net</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($structures as $structure)
                        <tr>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 shrink-0 rounded-full brand-gradient text-white flex items-center justify-center text-xs font-semibold">
                                        {{ strtoupper(substr($structure->payable->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-slate-800 truncate">{{ $structure->payable->user->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $structure->payable->employee_id ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $structure->payable_type === \App\Models\Teacher::class ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $structure->payable_type === \App\Models\Teacher::class ? 'Teacher' : 'Staff' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-700">{{ auth()->user()->school->currency }} {{ number_format($structure->basic_salary, 2) }}</td>
                            <td class="py-3 px-4 text-right text-slate-600">{{ number_format($structure->house_allowance + $structure->transport_allowance + $structure->other_allowance, 2) }}</td>
                            <td class="py-3 px-4 text-right text-rose-600">{{ number_format($structure->deduction, 2) }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-slate-800">{{ number_format($structure->net(), 2) }}</td>
                            <td class="py-3 px-4">
                                @if ($structure->status === 'active')
                                    <span class="rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-1 text-xs font-medium">Active</span>
                                @else
                                    <span class="rounded-full bg-slate-100 text-slate-500 px-2.5 py-1 text-xs font-medium">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button type="button" wire:click="openEdit({{ $structure->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                @if ($structure->status === 'active')
                                    <button type="button" wire:click="deactivate({{ $structure->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-amber-600" title="Deactivate">
                                        <i class="fa-solid fa-toggle-on"></i>
                                    </button>
                                @endif
                                <button type="button" wire:click="delete({{ $structure->id }})" wire:confirm="Delete this salary structure?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-money-check-dollar text-3xl text-slate-300 mb-3 block"></i>
                                No salary structures yet. Add one for each teacher or staff member.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog">
            <div class="fixed inset-0 bg-slate-900/60" wire:click="closeModal"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-heading text-lg font-bold text-slate-900">{{ $structureId ? 'Edit Salary Structure' : 'New Salary Structure' }}</h2>
                    <button type="button" wire:click="closeModal" class="min-h-touch min-w-touch text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Employee type</label>
                        <select wire:model.live="employeeType" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            <option value="teacher">Teacher</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $employeeType === 'teacher' ? 'Teacher' : 'Staff member' }}</label>
                        <select wire:model="employeeId" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            <option value="">Select {{ $employeeType }}...</option>
                            @foreach (($employeeType === 'teacher' ? $teachers : $staff) as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->user->name }} ({{ $employee->employee_id }})</option>
                            @endforeach
                        </select>
                        @error('employeeId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-floating-input label="Basic salary" name="basicSalary" type="number" step="0.01" min="0" wire:model="basicSalary" />
                            @error('basicSalary') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-floating-input label="House allowance" name="houseAllowance" type="number" step="0.01" min="0" wire:model="houseAllowance" />
                        </div>
                        <div>
                            <x-floating-input label="Transport allowance" name="transportAllowance" type="number" step="0.01" min="0" wire:model="transportAllowance" />
                        </div>
                        <div>
                            <x-floating-input label="Other allowance" name="otherAllowance" type="number" step="0.01" min="0" wire:model="otherAllowance" />
                        </div>
                        <div>
                            <x-floating-input label="Monthly deduction" name="deduction" type="number" step="0.01" min="0" wire:model="deduction" />
                        </div>
                        <div>
                            <x-floating-input label="Effective from" name="effectiveFrom" type="date" wire:model="effectiveFrom" />
                        </div>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">
                        Net monthly: <span class="font-semibold text-slate-800">{{ auth()->user()->school->currency }}
                            {{ number_format((float)($basicSalary ?: 0) + (float)($houseAllowance ?: 0) + (float)($transportAllowance ?: 0) + (float)($otherAllowance ?: 0) - (float)($deduction ?: 0), 2) }}
                        </span>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
