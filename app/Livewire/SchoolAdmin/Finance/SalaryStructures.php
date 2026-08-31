<?php

namespace App\Livewire\SchoolAdmin\Finance;

use App\Models\SalaryStructure;
use App\Models\Staff;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class SalaryStructures extends Component
{
    public bool $showModal = false;

    public ?int $structureId = null;

    public string $employeeType = 'teacher';

    public ?int $employeeId = null;

    public string $basicSalary = '';

    public string $houseAllowance = '';

    public string $transportAllowance = '';

    public string $otherAllowance = '';

    public string $deduction = '';

    public string $effectiveFrom = '';

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $structures = SalaryStructure::with('payable.user')
            ->where('school_id', $schoolId)
            ->orderByDesc('updated_at')
            ->get();

        $teachers = Teacher::with('user')->where('school_id', $schoolId)->orderBy('employee_id')->get();
        $staff = Staff::with('user')->where('school_id', $schoolId)->orderBy('employee_id')->get();

        return view('livewire.school-admin.finance.salary-structures', [
            'structures' => $structures,
            'teachers' => $teachers,
            'staff' => $staff,
        ]);
    }

    public function openCreate(string $type = 'teacher'): void
    {
        $this->resetExcept(['structures', 'teachers', 'staff']);
        $this->employeeType = $type;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $structure = SalaryStructure::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->structureId = $structure->id;
        $this->employeeType = $structure->payable_type === Teacher::class ? 'teacher' : 'staff';
        $this->employeeId = $structure->payable_id;
        $this->basicSalary = (string) $structure->basic_salary;
        $this->houseAllowance = (string) $structure->house_allowance;
        $this->transportAllowance = (string) $structure->transport_allowance;
        $this->otherAllowance = (string) $structure->other_allowance;
        $this->deduction = (string) $structure->deduction;
        $this->effectiveFrom = $structure->effective_from?->format('Y-m-d') ?? '';

        $this->showModal = true;
    }

    public function updatedEmployeeType(): void
    {
        $this->employeeId = null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'employeeType' => 'required|in:teacher,staff',
            'employeeId' => 'required|integer',
            'basicSalary' => 'required|numeric|min:0',
            'houseAllowance' => 'nullable|numeric|min:0',
            'transportAllowance' => 'nullable|numeric|min:0',
            'otherAllowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'effectiveFrom' => 'nullable|date',
        ]);

        $schoolId = auth()->user()->school_id;
        $model = $this->employeeType === 'teacher' ? Teacher::class : Staff::class;

        // Verify the employee belongs to this school
        $model::where('school_id', $schoolId)->findOrFail($validated['employeeId']);

        // Deactivate any existing active structures for this employee
        SalaryStructure::where('school_id', $schoolId)
            ->where('payable_type', $model)
            ->where('payable_id', $validated['employeeId'])
            ->update(['status' => 'inactive']);

        SalaryStructure::create([
            'school_id' => $schoolId,
            'payable_type' => $model,
            'payable_id' => $validated['employeeId'],
            'basic_salary' => $validated['basicSalary'],
            'house_allowance' => $validated['houseAllowance'] ?? 0,
            'transport_allowance' => $validated['transportAllowance'] ?? 0,
            'other_allowance' => $validated['otherAllowance'] ?? 0,
            'deduction' => $validated['deduction'] ?? 0,
            'effective_from' => $validated['effectiveFrom'] ?: null,
            'status' => 'active',
        ]);

        $this->showModal = false;
        $this->resetExcept(['structures', 'teachers', 'staff']);
    }

    public function deactivate(int $id): void
    {
        SalaryStructure::where('school_id', auth()->user()->school_id)
            ->where('id', $id)
            ->update(['status' => 'inactive']);
    }

    public function delete(int $id): void
    {
        SalaryStructure::where('school_id', auth()->user()->school_id)
            ->where('id', $id)
            ->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetExcept(['structures', 'teachers', 'staff']);
    }
}
