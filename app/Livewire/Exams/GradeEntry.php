<?php

namespace App\Livewire\Exams;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamSubject;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class GradeEntry extends Component
{
    public ?int $examId = null;

    public ?int $examSubjectId = null;

    public array $marks = [];

    public array $remarks = [];

    public bool $saved = false;

    public function updatedExamId(): void
    {
        $this->examSubjectId = null;
        $this->marks = [];
        $this->remarks = [];
        $this->saved = false;
    }

    public function updatedExamSubjectId(): void
    {
        $this->loadResults();
    }

    public function render(): View
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        $schoolId = $user->school_id;

        $exams = Exam::with('schoolClass')
            ->where('school_id', $schoolId)
            ->when($teacher, fn ($q) => $q->whereHas('examSubjects.subject', fn ($q2) => $q2->where('teacher_id', $teacher->id)))
            ->orderByDesc('id')
            ->get();

        $examSubjects = collect();
        if ($this->examId) {
            $exam = $exams->firstWhere('id', $this->examId);
            if ($exam) {
                $examSubjects = $exam->examSubjects()
                    ->with('subject')
                    ->when($teacher, fn ($q) => $q->whereHas('subject', fn ($q2) => $q2->where('teacher_id', $teacher->id)))
                    ->get();
            }
        }

        $activeExamSubject = $this->examSubjectId ? $examSubjects->firstWhere('id', $this->examSubjectId) : null;

        $students = $activeExamSubject
            ? $activeExamSubject->exam->schoolClass->students()->with('user')->orderBy('admission_number')->get()
            : collect();

        return view('livewire.exams.grade-entry', [
            'exams' => $exams,
            'examSubjects' => $examSubjects,
            'activeExamSubject' => $activeExamSubject,
            'students' => $students,
        ]);
    }

    private function loadResults(): void
    {
        $this->saved = false;
        $this->marks = [];
        $this->remarks = [];

        if (! $this->examSubjectId) {
            return;
        }

        $examSubject = ExamSubject::with('exam.schoolClass.students')->find($this->examSubjectId);

        if (! $examSubject) {
            return;
        }

        $existing = ExamResult::where('exam_subject_id', $examSubject->id)->get()->keyBy('student_id');

        foreach ($examSubject->exam->schoolClass->students as $student) {
            $record = $existing->get($student->id);
            $this->marks[$student->id] = $record?->marks_obtained !== null ? (string) $record->marks_obtained : '';
            $this->remarks[$student->id] = $record->remarks ?? '';
        }
    }

    public function save(): void
    {
        $examSubject = ExamSubject::findOrFail($this->examSubjectId);

        $rules = [];
        foreach (array_keys($this->marks) as $studentId) {
            $rules["marks.{$studentId}"] = "nullable|numeric|min:0|max:{$examSubject->max_marks}";
        }
        $this->validate($rules);

        foreach ($this->marks as $studentId => $marksObtained) {
            ExamResult::updateOrCreate(
                ['exam_subject_id' => $examSubject->id, 'student_id' => $studentId],
                [
                    'marks_obtained' => $marksObtained !== '' ? $marksObtained : null,
                    'remarks' => $this->remarks[$studentId] ?: null,
                    'entered_by' => auth()->id(),
                ]
            );
        }

        $this->saved = true;
    }
}
