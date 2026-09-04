<?php

namespace App\Livewire\SchoolAdmin\Announcements;

use App\Models\Announcement;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $announcementId = null;

    public string $title = '';

    public string $body = '';

    public string $audience = 'everyone';

    public ?int $schoolClassId = null;

    public bool $publishNow = true;

    public string $publishDate = '';

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        return view('livewire.school-admin.announcements.manage', [
            'announcements' => Announcement::with(['schoolClass', 'author'])
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->paginate(10),
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
        $announcement = Announcement::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->announcementId = $announcement->id;
        $this->title = $announcement->title;
        $this->body = $announcement->body;
        $this->audience = $announcement->audience;
        $this->schoolClassId = $announcement->school_class_id;
        $this->publishNow = $announcement->published_at !== null && $announcement->published_at->lessThanOrEqualTo(now());
        $this->publishDate = $announcement->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:5000',
            'audience' => 'required|in:everyone,teachers,students,parents',
            'schoolClassId' => ['nullable', Rule::exists('school_classes', 'id')->where('school_id', $schoolId)],
            'publishDate' => 'nullable|date',
        ]);

        Announcement::updateOrCreate(
            ['id' => $this->announcementId, 'school_id' => $schoolId],
            [
                'school_id' => $schoolId,
                'title' => $validated['title'],
                'body' => $validated['body'],
                'audience' => $validated['audience'],
                'school_class_id' => $validated['schoolClassId'] ?: null,
                'published_at' => $this->publishNow ? now() : ($validated['publishDate'] ?: null),
                'created_by' => auth()->id(),
            ]
        );

        if ($this->publishNow) {
            $roleMap = [
                'everyone' => ['teacher', 'student', 'parent'],
                'teachers' => ['teacher'],
                'students' => ['student'],
                'parents' => ['parent'],
            ];
            $roles = $roleMap[$validated['audience']] ?? [];

            if ($validated['schoolClassId']) {
                $class = SchoolClass::with('students.user.guardianProfile')->find($validated['schoolClassId']);
                $recipients = collect();
                if ($class) {
                    $studentUsers = $class->students->pluck('user')->filter()->values();
                    $guardianUsers = $class->students->flatMap(fn ($s) => $s->guardians ?? [])->pluck('user')->filter()->values();
                    if (in_array('teacher', $roles)) {
                        $teacherUsers = User::role('teacher')->where('school_id', $schoolId)->get();
                        $recipients = $recipients->merge($teacherUsers);
                    }
                    $recipients = $recipients->merge($studentUsers)->merge($guardianUsers);
                }
            } else {
                $recipients = User::role($roles)->where('school_id', $schoolId)->get();
            }

            $recipients = $recipients->unique('id')->values();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new \App\Notifications\AnnouncementPublished(
                    $validated['title'],
                    $validated['body'],
                    auth()->user()->name
                ));
            }
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Announcement::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['announcementId', 'title', 'body', 'schoolClassId', 'publishDate']);
        $this->audience = 'everyone';
        $this->publishNow = true;
        $this->resetErrorBag();
    }
}
