<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage schools',
            'manage school admins',
            'manage system settings',
            'view global analytics',
            'manage teachers',
            'manage students',
            'manage classes',
            'manage subjects',
            'manage fees',
            'manage exams',
            'manage cms',
            'manage announcements',
            'mark attendance',
            'manage grades',
            'manage staff',
            'mark staff attendance',
            'manage documents',
            'view timetable',
            'view own attendance',
            'view own grades',
            'view own fees',
            'manage own children',
            'send messages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'super_admin' => $permissions,
            'school_admin' => [
                'manage teachers', 'manage students', 'manage classes', 'manage subjects',
                'manage fees', 'manage exams', 'manage cms', 'manage announcements',
                'view timetable', 'send messages', 'mark attendance', 'manage staff',
                'mark staff attendance', 'manage documents',
            ],
            'teacher' => [
                'mark attendance', 'manage grades', 'view timetable', 'send messages', 'view own attendance', 'manage documents',
            ],
            'student' => [
                'view timetable', 'view own attendance', 'view own grades', 'view own fees', 'manage documents',
            ],
            'parent' => [
                'manage own children', 'view own attendance', 'view own grades', 'view own fees', 'send messages', 'manage documents',
            ],
            'staff' => [
                'view own attendance', 'manage documents',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
