<?php

namespace App\Support;

class Navigation
{
    /**
     * Returns the sidebar navigation for a role, grouped into labelled,
     * collapsible sections. Each group: ['label' => string, 'icon' => string, 'items' => [ [label, icon, route] ] ].
     * Items without a route name that exists yet are rendered as disabled
     * "coming soon" links.
     */
    public static function forRole(string $role): array
    {
        $groups = match ($role) {
            'super_admin' => [
                [
                    'label' => 'Overview',
                    'icon' => 'fa-circle',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'dashboard'],
                        ['label' => 'Schools', 'icon' => 'fa-school', 'route' => 'super-admin.schools'],
                    ],
                ],
                [
                    'label' => 'Platform',
                    'icon' => 'fa-toolbox',
                    'items' => [
                        ['label' => 'Admins', 'icon' => 'fa-user-shield', 'route' => null],
                        ['label' => 'System Settings', 'icon' => 'fa-gears', 'route' => null],
                        ['label' => 'Global Analytics', 'icon' => 'fa-chart-line', 'route' => null],
                    ],
                ],
            ],
            'school_admin' => [
                [
                    'label' => 'Overview',
                    'icon' => 'fa-gauge-high',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'dashboard'],
                    ],
                ],
                [
                    'label' => 'Academic',
                    'icon' => 'fa-graduation-cap',
                    'items' => [
                        ['label' => 'Admissions', 'icon' => 'fa-user-plus', 'route' => 'school-admin.admissions'],
                        ['label' => 'Students', 'icon' => 'fa-user-graduate', 'route' => 'school-admin.students'],
                        ['label' => 'Teachers', 'icon' => 'fa-chalkboard-user', 'route' => 'school-admin.teachers'],
                        ['label' => 'Staff', 'icon' => 'fa-id-badge', 'route' => 'school-admin.staff'],
                        ['label' => 'Classes', 'icon' => 'fa-layer-group', 'route' => 'school-admin.classes'],
                        ['label' => 'Subjects', 'icon' => 'fa-book', 'route' => 'school-admin.subjects'],
                        ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => 'school-admin.timetable.manage'],
                    ],
                ],
                [
                    'label' => 'Operations',
                    'icon' => 'fa-clipboard-check',
                    'items' => [
                        ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'school-admin.attendance'],
                        ['label' => 'Staff Attendance', 'icon' => 'fa-clipboard-user', 'route' => 'school-admin.staff.attendance'],
                        ['label' => 'Exams', 'icon' => 'fa-file-pen', 'route' => 'school-admin.exams'],
                        ['label' => 'Homework', 'icon' => 'fa-book-open', 'route' => 'school-admin.homework'],
                        ['label' => 'Leave Requests', 'icon' => 'fa-calendar-minus', 'route' => 'school-admin.leave'],
                    ],
                ],
                [
                    'label' => 'Finance',
                    'icon' => 'fa-sack-dollar',
                    'items' => [
                        ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'school-admin.fees.invoices'],
                        ['label' => 'Payroll', 'icon' => 'fa-money-check-dollar', 'route' => 'school-admin.finance.payroll'],
                    ],
                ],
                [
                    'label' => 'People & Content',
                    'icon' => 'fa-comments',
                    'items' => [
                        ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'school-admin.announcements'],
                        ['label' => 'CMS', 'icon' => 'fa-newspaper', 'route' => 'school-admin.cms.pages'],
                        ['label' => 'Reports', 'icon' => 'fa-chart-pie', 'route' => 'school-admin.reports.attendance'],
                    ],
                ],
                [
                    'label' => 'System',
                    'icon' => 'fa-gear',
                    'items' => [
                        ['label' => 'Data Import/Export', 'icon' => 'fa-file-arrow-up', 'route' => 'school-admin.data-tools'],
                        ['label' => 'Campuses', 'icon' => 'fa-building', 'route' => 'school-admin.campuses'],
                        ['label' => 'School Profile', 'icon' => 'fa-palette', 'route' => 'school-admin.settings'],
                    ],
                ],
            ],
            'teacher' => [
                [
                    'label' => 'Overview',
                    'icon' => 'fa-gauge-high',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'dashboard'],
                    ],
                ],
                [
                    'label' => 'Teaching',
                    'icon' => 'fa-chalkboard-user',
                    'items' => [
                        ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => 'teacher.timetable'],
                        ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'teacher.attendance'],
                        ['label' => 'Grades', 'icon' => 'fa-file-pen', 'route' => 'teacher.exams.grades'],
                        ['label' => 'Homework', 'icon' => 'fa-book-open', 'route' => 'teacher.homework'],
                    ],
                ],
                [
                    'label' => 'Personal',
                    'icon' => 'fa-user',
                    'items' => [
                        ['label' => 'My Attendance', 'icon' => 'fa-clipboard-user', 'route' => 'teacher.staff-attendance'],
                        ['label' => 'Leave', 'icon' => 'fa-calendar-minus', 'route' => 'teacher.leave'],
                        ['label' => 'My Pay', 'icon' => 'fa-money-check-dollar', 'route' => 'teacher.payroll'],
                    ],
                ],
                [
                    'label' => 'Communication',
                    'icon' => 'fa-comments',
                    'items' => [
                        ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'teacher.announcements'],
                        ['label' => 'Messages', 'icon' => 'fa-comments', 'route' => 'teacher.messages'],
                    ],
                ],
            ],
            'student' => [
                [
                    'label' => 'Overview',
                    'icon' => 'fa-gauge-high',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'dashboard'],
                    ],
                ],
                [
                    'label' => 'Academic',
                    'icon' => 'fa-graduation-cap',
                    'items' => [
                        ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => 'student.timetable'],
                        ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'student.attendance'],
                        ['label' => 'Grades', 'icon' => 'fa-graduation-cap', 'route' => 'student.exams'],
                        ['label' => 'Homework', 'icon' => 'fa-book-open', 'route' => 'student.homework'],
                    ],
                ],
                [
                    'label' => 'Financial',
                    'icon' => 'fa-sack-dollar',
                    'items' => [
                        ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'student.fees'],
                    ],
                ],
                [
                    'label' => 'Communication',
                    'icon' => 'fa-comments',
                    'items' => [
                        ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'student.announcements'],
                    ],
                ],
            ],
            'parent' => [
                [
                    'label' => 'Overview',
                    'icon' => 'fa-gauge-high',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'dashboard'],
                        ['label' => 'My Children', 'icon' => 'fa-child-reaching', 'route' => null],
                    ],
                ],
                [
                    'label' => 'Academic',
                    'icon' => 'fa-graduation-cap',
                    'items' => [
                        ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'parent.attendance'],
                        ['label' => 'Performance', 'icon' => 'fa-graduation-cap', 'route' => 'parent.exams'],
                        ['label' => 'Homework', 'icon' => 'fa-book-open', 'route' => 'parent.homework'],
                    ],
                ],
                [
                    'label' => 'Financial',
                    'icon' => 'fa-sack-dollar',
                    'items' => [
                        ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'parent.fees'],
                    ],
                ],
                [
                    'label' => 'Communication',
                    'icon' => 'fa-comments',
                    'items' => [
                        ['label' => 'Leave', 'icon' => 'fa-calendar-minus', 'route' => 'parent.leave'],
                        ['label' => 'Announcements', 'icon' => 'fa-bullhorn', 'route' => 'parent.announcements'],
                        ['label' => 'Messages', 'icon' => 'fa-comments', 'route' => 'parent.messages'],
                    ],
                ],
            ],
            'staff' => [
                [
                    'label' => 'Overview',
                    'icon' => 'fa-gauge-high',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'dashboard'],
                        ['label' => 'My Attendance', 'icon' => 'fa-clipboard-user', 'route' => 'staff.attendance'],
                        ['label' => 'My Pay', 'icon' => 'fa-money-check-dollar', 'route' => 'staff.payroll'],
                    ],
                ],
            ],
            default => [],
        };

        return $groups;
    }

    /**
     * Flattens the grouped nav into a simple list (used by the mobile bottom bar).
     */
    public static function flattened(string $role): array
    {
        $items = [];

        foreach (self::forRole($role) as $group) {
            foreach ($group['items'] as $item) {
                $items[] = $item;
            }
        }

        return $items;
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
