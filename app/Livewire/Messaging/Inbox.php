<?php

namespace App\Livewire\Messaging;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Inbox extends Component
{
    public ?int $conversationId = null;

    public string $body = '';

    public string $newContact = '';

    public bool $showList = true;

    public function mount(): void
    {
        $conversations = $this->conversationsQuery()->orderByDesc('last_message_at')->get();
        $this->conversationId = $conversations->first()?->id;
    }

    public function render(): View
    {
        $user = auth()->user();
        $isTeacher = $user->hasRole('teacher');

        $conversations = $this->conversationsQuery()
            ->with(['teacher.user', 'guardian.user', 'student.user', 'student.schoolClass'])
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->whereNull('read_at')->where('sender_id', '!=', $user->id);
            }])
            ->orderByDesc('last_message_at')
            ->get();

        if (! $conversations->pluck('id')->contains($this->conversationId)) {
            $this->conversationId = $conversations->first()?->id;
        }

        $activeConversation = $this->conversationId
            ? $conversations->firstWhere('id', $this->conversationId)
            : null;

        $messages = collect();

        if ($activeConversation) {
            $messages = Message::where('conversation_id', $activeConversation->id)
                ->with('sender')
                ->orderBy('created_at')
                ->get();

            Message::where('conversation_id', $activeConversation->id)
                ->whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->update(['read_at' => now()]);

            $activeConversation->unread_count = 0;
        }

        return view('livewire.messaging.inbox', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'contacts' => $this->contactOptions($isTeacher),
            'isTeacher' => $isTeacher,
        ]);
    }

    public function selectConversation(int $id): void
    {
        $this->conversationId = $id;
        $this->showList = false;
    }

    public function backToList(): void
    {
        $this->showList = true;
    }

    public function startConversation(): void
    {
        if ($this->newContact === '') {
            return;
        }

        [$contactId, $studentId] = array_map('intval', explode('-', $this->newContact));
        $user = auth()->user();
        $schoolId = $user->school_id;

        if ($user->hasRole('teacher')) {
            $teacherId = $user->teacher->id;
            $guardianId = $contactId;
        } else {
            $guardianId = $user->guardianProfile->id;
            $teacherId = $contactId;
        }

        $allowed = $this->contactOptions($user->hasRole('teacher'))
            ->contains(fn ($option) => $option['contactId'] === $contactId && $option['studentId'] === $studentId);

        if (! $allowed) {
            return;
        }

        $conversation = Conversation::firstOrCreate([
            'teacher_id' => $teacherId,
            'guardian_id' => $guardianId,
            'student_id' => $studentId,
        ], [
            'school_id' => $schoolId,
        ]);

        $this->conversationId = $conversation->id;
        $this->showList = false;
        $this->newContact = '';
    }

    public function send(): void
    {
        $this->validate(['body' => 'required|string|max:2000']);

        $user = auth()->user();
        $conversation = $this->conversationsQuery()->where('id', $this->conversationId)->first();

        if (! $conversation) {
            return;
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $this->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $this->reset('body');
    }

    private function conversationsQuery()
    {
        $user = auth()->user();

        return Conversation::query()
            ->when($user->hasRole('teacher'), fn ($q) => $q->where('teacher_id', $user->teacher?->id ?? 0))
            ->when($user->hasRole('parent'), fn ($q) => $q->where('guardian_id', $user->guardianProfile?->id ?? 0));
    }

    private function contactOptions(bool $isTeacher): Collection
    {
        $user = auth()->user();

        if ($isTeacher) {
            $teacher = $user->teacher;
            if (! $teacher) {
                return collect();
            }

            $students = Student::where('school_id', $teacher->school_id)
                ->where(function ($q) use ($teacher) {
                    $q->whereHas('section', fn ($q2) => $q2->where('teacher_id', $teacher->id))
                        ->orWhereHas('schoolClass.subjects', fn ($q2) => $q2->where('teacher_id', $teacher->id));
                })
                ->with(['user', 'schoolClass', 'guardians.user'])
                ->get();

            $options = collect();
            foreach ($students as $student) {
                foreach ($student->guardians as $guardian) {
                    $options->push([
                        'contactId' => $guardian->id,
                        'studentId' => $student->id,
                        'value' => $guardian->id.'-'.$student->id,
                        'label' => $guardian->user->name.' — parent of '.$student->user->name.($student->schoolClass ? ' ('.$student->schoolClass->name.')' : ''),
                    ]);
                }
            }

            return $options->unique('value')->sortBy('label')->values();
        }

        $guardian = $user->guardianProfile;
        if (! $guardian) {
            return collect();
        }

        $children = $guardian->students()->with(['schoolClass.subjects.teacher.user', 'section.classTeacher.user'])->get();

        $options = collect();
        foreach ($children as $student) {
            $teachers = collect();

            if ($student->section?->classTeacher) {
                $teachers->push($student->section->classTeacher);
            }

            foreach ($student->schoolClass?->subjects ?? [] as $subject) {
                if ($subject->teacher) {
                    $teachers->push($subject->teacher);
                }
            }

            foreach ($teachers->unique('id') as $teacher) {
                $options->push([
                    'contactId' => $teacher->id,
                    'studentId' => $student->id,
                    'value' => $teacher->id.'-'.$student->id,
                    'label' => $teacher->user->name.' — teacher of '.$student->user->name,
                ]);
            }
        }

        return $options->unique('value')->sortBy('label')->values();
    }
}
