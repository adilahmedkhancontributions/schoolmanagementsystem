<?php

namespace App\Support;

use App\Models\Section;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use App\Notifications\TimetableChanged;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class TimetableNotifier
{
    public static function describe(TimetableSlot $slot, int $day): string
    {
        $dayName = TimetableEntry::DAYS[$day] ?? '';

        return "{$dayName}, {$slot->name} ({$slot->start_time->format('h:i A')}–{$slot->end_time->format('h:i A')})";
    }

    public static function sectionLabel(Section $section): string
    {
        return $section->schoolClass->name.' - '.$section->name;
    }

    public static function notify(Section $section, ?Teacher $teacher, string $title, string $message, string $changedBy): void
    {
        $recipients = self::recipientsFor($section, $teacher);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new TimetableChanged($title, $message, $changedBy));
    }

    public static function notifyUser($user, string $title, string $message, string $changedBy): void
    {
        if (! $user) {
            return;
        }

        $user->notify(new TimetableChanged($title, $message, $changedBy));
    }

    private static function recipientsFor(Section $section, ?Teacher $teacher): Collection
    {
        $users = collect();

        if ($teacher?->user) {
            $users->push($teacher->user);
        }

        $students = $section->students()->with(['user', 'guardians.user'])->get();

        foreach ($students as $student) {
            if ($student->user) {
                $users->push($student->user);
            }

            foreach ($student->guardians as $guardian) {
                if ($guardian->user) {
                    $users->push($guardian->user);
                }
            }
        }

        return $users->filter()->unique('id')->values();
    }
}
