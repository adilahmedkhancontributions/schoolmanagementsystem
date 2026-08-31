<?php

namespace App\Livewire\Finance;

use App\Models\Payslip;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class MyPayslips extends Component
{
    public function render(): View
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        $payslips = collect();

        if ($user->teacher) {
            $payslips = Payslip::with('payable')
                ->where('school_id', $schoolId)
                ->where('payable_type', \App\Models\Teacher::class)
                ->where('payable_id', $user->teacher->id)
                ->orderByDesc('period')
                ->get();
        } elseif ($user->staffProfile) {
            $payslips = Payslip::with('payable')
                ->where('school_id', $schoolId)
                ->where('payable_type', \App\Models\Staff::class)
                ->where('payable_id', $user->staffProfile->id)
                ->orderByDesc('period')
                ->get();
        }

        $latest = $payslips->first();

        return view('livewire.finance.my-payslips', [
            'payslips' => $payslips,
            'latest' => $latest,
        ]);
    }
}
