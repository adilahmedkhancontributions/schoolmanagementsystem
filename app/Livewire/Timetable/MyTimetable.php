<?php

namespace App\Livewire\Timetable;

use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class MyTimetable extends Component
{
    public function render(): View
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        $periods = TimetableSlot::where('school_id', $schoolId)->orderBy('sort_order')->orderBy('start_time')->get();

        $entries = collect();

        if ($user->hasRole('teacher') && $user->teacher) {
            $entries = TimetableEntry::where('teacher_id', $user->teacher->id)
                ->with(['subject', 'section.schoolClass'])
                ->get();
        } elseif ($user->hasRole('student') && $user->student?->section_id) {
            $entries = TimetableEntry::where('section_id', $user->student->section_id)
                ->with(['subject', 'teacher.user'])
                ->get();
        }

        $grid = [];
        foreach ($entries as $entry) {
            $grid[$entry->timetable_slot_id][$entry->day_of_week] = $entry;
        }

        return view('livewire.timetable.my-timetable', [
            'periods' => $periods,
            'grid' => $grid,
            'days' => TimetableEntry::DAYS,
            'isTeacher' => $user->hasRole('teacher'),
        ]);
    }
}
