<?php

namespace App\Livewire\SchoolAdmin\Subjects;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $subjectId = null;

    public string $name = '';

    public string $code = '';

    public ?int $schoolClassId = null;

    public ?int $teacherId = null;

    public bool $isElective = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $subjects = Subject::with(['schoolClass', 'teacher.user'])
            ->where('school_id', $schoolId)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.school-admin.subjects.manage', [
            'subjects' => $subjects,
            'classes' => SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get(),
            'teachers' => Teacher::where('school_id', $schoolId)->with('user')->get(),
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $subject = Subject::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->subjectId = $subject->id;
        $this->name = $subject->name;
        $this->code = (string) $subject->code;
        $this->schoolClassId = $subject->school_class_id;
        $this->teacherId = $subject->teacher_id;
        $this->isElective = $subject->is_elective;
        $this->showModal = true;
    }

    public function save(): void
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'code' => [
                'nullable', 'string', 'max:20',
                Rule::unique('subjects')
                    ->where('school_id', $schoolId)
                    ->where('school_class_id', $this->schoolClassId)
                    ->ignore($this->subjectId),
            ],
            'schoolClassId' => 'nullable|exists:school_classes,id',
            'teacherId' => 'nullable|exists:teachers,id',
            'isElective' => 'boolean',
        ]);

        Subject::updateOrCreate(
            ['id' => $this->subjectId, 'school_id' => $schoolId],
            [
                'school_id' => $schoolId,
                'name' => $validated['name'],
                'code' => $validated['code'] ?: null,
                'school_class_id' => $validated['schoolClassId'] ?: null,
                'teacher_id' => $validated['teacherId'] ?: null,
                'is_elective' => $validated['isElective'],
            ]
        );

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Subject::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['subjectId', 'name', 'code', 'schoolClassId', 'teacherId', 'isElective']);
        $this->resetErrorBag();
    }
}
