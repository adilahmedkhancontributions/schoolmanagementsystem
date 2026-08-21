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
                ['label' => 'Staff', 'icon' => 'fa-id-badge', 'route' => 'school-admin.staff'],
                ['label' => 'Classes', 'icon' => 'fa-layer-group', 'route' => 'school-admin.classes'],
                ['label' => 'Subjects', 'icon' => 'fa-book', 'route' => 'school-admin.subjects'],
                ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => 'school-admin.timetable.manage'],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'school-admin.attendance'],
                ['label' => 'Staff Attendance', 'icon' => 'fa-clipboard-user', 'route' => 'school-admin.staff.attendance'],
                ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'school-admin.fees.invoices'],
                ['label' => 'Exams', 'icon' => 'fa-file-pen', 'route' => 'school-admin.exams'],
                ['label' => 'Reports', 'icon' => 'fa-chart-pie', 'route' => 'school-admin.reports.attendance'],
                ['label' => 'CMS', 'icon' => 'fa-newspaper', 'route' => 'school-admin.cms.pages'],
                ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'school-admin.announcements'],
                ['label' => 'School Profile', 'icon' => 'fa-palette', 'route' => 'school-admin.settings'],
            ],
            'teacher' => [
                ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => 'teacher.timetable'],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'teacher.attendance'],
                ['label' => 'My Attendance', 'icon' => 'fa-clipboard-user', 'route' => 'teacher.staff-attendance'],
                ['label' => 'Grades', 'icon' => 'fa-file-pen', 'route' => 'teacher.exams.grades'],
                ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'teacher.announcements'],
                ['label' => 'Messages', 'icon' => 'fa-comments', 'route' => 'teacher.messages'],
            ],
            'student' => [
                ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => 'student.timetable'],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'student.attendance'],
                ['label' => 'Grades', 'icon' => 'fa-graduation-cap', 'route' => 'student.exams'],
                ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'student.fees'],
                ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'student.announcements'],
            ],
            'parent' => [
                ['label' => 'My Children', 'icon' => 'fa-child-reaching', 'route' => null],
                ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'parent.attendance'],
                ['label' => 'Performance', 'icon' => 'fa-graduation-cap', 'route' => 'parent.exams'],
                ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'parent.fees'],
                ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'parent.announcements'],
                ['label' => 'Messages', 'icon' => 'fa-comments', 'route' => 'parent.messages'],
            ],
            'staff' => [
                ['label' => 'My Attendance', 'icon' => 'fa-clipboard-user', 'route' => 'staff.attendance'],
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
            'staff' => 'Staff',
            default => ucfirst($role),
        };
    }
}
