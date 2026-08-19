<?php

namespace App\Livewire\SchoolAdmin\Exams;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    public bool $showModal = false;

    public ?int $examId = null;

    public string $name = '';

    public ?int $schoolClassId = null;

    public string $term = '';

    public string $startDate = '';

    public string $endDate = '';

    public bool $showSubjectsModal = false;

    public ?int $activeExamId = null;

    public array $subjectRows = [];

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        return view('livewire.school-admin.exams.manage', [
            'exams' => Exam::with('schoolClass')
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get(),
            'classes' => SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get(),
            'activeExam' => $this->activeExamId ? Exam::with('schoolClass')->find($this->activeExamId) : null,
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $exam = Exam::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->examId = $exam->id;
        $this->name = $exam->name;
        $this->schoolClassId = $exam->school_class_id;
        $this->term = $exam->term ?? '';
        $this->startDate = $exam->start_date?->format('Y-m-d') ?? '';
        $this->endDate = $exam->end_date?->format('Y-m-d') ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:150',
            'schoolClassId' => 'required|exists:school_classes,id',
            'term' => 'nullable|string|max:100',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ]);

        $schoolId = auth()->user()->school_id;

        Exam::updateOrCreate(
            ['id' => $this->examId, 'school_id' => $schoolId],
            [
                'school_id' => $schoolId,
                'name' => $validated['name'],
                'school_class_id' => $validated['schoolClassId'],
                'term' => $validated['term'] ?: null,
                'start_date' => $validated['startDate'] ?: null,
                'end_date' => $validated['endDate'] ?: null,
            ]
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Exam::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openSubjects(int $examId): void
    {
        $exam = Exam::where('school_id', auth()->user()->school_id)->findOrFail($examId);
        $this->activeExamId = $exam->id;

        $subjects = Subject::where('school_class_id', $exam->school_class_id)->orderBy('name')->get();
        $existing = $exam->examSubjects()->get()->keyBy('subject_id');

        $this->subjectRows = [];
        foreach ($subjects as $subject) {
            $row = $existing->get($subject->id);
            $this->subjectRows[$subject->id] = [
                'name' => $subject->name,
                'included' => (bool) $row,
                'max_marks' => $row ? (string) $row->max_marks : '100',
                'pass_marks' => $row ? (string) $row->pass_marks : '40',
            ];
        }

        $this->showSubjectsModal = true;
    }

    public function saveSubjects(): void
    {
        $exam = Exam::where('school_id', auth()->user()->school_id)->findOrFail($this->activeExamId);

        foreach ($this->subjectRows as $subjectId => $row) {
            if (! $row['included']) {
                ExamSubject::where('exam_id', $exam->id)->where('subject_id', $subjectId)->delete();

                continue;
            }

            ExamSubject::updateOrCreate(
                ['exam_id' => $exam->id, 'subject_id' => $subjectId],
                [
                    'max_marks' => is_numeric($row['max_marks']) ? $row['max_marks'] : 100,
                    'pass_marks' => is_numeric($row['pass_marks']) ? $row['pass_marks'] : 40,
                ]
            );
        }

        $this->closeSubjectsModal();
    }

    public function closeSubjectsModal(): void
    {
        $this->showSubjectsModal = false;
        $this->activeExamId = null;
        $this->subjectRows = [];
    }

    private function resetForm(): void
    {
        $this->reset(['examId', 'name', 'schoolClassId', 'term', 'startDate', 'endDate']);
        $this->resetErrorBag();
    }
}
