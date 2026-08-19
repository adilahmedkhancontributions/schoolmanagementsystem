<?php

namespace App\Livewire\SchoolAdmin\Classes;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    public string $search = '';

    public bool $showClassModal = false;

    public ?int $classId = null;

    public string $className = '';

    public int $sortOrder = 0;

    public bool $showSectionModal = false;

    public ?int $sectionId = null;

    public ?int $sectionClassId = null;

    public string $sectionName = '';

    public int $sectionCapacity = 40;

    public ?int $sectionTeacherId = null;

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $classes = SchoolClass::withCount('students')
            ->with(['sections.classTeacher.user'])
            ->where('school_id', $schoolId)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.school-admin.classes.manage', [
            'classes' => $classes,
            'teachers' => Teacher::where('school_id', $schoolId)->with('user')->get(),
        ]);
    }

    public function closeClassModal(): void
    {
        $this->showClassModal = false;
        $this->resetClassForm();
    }

    public function closeSectionModal(): void
    {
        $this->showSectionModal = false;
        $this->resetSectionForm();
    }

    public function openCreateClass(): void
    {
        $this->resetClassForm();
        $this->showClassModal = true;
    }

    public function openEditClass(int $id): void
    {
        $class = SchoolClass::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $this->classId = $class->id;
        $this->className = $class->name;
        $this->sortOrder = $class->sort_order;
        $this->showClassModal = true;
    }

    public function saveClass(): void
    {
        $validated = $this->validate([
            'className' => 'required|string|max:100',
            'sortOrder' => 'nullable|integer|min:0|max:255',
        ]);

        $schoolId = auth()->user()->school_id;

        SchoolClass::updateOrCreate(
            ['id' => $this->classId, 'school_id' => $schoolId],
            ['name' => $validated['className'], 'sort_order' => $validated['sortOrder'] ?? 0, 'school_id' => $schoolId]
        );

        $this->showClassModal = false;
        $this->resetClassForm();
    }

    public function deleteClass(int $id): void
    {
        SchoolClass::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    private function resetClassForm(): void
    {
        $this->reset(['classId', 'className', 'sortOrder']);
        $this->resetErrorBag();
    }

    public function openCreateSection(int $classId): void
    {
        $this->resetSectionForm();
        $this->sectionClassId = $classId;
        $this->showSectionModal = true;
    }

    public function openEditSection(int $id): void
    {
        $section = Section::whereHas('schoolClass', fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->findOrFail($id);

        $this->sectionId = $section->id;
        $this->sectionClassId = $section->school_class_id;
        $this->sectionName = $section->name;
        $this->sectionCapacity = $section->capacity;
        $this->sectionTeacherId = $section->teacher_id;
        $this->showSectionModal = true;
    }

    public function saveSection(): void
    {
        $validated = $this->validate([
            'sectionClassId' => 'required|exists:school_classes,id',
            'sectionName' => 'required|string|max:50',
            'sectionCapacity' => 'required|integer|min:1|max:200',
            'sectionTeacherId' => 'nullable|exists:teachers,id',
        ]);

        Section::updateOrCreate(
            ['id' => $this->sectionId, 'school_class_id' => $validated['sectionClassId']],
            [
                'name' => $validated['sectionName'],
                'capacity' => $validated['sectionCapacity'],
                'teacher_id' => $validated['sectionTeacherId'] ?: null,
            ]
        );

        $this->showSectionModal = false;
        $this->resetSectionForm();
    }

    public function deleteSection(int $id): void
    {
        Section::whereHas('schoolClass', fn ($q) => $q->where('school_id', auth()->user()->school_id))
            ->findOrFail($id)
            ->delete();
    }

    private function resetSectionForm(): void
    {
        $this->reset(['sectionId', 'sectionClassId', 'sectionName', 'sectionCapacity', 'sectionTeacherId']);
        $this->sectionCapacity = 40;
        $this->resetErrorBag();
    }
}
