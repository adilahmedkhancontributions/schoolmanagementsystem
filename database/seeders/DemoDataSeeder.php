<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\FeeInvoice;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Creates one demo user per role so every dashboard can be tested.
     * Password for all seeded users: "password".
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            ['name' => 'Super Admin', 'password' => 'password', 'status' => 'active']
        );
        $superAdmin->assignRole('super_admin');

        $school = School::firstOrCreate(
            ['code' => 'DEMO01'],
            [
                'name' => 'Demo Public School',
                'slug' => 'demo-public-school',
                'email' => 'info@demoschool.test',
                'timezone' => 'UTC',
                'currency' => 'USD',
                'academic_year' => now()->year.'-'.(now()->year + 1),
                'status' => 'active',
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@demoschool.test'],
            ['name' => 'Demo School Admin', 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
        );
        $admin->assignRole('school_admin');

        $class = SchoolClass::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Grade 5'],
            ['sort_order' => 5]
        );

        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@demoschool.test'],
            ['name' => 'Demo Teacher', 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
        );
        $teacherUser->assignRole('teacher');
        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            ['school_id' => $school->id, 'employee_id' => 'EMP-0001', 'employment_type' => 'full_time', 'joining_date' => now()]
        );

        $section = Section::firstOrCreate(
            ['school_class_id' => $class->id, 'name' => 'A'],
            ['teacher_id' => $teacher->id, 'capacity' => 40]
        );

        $subject = Subject::firstOrCreate(
            ['school_id' => $school->id, 'school_class_id' => $class->id, 'code' => 'MATH5'],
            ['name' => 'Mathematics', 'teacher_id' => $teacher->id]
        );

        $studentUser = User::firstOrCreate(
            ['email' => 'student@demoschool.test'],
            ['name' => 'Demo Student', 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
        );
        $studentUser->assignRole('student');
        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'school_id' => $school->id,
                'school_class_id' => $class->id,
                'section_id' => $section->id,
                'admission_number' => 'ADM-0001',
                'admission_date' => now(),
                'gender' => 'male',
                'status' => 'active',
            ]
        );

        $parentUser = User::firstOrCreate(
            ['email' => 'parent@demoschool.test'],
            ['name' => 'Demo Parent', 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
        );
        $parentUser->assignRole('parent');
        $guardian = Guardian::firstOrCreate(['user_id' => $parentUser->id]);
        $guardian->students()->syncWithoutDetaching([
            $student->id => ['relationship' => 'father', 'is_primary' => true],
        ]);

        $tuitionFee = FeeStructure::firstOrCreate(
            ['school_id' => $school->id, 'school_class_id' => $class->id, 'name' => 'Monthly Tuition Fee'],
            ['amount' => 100, 'frequency' => 'monthly']
        );

        FeeInvoice::firstOrCreate(
            ['school_id' => $school->id, 'student_id' => $student->id, 'fee_structure_id' => $tuitionFee->id, 'title' => 'Monthly Tuition Fee'],
            ['amount' => $tuitionFee->amount, 'due_date' => now()->addDays(15), 'status' => 'unpaid']
        );

        $exam = Exam::firstOrCreate(
            ['school_id' => $school->id, 'school_class_id' => $class->id, 'name' => 'Mid Term Exam'],
            ['term' => 'Term 1', 'start_date' => now()->subDays(5), 'end_date' => now()->subDays(1)]
        );

        ExamSubject::firstOrCreate(
            ['exam_id' => $exam->id, 'subject_id' => $subject->id],
            ['max_marks' => 100, 'pass_marks' => 40]
        );

        Announcement::firstOrCreate(
            ['school_id' => $school->id, 'title' => 'Welcome to the new term'],
            [
                'body' => 'We are excited to welcome everyone back for the new academic term. Please check your dashboard regularly for updates.',
                'audience' => 'everyone',
                'published_at' => now(),
                'created_by' => $admin->id,
            ]
        );
    }
}
