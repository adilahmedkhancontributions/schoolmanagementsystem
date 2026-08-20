<?php

namespace App\Livewire\SchoolAdmin\Timetable;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use App\Support\TimetableNotifier;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    public ?int $schoolClassId = null;

    public ?int $sectionId = null;

    public function mount(): void
    {
        $this->schoolClassId = $this->availableClasses()->orderBy('sort_order')->value('id');
        $this->sectionId = $this->schoolClassId
            ? $this->availableSections()->where('school_class_id', $this->schoolClassId)->orderBy('name')->value('id')
            : null;
    }

    public function updatedSchoolClassId(): void
    {
        $this->sectionId = $this->availableSections()->where('school_class_id', $this->schoolClassId)->orderBy('name')->value('id');
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $section = $this->sectionId ? $this->availableSections()->find($this->sectionId) : null;
        $this->sectionId = $section?->id;

        $periods = TimetableSlot::where('school_id', $schoolId)->orderBy('sort_order')->orderBy('start_time')->get();

        $subjects = $this->schoolClassId
            ? Subject::where('school_id', $schoolId)
                ->where(fn ($q) => $q->where('school_class_id', $this->schoolClassId)->orWhereNull('school_class_id'))
                ->with('teacher.user')
                ->orderBy('name')
                ->get()
            : collect();

        $grid = [];
        if ($section) {
            $entries = TimetableEntry::where('section_id', $section->id)->with('subject')->get();
            foreach ($entries as $entry) {
                $grid[$entry->timetable_slot_id][$entry->day_of_week] = $entry;
            }
        }

        return view('livewire.school-admin.timetable.manage', [
            'classes' => $this->availableClasses()->orderBy('sort_order')->get(),
            'sections' => $this->schoolClassId
                ? $this->availableSections()->where('school_class_id', $this->schoolClassId)->orderBy('name')->get()
                : collect(),
            'periods' => $periods,
            'subjects' => $subjects,
            'grid' => $grid,
            'days' => TimetableEntry::DAYS,
        ]);
    }

    public function assign(int $slotId, int $day, string $subjectId): void
    {
        $schoolId = auth()->user()->school_id;
        $section = $this->sectionId ? $this->availableSections()->find($this->sectionId) : null;

        if (! $section) {
            return;
        }

        $slot = TimetableSlot::where('school_id', $schoolId)->findOrFail($slotId);
        $changedBy = auth()->user()->name.' (School Admin)';

        $existing = TimetableEntry::where('section_id', $section->id)
            ->where('timetable_slot_id', $slotId)
            ->where('day_of_week', $day)
            ->with(['subject', 'teacher.user'])
            ->first();

        if ($subjectId === '') {
            if ($existing) {
                TimetableNotifier::notify(
                    $section,
                    $existing->teacher,
                    'Period removed from timetable',
                    TimetableNotifier::sectionLabel($section).' — '.$existing->subject->name.' on '.TimetableNotifier::describe($slot, $day).' has been removed from the timetable.',
                    $changedBy
                );
                $existing->delete();
            }

            return;
        }

        $subject = Subject::where('school_id', $schoolId)->with('teacher')->findOrFail((int) $subjectId);

        TimetableEntry::updateOrCreate(
            ['section_id' => $section->id, 'timetable_slot_id' => $slot->id, 'day_of_week' => $day],
            [
                'school_id' => $schoolId,
                'subject_id' => $subject->id,
                'teacher_id' => $subject->teacher_id,
            ]
        );

        if ($existing && $existing->subject_id !== $subject->id) {
            $scheduleLabel = TimetableNotifier::describe($slot, $day);

            TimetableNotifier::notify(
                $section,
                $subject->teacher,
                'Timetable updated',
                TimetableNotifier::sectionLabel($section).' — '.$scheduleLabel.' changed from '.$existing->subject->name.' to '.$subject->name.'.',
                $changedBy
            );

            if ($existing->teacher_id && $existing->teacher_id !== $subject->teacher_id) {
                TimetableNotifier::notifyUser(
                    $existing->teacher?->user,
                    'You are no longer scheduled for this period',
                    'You are no longer teaching '.$existing->subject->name.' for '.TimetableNotifier::sectionLabel($section).' on '.$scheduleLabel.'.',
                    $changedBy
                );
            }
        }
    }

    private function availableClasses()
    {
        return SchoolClass::where('school_id', auth()->user()->school_id);
    }

    private function availableSections()
    {
        return Section::whereHas('schoolClass', fn ($q) => $q->where('school_id', auth()->user()->school_id));
    }
}
