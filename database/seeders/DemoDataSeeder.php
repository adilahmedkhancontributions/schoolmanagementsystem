<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\CmsPage;
use App\Models\CmsPost;
use App\Models\ContactMessage;
use App\Models\Conversation;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\FeeInvoice;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Message;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
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
                'hero_headline' => 'A place where every student thrives',
                'hero_subheadline' => 'Quality education, caring teachers, and a vibrant community.',
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

        CmsPage::firstOrCreate(
            ['school_id' => $school->id, 'slug' => 'about'],
            [
                'title' => 'About Us',
                'content' => '<p>Demo Public School has been serving our community for over 20 years, providing a nurturing environment where students grow academically, socially and emotionally.</p><p>Our dedicated teachers and staff are committed to helping every child reach their full potential.</p>',
                'meta_description' => 'Learn about Demo Public School, our mission and our community.',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        CmsPage::firstOrCreate(
            ['school_id' => $school->id, 'slug' => 'admissions'],
            [
                'title' => 'Admissions',
                'content' => '<p>We welcome new students throughout the academic year, subject to availability.</p><ul><li>Complete the enquiry form on our contact page</li><li>Schedule a campus visit</li><li>Submit required documents</li></ul>',
                'meta_description' => 'Find out how to apply for admission to Demo Public School.',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        CmsPost::firstOrCreate(
            ['school_id' => $school->id, 'slug' => 'welcome-back-to-a-new-academic-year'],
            [
                'author_id' => $admin->id,
                'title' => 'Welcome Back to a New Academic Year',
                'excerpt' => 'We kicked off the new term with orientation activities for all grades.',
                'content' => '<p>Students and staff returned this week for the start of a new academic year. Orientation activities were held across all grades, and we are looking forward to a great year ahead.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ]
        );

        ContactMessage::firstOrCreate(
            ['school_id' => $school->id, 'email' => 'prospective.parent@example.com'],
            [
                'name' => 'Prospective Parent',
                'phone' => '+1 555 0100',
                'message' => 'Hi, I would like to know more about the admissions process for Grade 1. Thank you!',
                'is_read' => false,
            ]
        );

        $conversation = Conversation::firstOrCreate(
            ['teacher_id' => $teacher->id, 'guardian_id' => $guardian->id, 'student_id' => $student->id],
            ['school_id' => $school->id, 'last_message_at' => now()]
        );

        Message::firstOrCreate(
            ['conversation_id' => $conversation->id, 'sender_id' => $teacherUser->id, 'body' => 'Hello! Just wanted to let you know your child is doing great in Mathematics this term.'],
            ['created_at' => now()->subHours(2)]
        );

        Message::firstOrCreate(
            ['conversation_id' => $conversation->id, 'sender_id' => $parentUser->id, 'body' => "Thank you for the update, that's great to hear!"],
            ['created_at' => now()->subHour(), 'read_at' => now()->subHour()]
        );

        $period1 = TimetableSlot::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Period 1'],
            ['start_time' => '08:00', 'end_time' => '08:45', 'sort_order' => 1]
        );

        $period2 = TimetableSlot::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Period 2'],
            ['start_time' => '08:45', 'end_time' => '09:30', 'sort_order' => 2]
        );

        TimetableEntry::firstOrCreate(
            ['section_id' => $section->id, 'timetable_slot_id' => $period1->id, 'day_of_week' => 1],
            ['school_id' => $school->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id]
        );

        TimetableEntry::firstOrCreate(
            ['section_id' => $section->id, 'timetable_slot_id' => $period2->id, 'day_of_week' => 3],
            ['school_id' => $school->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id]
        );
    }
}
