<?php

namespace App\Livewire\Fees;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class MyFees extends Component
{
    use WithPagination;

    public ?int $studentId = null;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user->hasRole('parent')) {
            $this->studentId = $user->guardianProfile?->students()->first()?->id;
        } elseif ($user->hasRole('student')) {
            $this->studentId = $user->student?->id;
        }
    }

    public function render(): View
    {
        $user = auth()->user();

        $children = $user->hasRole('parent')
            ? $user->guardianProfile?->students()->with('user')->get() ?? collect()
            : collect();

        $allowedIds = $user->hasRole('parent')
            ? $children->pluck('id')
            : collect([$user->student?->id]);

        if (! $allowedIds->contains($this->studentId)) {
            $this->studentId = null;
        }

        $invoices = collect();
        $totals = ['billed' => 0, 'paid' => 0, 'due' => 0];

        if ($this->studentId) {
            $student = Student::findOrFail($this->studentId);

            $invoices = $student->feeInvoices()
                ->with('payments')
                ->orderByDesc('due_date')
                ->orderByDesc('id')
                ->paginate(10);

            $totals['billed'] = $student->feeInvoices()->sum('amount');
            $totals['paid'] = $student->feeInvoices()->sum('paid_amount');
            $totals['due'] = $totals['billed'] - $totals['paid'];
        }

        return view('livewire.fees.my-fees', [
            'children' => $children,
            'invoices' => $invoices,
            'totals' => $totals,
        ]);
    }
}
