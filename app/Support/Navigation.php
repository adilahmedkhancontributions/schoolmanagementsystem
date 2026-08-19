<?php

namespace App\Support;

class Navigation
{
    /**
     * Returns the sidebar/bottom-nav items for a role. Items without a
     * `route` name that exists yet are rendered as disabled "coming soon"
     * links so the information architecture is visible while modules are
     * built out in later phases.
     */
    public static function forRole(string $role): array
    {
        $shared = [
            ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'dashboard'],
        ];

        $byRole = [
            'super_admin' => [
                ['label' => 'Schools', 'icon' => 'fa-school', 'route' => 'super-admin.schools'],
                ['label' => 'Admins', 'icon' => 'fa-user-shield', 'route' => null],
                ['label' => 'System Settings', 'icon' => 'fa-gears', 'route' => null],
                ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'route' => null],
            ],
            'school_admin' => [
                ['label' => 'Students', 'icon' => 'fa-user-graduate', 'route' => 'school-admin.students'],
                ['label' => 'Teachers', 'icon' => 'fa-chalkboard-user', 'route' => 'school-admin.teachers'],
                ['label' => 'Classes', 'icon' => 'fa-layer-group', 'route' => 'school-admin.classes'],
                ['label' => 'Subjects', 'icon' => 'fa-book', 'route' => 'school-admin.subjects'],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'school-admin.attendance'],
                ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => null],
                ['label' => 'Exams', 'icon' => 'fa-file-pen', 'route' => null],
                ['label' => 'CMS', 'icon' => 'fa-newspaper', 'route' => null],
                ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => null],
                ['label' => 'School Profile', 'icon' => 'fa-palette', 'route' => 'school-admin.settings'],
            ],
            'teacher' => [
                ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => null],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'teacher.attendance'],
                ['label' => 'Grades', 'icon' => 'fa-file-pen', 'route' => null],
                ['label' => 'Messages', 'icon' => 'fa-comments', 'route' => null],
            ],
            'student' => [
                ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => null],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'student.attendance'],
                ['label' => 'Grades', 'icon' => 'fa-graduation-cap', 'route' => null],
                ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => null],
                ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => null],
            ],
            'parent' => [
                ['label' => 'My Children', 'icon' => 'fa-child-reaching', 'route' => null],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'parent.attendance'],
                ['label' => 'Performance', 'icon' => 'fa-graduation-cap', 'route' => null],
                ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => null],
                ['label' => 'Messages', 'icon' => 'fa-comments', 'route' => null],
            ],
        ];

        return array_merge($shared, $byRole[$role] ?? []);
    }

    public static function roleLabel(string $role): string
    {
        return match ($role) {
            'super_admin' => 'Super Admin',
            'school_admin' => 'School Admin',
            'teacher' => 'Teacher',
            'student' => 'Student',
            'parent' => 'Parent',
            default => ucfirst($role),
        };
    }
}
