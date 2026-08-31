<?php

namespace App\Livewire\SchoolAdmin\Finance;

use App\Models\Payslip;
use App\Models\SalaryStructure;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Payroll extends Component
{
    public string $period;

    public array $bonus = [];

    public array $deduction = [];

    public function mount(): void
    {
        $this->period = now()->format('Y-m');
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $payslips = Payslip::with('payable.user')
            ->where('school_id', $schoolId)
            ->where('period', $this->period)
            ->orderByRaw("CASE WHEN payable_type = '".\App\Models\Teacher::class."' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->get();

        $summary = [
            'draft' => $payslips->where('status', 'draft')->sum('net_salary'),
            'paid' => $payslips->where('status', 'paid')->sum('net_salary'),
            'count' => $payslips->count(),
        ];

        // Employees eligible this month (active salary structure, no payslip yet)
        $structureCount = SalaryStructure::where('school_id', $schoolId)->where('status', 'active')->count();

        return view('livewire.school-admin.finance.payroll', [
            'payslips' => $payslips,
            'summary' => $summary,
            'structureCount' => $structureCount,
            'periodLabel' => \Carbon\Carbon::createFromFormat('Y-m', $this->period)->format('F Y'),
        ]);
    }

    public function updatedPeriod(): void
    {
        $this->bonus = [];
        $this->deduction = [];
    }

    public function generate(): void
    {
        $schoolId = auth()->user()->school_id;
        $period = $this->validatedPeriod();

        $structures = SalaryStructure::with('payable')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->whereHas('payable')
            ->get();

        $created = 0;
        foreach ($structures as $structure) {
            $existing = Payslip::where('school_id', $schoolId)
                ->where('payable_type', $structure->payable_type)
                ->where('payable_id', $structure->payable_id)
                ->where('period', $period)
                ->exists();

            if ($existing) {
                continue;
            }

            $gross = $structure->basic_salary + $structure->house_allowance + $structure->transport_allowance + $structure->other_allowance;

            Payslip::create([
                'school_id' => $schoolId,
                'payable_type' => $structure->payable_type,
                'payable_id' => $structure->payable_id,
                'period' => $period,
                'basic_salary' => $structure->basic_salary,
                'house_allowance' => $structure->house_allowance,
                'transport_allowance' => $structure->transport_allowance,
                'other_allowance' => $structure->other_allowance,
                'bonus' => 0,
                'deduction' => $structure->deduction,
                'net_salary' => $gross - $structure->deduction,
                'status' => 'draft',
            ]);
            $created++;
        }

        session()->flash('message', $created > 0
            ? "Generated {$created} payslip(s) for {$this->periodLabel()}."
            : 'No new payslips to generate — every eligible employee already has one.');
    }

    public function saveAdjustment(int $slipId): void
    {
        $slip = Payslip::where('school_id', auth()->user()->school_id)->findOrFail($slipId);

        if ($slip->status === 'paid') {
            return;
        }

        $bonus = (float) ($this->bonus[$slipId] ?? 0);
        $deduction = (float) ($this->deduction[$slipId] ?? 0);

        $net = $slip->gross() + $bonus - $deduction;

        $slip->update([
            'bonus' => $bonus,
            'deduction' => $deduction,
            'net_salary' => max(0, $net),
        ]);

        $this->bonus[$slipId] = (string) $bonus;
        $this->deduction[$slipId] = (string) $deduction;
    }

    public function markPaid(int $slipId): void
    {
        Payslip::where('school_id', auth()->user()->school_id)
            ->where('id', $slipId)
            ->update(['status' => 'paid', 'paid_at' => now()]);
    }

    public function revertToDraft(int $slipId): void
    {
        Payslip::where('school_id', auth()->user()->school_id)
            ->where('id', $slipId)
            ->update(['status' => 'draft', 'paid_at' => null]);
    }

    public function delete(int $slipId): void
    {
        Payslip::where('school_id', auth()->user()->school_id)
            ->where('id', $slipId)
            ->where('status', 'draft')
            ->delete();
    }

    private function validatedPeriod(): string
    {
        return \Illuminate\Support\Str::limit(preg_replace('/[^0-9-]/', '', $this->period), 7, '');
    }

    private function periodLabel(): string
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->validatedPeriod())->format('F Y');
    }
}
