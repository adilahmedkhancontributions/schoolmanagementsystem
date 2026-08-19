<?php

namespace App\Livewire\SchoolAdmin\Fees;

use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Invoices extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public ?int $filterClassId = null;

    public bool $showGenerateModal = false;

    public ?int $generateStructureId = null;

    public ?int $generateClassId = null;

    public ?int $generateStudentId = null;

    public string $generateTitle = '';

    public string $generateAmount = '';

    public string $generateDueDate = '';

    public bool $showPaymentModal = false;

    public ?int $activeInvoiceId = null;

    public string $paymentAmount = '';

    public string $paymentMethod = 'cash';

    public string $paymentDate = '';

    public string $paymentNotes = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterClassId(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $invoices = FeeInvoice::with(['student.user', 'student.schoolClass'])
            ->where('school_id', $schoolId)
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterClassId, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('school_class_id', $this->filterClassId)))
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%")
                        ->orWhereHas('student.user', fn ($q3) => $q3->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.school-admin.fees.invoices', [
            'invoices' => $invoices,
            'classes' => SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get(),
            'structures' => FeeStructure::where('school_id', $schoolId)->orderBy('name')->get(),
            'generateClassStudents' => $this->generateClassId
                ? Student::with('user')->where('school_class_id', $this->generateClassId)->where('school_id', $schoolId)->get()
                : collect(),
            'activeInvoice' => $this->activeInvoiceId
                ? FeeInvoice::with(['student.user', 'payments' => fn ($q) => $q->orderByDesc('paid_at')])->find($this->activeInvoiceId)
                : null,
        ]);
    }

    public function openGenerate(): void
    {
        $this->resetGenerateForm();
        $this->showGenerateModal = true;
    }

    public function updatedGenerateStructureId(): void
    {
        $this->generateStudentId = null;

        if ($this->generateStructureId) {
            $structure = FeeStructure::find($this->generateStructureId);
            $this->generateTitle = $structure->name;
            $this->generateAmount = (string) $structure->amount;
            $this->generateClassId = $structure->school_class_id;
        }
    }

    public function updatedGenerateClassId(): void
    {
        $this->generateStudentId = null;
    }

    public function closeGenerateModal(): void
    {
        $this->showGenerateModal = false;
        $this->resetGenerateForm();
    }

    public function generate(): void
    {
        $validated = $this->validate([
            'generateTitle' => 'required|string|max:150',
            'generateAmount' => 'required|numeric|min:0',
            'generateDueDate' => 'nullable|date',
            'generateClassId' => 'nullable|exists:school_classes,id',
            'generateStudentId' => 'nullable|exists:students,id',
        ]);

        $schoolId = auth()->user()->school_id;

        $students = $this->generateStudentId
            ? Student::where('id', $this->generateStudentId)->where('school_id', $schoolId)->get()
            : Student::where('school_id', $schoolId)
                ->when($this->generateClassId, fn ($q) => $q->where('school_class_id', $this->generateClassId))
                ->get();

        foreach ($students as $student) {
            FeeInvoice::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'fee_structure_id' => $this->generateStructureId,
                'title' => $validated['generateTitle'],
                'amount' => $validated['generateAmount'],
                'due_date' => $validated['generateDueDate'] ?: null,
                'status' => 'unpaid',
            ]);
        }

        $this->showGenerateModal = false;
        $this->resetGenerateForm();
    }

    private function resetGenerateForm(): void
    {
        $this->reset(['generateStructureId', 'generateClassId', 'generateStudentId', 'generateTitle', 'generateAmount', 'generateDueDate']);
        $this->resetErrorBag();
    }

    public function openPayment(int $invoiceId): void
    {
        $this->activeInvoiceId = FeeInvoice::where('school_id', auth()->user()->school_id)->findOrFail($invoiceId)->id;
        $this->paymentAmount = '';
        $this->paymentMethod = 'cash';
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->activeInvoiceId = null;
        $this->resetErrorBag();
    }

    public function recordPayment(): void
    {
        $invoice = FeeInvoice::where('school_id', auth()->user()->school_id)->findOrFail($this->activeInvoiceId);

        $validated = $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:0.01', 'max:'.$invoice->balance()],
            'paymentMethod' => 'required|in:cash,bank_transfer,cheque,online,other',
            'paymentDate' => 'required|date',
            'paymentNotes' => 'nullable|string|max:255',
        ]);

        FeePayment::create([
            'fee_invoice_id' => $invoice->id,
            'amount' => $validated['paymentAmount'],
            'method' => $validated['paymentMethod'],
            'paid_at' => $validated['paymentDate'],
            'notes' => $validated['paymentNotes'] ?: null,
            'recorded_by' => auth()->id(),
        ]);

        $invoice->paid_amount = bcadd((string) $invoice->paid_amount, (string) $validated['paymentAmount'], 2);
        $invoice->refreshStatus();

        $this->paymentAmount = '';
        $this->paymentNotes = '';
    }

    public function deleteInvoice(int $id): void
    {
        FeeInvoice::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();

        if ($this->activeInvoiceId === $id) {
            $this->closePaymentModal();
        }
    }
}
