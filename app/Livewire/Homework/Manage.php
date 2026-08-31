<?php

namespace App\Livewire\Homework;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithFileUploads;

    public bool $showModal = false;

    public ?int $homeworkId = null;

    public string $title = '';

    public string $description = '';

    public ?int $schoolClassId = null;

    public ?int $subjectId = null;

    public string $dueDate = '';

    public string $maxMarks = '';

    public $attachment;

    public ?string $existingAttachmentPath = null;

    public bool $showSubmissionsModal = false;

    public ?int $activeHomeworkId = null;

    public array $marksObtained = [];

    public array $feedback = [];

    public function updatedSchoolClassId(): void
    {
        $this->subjectId = null;
    }

    public function render(): View
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        $schoolId = $user->school_id;

        $homeworks = Homework::with(['schoolClass', 'subject'])
            ->withCount('submissions')
            ->where('school_id', $schoolId)
            ->when($teacher, fn ($q) => $q->where('teacher_id', $teacher->id))
            ->orderByDesc('due_date')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get();

        $subjects = $this->schoolClassId
            ? Subject::where('school_class_id', $this->schoolClassId)
                ->when($teacher, fn ($q) => $q->where('teacher_id', $teacher->id))
                ->orderBy('name')
                ->get()
            : collect();

        $activeHomework = $this->activeHomeworkId
            ? Homework::with('schoolClass.students.user')
                ->where('school_id', $schoolId)
                ->when($teacher, fn ($q) => $q->where('teacher_id', $teacher->id))
                ->find($this->activeHomeworkId)
            : null;

        $submissions = $activeHomework
            ? HomeworkSubmission::where('homework_id', $activeHomework->id)->get()->keyBy('student_id')
            : collect();

        return view('livewire.homework.manage', [
            'homeworks' => $homeworks,
            'classes' => $classes,
            'subjects' => $subjects,
            'activeHomework' => $activeHomework,
            'submissions' => $submissions,
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $homework = $this->baseQuery()->findOrFail($id);

        $this->homeworkId = $homework->id;
        $this->title = $homework->title;
        $this->description = (string) $homework->description;
        $this->schoolClassId = $homework->school_class_id;
        $this->subjectId = $homework->subject_id;
        $this->dueDate = $homework->due_date->format('Y-m-d');
        $this->maxMarks = $homework->max_marks !== null ? (string) $homework->max_marks : '';
        $this->existingAttachmentPath = $homework->attachment_path;
        $this->showModal = true;
    }

    public function removeAttachment(): void
    {
        if ($this->homeworkId && $this->existingAttachmentPath) {
            Storage::disk('public')->delete($this->existingAttachmentPath);
            $this->baseQuery()->whereKey($this->homeworkId)->update(['attachment_path' => null]);
        }

        $this->existingAttachmentPath = null;
        $this->attachment = null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'schoolClassId' => 'required|exists:school_classes,id',
            'subjectId' => 'required|exists:subjects,id',
            'dueDate' => 'required|date',
            'maxMarks' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $user = auth()->user();
        $teacher = $user->teacher;

        $subject = Subject::where('school_class_id', $validated['schoolClassId'])
            ->when($teacher, fn ($q) => $q->where('teacher_id', $teacher->id))
            ->findOrFail($validated['subjectId']);

        $attachmentPath = $this->existingAttachmentPath;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('homework-attachments', 'public');
        }

        Homework::updateOrCreate(
            ['id' => $this->homeworkId, 'school_id' => $user->school_id],
            [
                'school_id' => $user->school_id,
                'school_class_id' => $validated['schoolClassId'],
                'subject_id' => $subject->id,
                'teacher_id' => $subject->teacher_id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?: null,
                'due_date' => $validated['dueDate'],
                'max_marks' => $validated['maxMarks'] !== '' ? $validated['maxMarks'] : null,
                'attachment_path' => $attachmentPath,
            ]
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $homework = $this->baseQuery()->findOrFail($id);

        if ($homework->attachment_path) {
            Storage::disk('public')->delete($homework->attachment_path);
        }

        foreach ($homework->submissions as $submission) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        $homework->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openSubmissions(int $id): void
    {
        $homework = $this->baseQuery()->with('schoolClass.students')->findOrFail($id);
        $this->activeHomeworkId = $homework->id;

        $existing = HomeworkSubmission::where('homework_id', $homework->id)->get()->keyBy('student_id');

        $this->marksObtained = [];
        $this->feedback = [];
        foreach ($homework->schoolClass->students as $student) {
            $record = $existing->get($student->id);
            $this->marksObtained[$student->id] = $record?->marks_obtained !== null ? (string) $record->marks_obtained : '';
            $this->feedback[$student->id] = $record->feedback ?? '';
        }

        $this->showSubmissionsModal = true;
    }

    public function saveGrades(): void
    {
        $homework = $this->baseQuery()->findOrFail($this->activeHomeworkId);

        $rules = [];
        foreach (array_keys($this->marksObtained) as $studentId) {
            $rules["marksObtained.{$studentId}"] = $homework->max_marks !== null
                ? "nullable|numeric|min:0|max:{$homework->max_marks}"
                : 'nullable|numeric|min:0';
        }
        $this->validate($rules);

        $allowedStudentIds = $homework->schoolClass->students()->pluck('students.id');

        foreach ($this->marksObtained as $studentId => $marks) {
            if (! $allowedStudentIds->contains((int) $studentId)) {
                continue;
            }

            HomeworkSubmission::updateOrCreate(
                ['homework_id' => $homework->id, 'student_id' => $studentId],
                [
                    'marks_obtained' => $marks !== '' ? $marks : null,
                    'feedback' => $this->feedback[$studentId] ?: null,
                    'graded_by' => $marks !== '' ? auth()->id() : null,
                    'graded_at' => $marks !== '' ? now() : null,
                ]
            );
        }

        $this->closeSubmissionsModal();
    }

    public function closeSubmissionsModal(): void
    {
        $this->showSubmissionsModal = false;
        $this->activeHomeworkId = null;
        $this->marksObtained = [];
        $this->feedback = [];
    }

    private function baseQuery()
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        return Homework::where('school_id', $user->school_id)
            ->when($teacher, fn ($q) => $q->where('teacher_id', $teacher->id));
    }

    private function resetForm(): void
    {
        $this->reset(['homeworkId', 'title', 'description', 'schoolClassId', 'subjectId', 'dueDate', 'maxMarks', 'attachment', 'existingAttachmentPath']);
        $this->resetErrorBag();
    }
}
