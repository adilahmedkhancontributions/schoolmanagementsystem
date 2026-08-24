<?php

namespace App\Livewire\Homework;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class MyHomework extends Component
{
    use WithFileUploads;

    public ?int $studentId = null;

    public bool $showSubmitModal = false;

    public ?int $activeHomeworkId = null;

    public string $submissionText = '';

    public $submissionFile;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->hasRole('parent')) {
            $this->studentId = $user->guardianProfile?->students()->first()?->id;
        } elseif ($user->hasRole('student')) {
            $this->studentId = $user->student?->id;
        }
    }

    public function render(): View
    {
        $user = auth()->user();

        $children = $user->hasRole('parent')
            ? $user->guardianProfile?->students()->with('user')->get() ?? collect()
            : collect();

        $allowedIds = $user->hasRole('parent')
            ? $children->pluck('id')
            : collect([$user->student?->id]);

        if (! $allowedIds->contains($this->studentId)) {
            $this->studentId = null;
        }

        $homeworks = collect();
        $submissions = collect();
        $activeHomework = null;

        if ($this->studentId) {
            $student = Student::findOrFail($this->studentId);

            $homeworks = Homework::with('subject')
                ->where('school_class_id', $student->school_class_id)
                ->orderByDesc('due_date')
                ->get();

            $submissions = HomeworkSubmission::where('student_id', $student->id)
                ->whereIn('homework_id', $homeworks->pluck('id'))
                ->get()
                ->keyBy('homework_id');

            $activeHomework = $this->activeHomeworkId ? $homeworks->firstWhere('id', $this->activeHomeworkId) : null;
        }

        return view('livewire.homework.my-homework', [
            'children' => $children,
            'homeworks' => $homeworks,
            'submissions' => $submissions,
            'activeHomework' => $activeHomework,
        ]);
    }

    public function openSubmit(int $homeworkId): void
    {
        $this->assertOwnsStudent();

        $existing = HomeworkSubmission::where('homework_id', $homeworkId)
            ->where('student_id', $this->studentId)
            ->first();

        $this->activeHomeworkId = $homeworkId;
        $this->submissionText = $existing?->submission_text ?? '';
        $this->submissionFile = null;
        $this->showSubmitModal = true;
    }

    public function closeSubmitModal(): void
    {
        $this->showSubmitModal = false;
        $this->activeHomeworkId = null;
        $this->submissionText = '';
        $this->submissionFile = null;
        $this->resetErrorBag();
    }

    public function submit(): void
    {
        $this->assertOwnsStudent();

        $validated = $this->validate([
            'submissionText' => 'nullable|string|max:2000',
            'submissionFile' => 'nullable|file|max:10240',
        ]);

        $homework = Homework::where('school_class_id', Student::findOrFail($this->studentId)->school_class_id)
            ->findOrFail($this->activeHomeworkId);

        $existing = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', $this->studentId)
            ->first();

        $filePath = $existing?->file_path;
        if ($this->submissionFile) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $this->submissionFile->store('homework-submissions', 'public');
        }

        HomeworkSubmission::updateOrCreate(
            ['homework_id' => $homework->id, 'student_id' => $this->studentId],
            [
                'submission_text' => $validated['submissionText'] ?: null,
                'file_path' => $filePath,
                'submitted_at' => now(),
            ]
        );

        $this->closeSubmitModal();
    }

    private function assertOwnsStudent(): void
    {
        $user = auth()->user();

        $allowedIds = $user->hasRole('parent')
            ? ($user->guardianProfile?->students()->pluck('students.id') ?? collect())
            : collect([$user->student?->id]);

        abort_unless($allowedIds->contains($this->studentId), 403);
    }
}
