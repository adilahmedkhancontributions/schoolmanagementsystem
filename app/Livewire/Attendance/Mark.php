<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Mark extends Component
{
    public ?int $sectionId = null;

    public string $date;

    public array $status = [];

    public array $remarks = [];

    public bool $saved = false;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function updatedSectionId(): void
    {
        $this->loadAttendance();
    }

    public function updatedDate(): void
    {
        $this->loadAttendance();
    }

    public function render(): View
    {
        $sections = $this->availableSections()->with('schoolClass')->orderBy('school_class_id')->orderBy('name')->get();

        $students = $this->sectionId
            ? $this->availableSections()->findOrFail($this->sectionId)->students()->with('user')->orderBy('admission_number')->get()
            : collect();

        return view('livewire.attendance.mark', [
            'sections' => $sections,
            'students' => $students,
        ]);
    }

    private function availableSections()
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        return Section::whereHas('schoolClass', fn ($q) => $q->where('school_id', $user->school_id))
            ->when($teacher, fn ($q) => $q->where('teacher_id', $teacher->id));
    }

    public function loadAttendance(): void
    {
        $this->saved = false;
        $this->status = [];
        $this->remarks = [];

        if (! $this->sectionId) {
            return;
        }

        $students = $this->availableSections()->findOrFail($this->sectionId)->students;

        $existing = Attendance::where('section_id', $this->sectionId)
            ->where('date', $this->date)
            ->get()
            ->keyBy('student_id');

        foreach ($students as $student) {
            $record = $existing->get($student->id);
            $this->status[$student->id] = $record->status ?? 'present';
            $this->remarks[$student->id] = $record->remarks ?? '';
        }
    }

    public function markAll(string $status): void
    {
        foreach (array_keys($this->status) as $studentId) {
            $this->status[$studentId] = $status;
        }
    }

    public function setStatus(int $studentId, string $status): void
    {
        $this->status[$studentId] = $status;
    }

    public function save(): void
    {
        $this->validate([
            'sectionId' => 'required|exists:sections,id',
            'date' => 'required|date',
        ]);

        $section = $this->availableSections()->findOrFail($this->sectionId);

        foreach ($this->status as $studentId => $status) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $this->date],
                [
                    'school_id' => $section->schoolClass->school_id,
                    'section_id' => $section->id,
                    'status' => $status,
                    'remarks' => $this->remarks[$studentId] ?: null,
                    'marked_by' => auth()->id(),
                ]
            );
        }

        $this->saved = true;
    }
}
