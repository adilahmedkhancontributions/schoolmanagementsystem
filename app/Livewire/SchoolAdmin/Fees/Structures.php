<?php

namespace App\Livewire\SchoolAdmin\Fees;

use App\Models\FeeStructure;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Structures extends Component
{
    public bool $showModal = false;

    public ?int $structureId = null;

    public string $name = '';

    public string $amount = '';

    public ?int $schoolClassId = null;

    public string $frequency = 'one_time';

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        return view('livewire.school-admin.fees.structures', [
            'structures' => FeeStructure::with('schoolClass')
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get(),
            'classes' => SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get(),
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $structure = FeeStructure::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->structureId = $structure->id;
        $this->name = $structure->name;
        $this->amount = (string) $structure->amount;
        $this->schoolClassId = $structure->school_class_id;
        $this->frequency = $structure->frequency;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0',
            'schoolClassId' => 'nullable|exists:school_classes,id',
            'frequency' => 'required|in:one_time,monthly,quarterly,term,annual',
        ]);

        $schoolId = auth()->user()->school_id;

        FeeStructure::updateOrCreate(
            ['id' => $this->structureId, 'school_id' => $schoolId],
            [
                'school_id' => $schoolId,
                'name' => $validated['name'],
                'amount' => $validated['amount'],
                'school_class_id' => $validated['schoolClassId'] ?: null,
                'frequency' => $validated['frequency'],
            ]
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        FeeStructure::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['structureId', 'name', 'amount', 'schoolClassId']);
        $this->frequency = 'one_time';
        $this->resetErrorBag();
    }
}
