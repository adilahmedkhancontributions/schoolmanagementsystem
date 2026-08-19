<?php

namespace App\Livewire\SchoolAdmin\Reports;

use App\Models\FeeInvoice;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.dashboard')]
class Fees extends Component
{
    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $invoices = FeeInvoice::with('student.schoolClass')->where('school_id', $schoolId)->get();

        $totalBilled = (float) $invoices->sum('amount');
        $totalPaid = (float) $invoices->sum('paid_amount');

        $classRows = $invoices->groupBy(fn ($invoice) => $invoice->student->schoolClass->name ?? 'Unassigned')
            ->map(function ($classInvoices, $className) {
                $billed = (float) $classInvoices->sum('amount');
                $paid = (float) $classInvoices->sum('paid_amount');

                return [
                    'class' => $className,
                    'billed' => $billed,
                    'paid' => $paid,
                    'due' => $billed - $paid,
                ];
            })->sortBy('class')->values();

        $overdue = FeeInvoice::with('student.user')
            ->where('school_id', $schoolId)
            ->where('status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get();

        return view('livewire.school-admin.reports.fees', [
            'summary' => [
                'billed' => $totalBilled,
                'paid' => $totalPaid,
                'due' => $totalBilled - $totalPaid,
                'collectionRate' => $totalBilled > 0 ? round($totalPaid / $totalBilled * 100, 1) : null,
            ],
            'classRows' => $classRows,
            'overdue' => $overdue,
        ]);
    }

    public function export(): StreamedResponse
    {
        $overdue = FeeInvoice::with('student.user')
            ->where('school_id', auth()->user()->school_id)
            ->where('status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get();

        return response()->streamDownload(function () use ($overdue) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Invoice Title', 'Amount', 'Balance', 'Due Date']);

            foreach ($overdue as $invoice) {
                fputcsv($handle, [
                    $invoice->student->user->name,
                    $invoice->title,
                    $invoice->amount,
                    $invoice->balance(),
                    $invoice->due_date->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 'overdue-fees-report.csv');
    }
}
