<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Campus;
use App\Models\CmsPage;
use App\Models\CmsPost;
use App\Models\ContactMessage;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamSubject;
use App\Models\FeeDiscount;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\GalleryImage;
use App\Models\Guardian;
use App\Models\LeaveRequest;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Message;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimetableChangeRequest;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Creates one demo user per role plus a full sample roster so every
     * dashboard and report can be tested with realistic data.
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

        // ---- Campuses ----
        $campus = Campus::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Main Campus'],
            [
                'code' => 'MC',
                'address' => '123 School Road',
                'city' => 'Demo City',
                'is_default' => true,
                'status' => 'active',
            ]
        );

        // ---- Classes & sections ----
        $classes = collect();
        foreach (['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'] as $i => $name) {
            $classes[$name] = SchoolClass::firstOrCreate(
                ['school_id' => $school->id, 'name' => $name],
                ['sort_order' => $i + 1, 'campus_id' => $campus->id]
            );
        }
        $class = $classes['Grade 5'];

        $sections = collect();
        foreach ($classes as $className => $schoolClass) {
            foreach (['A', 'B'] as $sectionName) {
                $sections[$className.' '.$sectionName] = Section::firstOrCreate(
                    ['school_class_id' => $schoolClass->id, 'name' => $sectionName],
                    ['capacity' => 40]
                );
            }
        }
        $section = $sections['Grade 5 A'];

        // ---- Teachers ----
        $teacherNames = [
            'Demo Teacher', 'Sarah Johnson', 'Michael Chen', 'Emily Davis',
            'Robert Wilson', 'Linda Martinez', 'James Anderson',
        ];
        $teachers = collect();
        foreach ($teacherNames as $i => $name) {
            $email = $i === 0 ? 'teacher@demoschool.test' : strtolower(str_replace(' ', '.', $name)).'@demoschool.test';
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
            );
            $user->assignRole('teacher');
            $teachers[$name] = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'school_id' => $school->id,
                    'campus_id' => $campus->id,
                    'employee_id' => sprintf('EMP-%04d', $i + 1),
                    'employment_type' => 'full_time',
                    'joining_date' => now()->subYears(random_int(1, 10)),
                    'qualification' => 'B.Ed, M.Ed',
                    'specialization' => 'General Education',
                ]
            );
        }
        $teacherUser = User::where('email', 'teacher@demoschool.test')->first();
        $teacher = $teachers['Demo Teacher'];
        $section->update(['teacher_id' => $teacher->id]);

        $otherSections = $sections->except('Grade 5 A')->values();
        $teacherPool = $teachers->except('Demo Teacher')->values();
        foreach ($otherSections as $i => $sec) {
            $sec->update(['teacher_id' => $teacherPool[$i % $teacherPool->count()]->id]);
        }

        // ---- Subjects ----
        $subjectDefs = [
            'Mathematics' => 'MATH',
            'English' => 'ENG',
            'Science' => 'SCI',
            'Social Studies' => 'SST',
            'Computer Science' => 'CS',
        ];
        $subjects = collect();
        $teacherList = $teachers->values();
        $t = 0;
        foreach ($classes as $className => $schoolClass) {
            $gradeNumber = (int) preg_replace('/\D/', '', $className);
            foreach ($subjectDefs as $subjectName => $codePrefix) {
                $subjects[$className.'-'.$subjectName] = Subject::firstOrCreate(
                    ['school_id' => $school->id, 'school_class_id' => $schoolClass->id, 'code' => $codePrefix.$gradeNumber],
                    ['name' => $subjectName, 'teacher_id' => $teacherList[$t % $teacherList->count()]->id]
                );
                $t++;
            }
        }
        $subject = $subjects['Grade 5-Mathematics'];

        // ---- Staff ----
        $staffDefs = [
            ['name' => 'Demo Staff', 'email' => 'staff@demoschool.test', 'designation' => 'Accountant', 'department' => 'Finance'],
            ['name' => 'Nancy Green', 'email' => 'nancy.green@demoschool.test', 'designation' => 'Librarian', 'department' => 'Library'],
            ['name' => 'Peter Baker', 'email' => 'peter.baker@demoschool.test', 'designation' => 'Receptionist', 'department' => 'Administration'],
            ['name' => 'Diana Lee', 'email' => 'diana.lee@demoschool.test', 'designation' => 'IT Support', 'department' => 'IT'],
        ];
        $staffMembers = collect();
        foreach ($staffDefs as $i => $def) {
            $user = User::firstOrCreate(
                ['email' => $def['email']],
                ['name' => $def['name'], 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
            );
            $user->assignRole('staff');
            $staffMembers[$def['name']] = Staff::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'school_id' => $school->id,
                    'campus_id' => $campus->id,
                    'employee_id' => sprintf('STF-%04d', $i + 1),
                    'designation' => $def['designation'],
                    'department' => $def['department'],
                    'employment_type' => 'full_time',
                    'joining_date' => now()->subYears(random_int(1, 6)),
                ]
            );
        }

        // ---- Students & guardians ----
        $firstNames = ['Aiden', 'Ava', 'Liam', 'Mia', 'Noah', 'Emma', 'Lucas', 'Sophia', 'Ethan', 'Isabella', 'Mason', 'Amelia'];
        $lastNames = ['Khan', 'Smith', 'Garcia', 'Brown', 'Lee', 'Ahmed', 'Taylor', 'Moore', 'Clark', 'Hussain'];
        $students = collect();
        $guardians = collect();
        $counter = 1;
        foreach ($sections as $sectionKey => $sec) {
            for ($i = 0; $i < 5; $i++) {
                $isDemoSlot = $sectionKey === 'Grade 5 A' && $i === 0;
                $studentEmail = $isDemoSlot ? 'student@demoschool.test' : sprintf('student%03d@demoschool.test', $counter);
                $studentName = $isDemoSlot ? 'Demo Student' : $firstNames[($counter * 3) % count($firstNames)].' '.$lastNames[($counter * 7) % count($lastNames)];

                $studentUser = User::firstOrCreate(
                    ['email' => $studentEmail],
                    ['name' => $studentName, 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
                );
                $studentUser->assignRole('student');

                $guardianEmail = $isDemoSlot ? 'parent@demoschool.test' : sprintf('guardian%03d@demoschool.test', $counter);
                $guardianName = $isDemoSlot ? 'Demo Parent' : $lastNames[($counter * 5) % count($lastNames)].' Family';

                $studentModel = Student::firstOrCreate(
                    ['user_id' => $studentUser->id],
                    [
                        'school_id' => $school->id,
                        'campus_id' => $campus->id,
                        'school_class_id' => $sec->school_class_id,
                        'section_id' => $sec->id,
                        'admission_number' => sprintf('ADM-%04d', $counter),
                        'admission_date' => now()->subMonths(random_int(1, 24)),
                        'date_of_birth' => now()->subYears(random_int(6, 15))->subDays(random_int(0, 300)),
                        'gender' => $counter % 2 === 0 ? 'female' : 'male',
                        'blood_group' => ['A+', 'B+', 'O+', 'AB-'][$counter % 4],
                        'nationality' => 'United States',
                        'religion' => '—',
                        'emergency_contact_name' => $guardianName,
                        'emergency_contact_phone' => '+1 555 010'.$counter % 10,
                        'emergency_contact_relation' => 'Parent',
                        'medical_notes' => $counter % 5 === 0 ? 'Allergic to peanuts.' : null,
                        'status' => 'active',
                    ]
                );
                $students[$sectionKey.'-'.$i] = $studentModel;

                $guardianUser = User::firstOrCreate(
                    ['email' => $guardianEmail],
                    ['name' => $guardianName, 'password' => 'password', 'school_id' => $school->id, 'status' => 'active']
                );
                $guardianUser->assignRole('parent');
                $guardianModel = Guardian::firstOrCreate(['user_id' => $guardianUser->id], ['occupation' => 'Business Owner']);
                $guardianModel->students()->syncWithoutDetaching([
                    $studentModel->id => ['relationship' => 'father', 'is_primary' => true],
                ]);
                $guardians[$sectionKey.'-'.$i] = $guardianModel;

                $counter++;
            }
        }
        $student = $students['Grade 5 A-0'];
        $studentUser = User::where('email', 'student@demoschool.test')->first();
        $parentUser = User::where('email', 'parent@demoschool.test')->first();
        $guardian = $guardians['Grade 5 A-0'];

        // ---- Admissions ----
        $admissionDefs = [
            ['name' => 'Ayesha Khan', 'status' => Admission::STATUS_INTERVIEW_SCHEDULED],
            ['name' => 'Bilal Ahmed', 'status' => Admission::STATUS_INQUIRY],
            ['name' => 'Zara Malik', 'status' => Admission::STATUS_TEST_SCHEDULED],
            ['name' => 'Hamza Iqbal', 'status' => Admission::STATUS_OFFERED],
            ['name' => 'Sana Riaz', 'status' => Admission::STATUS_ENROLLED],
            ['name' => 'Umer Farooq', 'status' => Admission::STATUS_REJECTED],
            ['name' => 'Fatima Noor', 'status' => Admission::STATUS_WITHDRAWN],
        ];
        foreach ($admissionDefs as $i => $def) {
            Admission::firstOrCreate(
                ['school_id' => $school->id, 'applicant_name' => $def['name']],
                [
                    'father_name' => $lastNames[$i % count($lastNames)].' Sr.',
                    'phone' => '+1 555 01'.sprintf('%02d', $i),
                    'email' => strtolower(str_replace(' ', '.', $def['name'])).'@example.com',
                    'gender' => $i % 2 === 0 ? 'female' : 'male',
                    'date_of_birth' => now()->subYears(10),
                    'school_class_id' => $class->id,
                    'source' => 'Website',
                    'status' => $def['status'],
                    'interview_date' => now()->addDays(3),
                    'created_by' => $admin->id,
                ]
            );
        }

        // ---- Fee structures, invoices, payments, discounts ----
        $feeStructures = collect();
        foreach ($classes as $className => $schoolClass) {
            $feeStructures[$className.'-tuition'] = FeeStructure::firstOrCreate(
                ['school_id' => $school->id, 'school_class_id' => $schoolClass->id, 'name' => 'Monthly Tuition Fee'],
                ['amount' => 100, 'frequency' => 'monthly']
            );
            $feeStructures[$className.'-exam'] = FeeStructure::firstOrCreate(
                ['school_id' => $school->id, 'school_class_id' => $schoolClass->id, 'name' => 'Term Exam Fee'],
                ['amount' => 50, 'frequency' => 'term']
            );
        }
        $tuitionFee = $feeStructures['Grade 5-tuition'];

        $paymentMethods = ['cash', 'bank_transfer', 'cheque', 'online'];
        $invoiceIndex = 0;
        foreach ($students as $key => $studentModel) {
            $className = explode(' ', $key)[0].' '.explode(' ', $key)[1];
            $tuition = $feeStructures[$className.'-tuition'];

            $invoice = FeeInvoice::firstOrCreate(
                ['school_id' => $school->id, 'student_id' => $studentModel->id, 'fee_structure_id' => $tuition->id, 'title' => 'Monthly Tuition Fee - '.now()->format('F Y')],
                ['amount' => $tuition->amount, 'due_date' => now()->addDays(15), 'status' => 'unpaid']
            );

            if ($invoiceIndex % 3 !== 2) {
                FeePayment::firstOrCreate(
                    ['fee_invoice_id' => $invoice->id, 'amount' => $tuition->amount, 'method' => $paymentMethods[$invoiceIndex % count($paymentMethods)]],
                    ['paid_at' => now()->subDays(random_int(1, 10)), 'recorded_by' => $admin->id]
                );
                $invoice->paid_amount = $tuition->amount;
                $invoice->refreshStatus();
            }

            if ($invoiceIndex % 7 === 0) {
                FeeDiscount::firstOrCreate(
                    ['fee_invoice_id' => $invoice->id, 'type' => 'sibling'],
                    ['is_percentage' => true, 'value' => 10, 'notes' => 'Sibling discount', 'created_by' => $admin->id]
                );
            }

            $invoiceIndex++;
        }

        // keep the demo student's invoice unpaid so the parent dashboard has an outstanding balance
        $demoInvoiceIds = FeeInvoice::where('student_id', $student->id)->where('title', 'like', 'Monthly Tuition Fee%')->pluck('id');
        FeePayment::whereIn('fee_invoice_id', $demoInvoiceIds)->delete();
        FeeInvoice::whereIn('id', $demoInvoiceIds)->update(['status' => 'unpaid', 'paid_amount' => 0]);

        // ---- Exams, exam subjects & results ----
        foreach ($classes as $className => $schoolClass) {
            $classSubjects = $subjects->filter(fn ($s, $k) => str_starts_with($k, $className.'-'))->values();

            $midTerm = Exam::firstOrCreate(
                ['school_id' => $school->id, 'school_class_id' => $schoolClass->id, 'name' => 'Mid Term Exam'],
                ['term' => 'Term 1', 'start_date' => now()->subDays(5), 'end_date' => now()->subDays(1)]
            );
            Exam::firstOrCreate(
                ['school_id' => $school->id, 'school_class_id' => $schoolClass->id, 'name' => 'Final Term Exam'],
                ['term' => 'Term 2', 'start_date' => now()->addMonths(3), 'end_date' => now()->addMonths(3)->addDays(4)]
            );

            $examSubjects = collect();
            foreach ($classSubjects as $classSubject) {
                $examSubjects[] = ExamSubject::firstOrCreate(
                    ['exam_id' => $midTerm->id, 'subject_id' => $classSubject->id],
                    ['max_marks' => 100, 'pass_marks' => 40]
                );
            }

            $classSections = $sections->filter(fn ($s, $k) => str_starts_with($k, $className.' '))->values();
            $classStudents = $students->filter(function ($s) use ($classSections) {
                return $classSections->contains(fn ($sec) => $sec->id === $s->section_id);
            })->values();

            foreach ($classStudents as $classStudent) {
                foreach ($examSubjects as $examSubject) {
                    ExamResult::firstOrCreate(
                        ['exam_subject_id' => $examSubject->id, 'student_id' => $classStudent->id],
                        ['marks_obtained' => random_int(35, 100), 'entered_by' => $teacherUser->id]
                    );
                }
            }
        }

        // ---- Homework & submissions ----
        foreach ($classes as $className => $schoolClass) {
            $classSubjects = $subjects->filter(fn ($s, $k) => str_starts_with($k, $className.'-'))->values();
            $classSections = $sections->filter(fn ($s, $k) => str_starts_with($k, $className.' '))->values();
            $classStudents = $students->filter(function ($s) use ($classSections) {
                return $classSections->contains(fn ($sec) => $sec->id === $s->section_id);
            })->values();

            foreach ($classSubjects->take(2) as $index => $classSubject) {
                $homework = Homework::firstOrCreate(
                    ['school_id' => $school->id, 'school_class_id' => $schoolClass->id, 'subject_id' => $classSubject->id, 'title' => $classSubject->name.' Homework '.($index + 1)],
                    [
                        'teacher_id' => $classSubject->teacher_id,
                        'description' => 'Complete the assigned exercises from '.$classSubject->name.'.',
                        'due_date' => now()->addDays(5 + $index),
                        'max_marks' => 20,
                    ]
                );

                foreach ($classStudents->take(3) as $submittingStudent) {
                    HomeworkSubmission::firstOrCreate(
                        ['homework_id' => $homework->id, 'student_id' => $submittingStudent->id],
                        ['submission_text' => 'Attached my answers.', 'submitted_at' => now()->subDay()]
                    );
                }
            }
        }

        // ---- Attendance ----
        foreach ($sections as $sectionKey => $sec) {
            $sectionStudents = $students->filter(fn ($s) => $s->section_id === $sec->id)->values();
            $statuses = ['present', 'present', 'present', 'present', 'late', 'absent', 'half_day', 'leave'];

            for ($dayOffset = 0; $dayOffset < 10; $dayOffset++) {
                $date = now()->subDays($dayOffset);
                if ($date->isWeekend()) {
                    continue;
                }

                foreach ($sectionStudents as $studentIndex => $sectionStudent) {
                    Attendance::firstOrCreate(
                        ['student_id' => $sectionStudent->id, 'date' => $date->format('Y-m-d')],
                        [
                            'school_id' => $school->id,
                            'section_id' => $sec->id,
                            'status' => $statuses[($studentIndex + $dayOffset) % count($statuses)],
                            'marked_by' => $teacherUser->id,
                        ]
                    );
                }
            }
        }

        // ---- Staff attendance ----
        foreach (User::role('staff')->get() as $staffUserRecord) {
            for ($dayOffset = 0; $dayOffset < 10; $dayOffset++) {
                $date = now()->subDays($dayOffset);
                if ($date->isWeekend()) {
                    continue;
                }

                StaffAttendance::firstOrCreate(
                    ['user_id' => $staffUserRecord->id, 'date' => $date->format('Y-m-d')],
                    [
                        'school_id' => $school->id,
                        'status' => $dayOffset % 9 === 0 ? 'absent' : 'present',
                        'marked_by' => $admin->id,
                    ]
                );
            }
        }

        // ---- Announcements ----
        $announcementDefs = [
            ['title' => 'Welcome to the new term', 'audience' => 'everyone', 'body' => 'We are excited to welcome everyone back for the new academic term. Please check your dashboard regularly for updates.'],
            ['title' => 'Parent-Teacher Meeting Scheduled', 'audience' => 'parents', 'body' => 'A parent-teacher meeting will be held next Friday to discuss student progress. Please book your slot in advance.'],
            ['title' => 'Staff Training Day', 'audience' => 'teachers', 'body' => 'All teaching staff are required to attend the professional development workshop this Saturday.'],
            ['title' => 'Annual Sports Day', 'audience' => 'students', 'body' => 'Get ready for the Annual Sports Day! Registrations for track and field events are now open.'],
            ['title' => 'Library Renovation Notice', 'audience' => 'everyone', 'body' => 'The library will be closed for renovation from Monday to Wednesday next week.'],
        ];
        foreach ($announcementDefs as $def) {
            Announcement::firstOrCreate(
                ['school_id' => $school->id, 'title' => $def['title']],
                [
                    'body' => $def['body'],
                    'audience' => $def['audience'],
                    'published_at' => now(),
                    'created_by' => $admin->id,
                ]
            );
        }

        // ---- CMS pages, posts, gallery ----
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

        CmsPage::firstOrCreate(
            ['school_id' => $school->id, 'slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'content' => '<p>Get in touch with us for any questions about admissions, academics, or events.</p>',
                'meta_description' => 'Contact Demo Public School.',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        $postDefs = [
            ['slug' => 'welcome-back-to-a-new-academic-year', 'title' => 'Welcome Back to a New Academic Year', 'excerpt' => 'We kicked off the new term with orientation activities for all grades.', 'content' => '<p>Students and staff returned this week for the start of a new academic year. Orientation activities were held across all grades, and we are looking forward to a great year ahead.</p>', 'daysAgo' => 2],
            ['slug' => 'science-fair-winners-announced', 'title' => 'Science Fair Winners Announced', 'excerpt' => 'Congratulations to all the students who participated in this year\'s science fair.', 'content' => '<p>Our annual science fair showcased incredible projects from students across all grades. Congratulations to all the winners and participants!</p>', 'daysAgo' => 10],
            ['slug' => 'new-computer-lab-opens', 'title' => 'New Computer Lab Opens', 'excerpt' => 'Students now have access to a state-of-the-art computer lab.', 'content' => '<p>We are proud to announce the opening of our new computer lab, equipped with the latest technology to support digital learning.</p>', 'daysAgo' => 20],
            ['slug' => 'sports-day-highlights', 'title' => 'Sports Day Highlights', 'excerpt' => 'A recap of the exciting moments from this year\'s Sports Day.', 'content' => '<p>Students showcased incredible athleticism and team spirit during our Annual Sports Day. Thank you to all the parents and volunteers who made it a success.</p>', 'daysAgo' => 35],
        ];
        foreach ($postDefs as $def) {
            CmsPost::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $def['slug']],
                [
                    'author_id' => $admin->id,
                    'title' => $def['title'],
                    'excerpt' => $def['excerpt'],
                    'content' => $def['content'],
                    'status' => 'published',
                    'published_at' => now()->subDays($def['daysAgo']),
                ]
            );
        }

        for ($i = 1; $i <= 6; $i++) {
            GalleryImage::firstOrCreate(
                ['school_id' => $school->id, 'image' => "gallery/demo-{$i}.jpg"],
                ['caption' => 'Campus life moment '.$i, 'sort_order' => $i]
            );
        }

        // ---- Contact messages ----
        $contactDefs = [
            ['name' => 'Prospective Parent', 'email' => 'prospective.parent@example.com', 'phone' => '+1 555 0100', 'message' => 'Hi, I would like to know more about the admissions process for Grade 1. Thank you!'],
            ['name' => 'John Carter', 'email' => 'john.carter@example.com', 'phone' => '+1 555 0111', 'message' => 'Do you offer transportation services for students living outside the city?'],
            ['name' => 'Maria Lopez', 'email' => 'maria.lopez@example.com', 'phone' => '+1 555 0122', 'message' => 'I would like to schedule a campus tour for next week.'],
            ['name' => 'David Kim', 'email' => 'david.kim@example.com', 'phone' => '+1 555 0133', 'message' => 'What extracurricular activities are available for middle school students?'],
        ];
        foreach ($contactDefs as $def) {
            ContactMessage::firstOrCreate(
                ['school_id' => $school->id, 'email' => $def['email']],
                [
                    'name' => $def['name'],
                    'phone' => $def['phone'],
                    'message' => $def['message'],
                    'is_read' => false,
                ]
            );
        }

        // ---- Conversations & messages ----
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

        foreach (range(0, 2) as $i) {
            $otherStudentKey = 'Grade '.($i + 1).' A-1';
            if (! $students->has($otherStudentKey)) {
                continue;
            }
            $otherStudent = $students[$otherStudentKey];
            $otherGuardian = $guardians[$otherStudentKey];
            $otherTeacher = $teacherPool[$i % $teacherPool->count()];
            $otherTeacherUser = $otherTeacher->user;

            $extraConversation = Conversation::firstOrCreate(
                ['teacher_id' => $otherTeacher->id, 'guardian_id' => $otherGuardian->id, 'student_id' => $otherStudent->id],
                ['school_id' => $school->id, 'last_message_at' => now()->subDays($i)]
            );

            Message::firstOrCreate(
                ['conversation_id' => $extraConversation->id, 'sender_id' => $otherTeacherUser->id, 'body' => 'Please remember to submit the signed permission slip by Friday.'],
                ['created_at' => now()->subDays($i)->subHours(3)]
            );

            Message::firstOrCreate(
                ['conversation_id' => $extraConversation->id, 'sender_id' => $otherGuardian->user_id, 'body' => 'Noted, thank you for the reminder!'],
                ['created_at' => now()->subDays($i)->subHours(2)]
            );
        }

        // ---- Timetable ----
        $slotDefs = [
            ['name' => 'Period 1', 'start' => '08:00', 'end' => '08:45'],
            ['name' => 'Period 2', 'start' => '08:45', 'end' => '09:30'],
            ['name' => 'Period 3', 'start' => '09:45', 'end' => '10:30'],
            ['name' => 'Period 4', 'start' => '10:30', 'end' => '11:15'],
        ];
        $slots = collect();
        foreach ($slotDefs as $i => $def) {
            $slots[$def['name']] = TimetableSlot::firstOrCreate(
                ['school_id' => $school->id, 'name' => $def['name']],
                ['start_time' => $def['start'], 'end_time' => $def['end'], 'sort_order' => $i + 1]
            );
        }

        foreach ($sections as $sectionKey => $sec) {
            $className = explode(' ', $sectionKey)[0].' '.explode(' ', $sectionKey)[1];
            $classSubjects = $subjects->filter(fn ($s, $k) => str_starts_with($k, $className.'-'))->values();

            foreach ($slots->values() as $slotIndex => $slot) {
                $slotSubject = $classSubjects[$slotIndex % $classSubjects->count()];

                TimetableEntry::firstOrCreate(
                    ['section_id' => $sec->id, 'timetable_slot_id' => $slot->id, 'day_of_week' => 1],
                    ['school_id' => $school->id, 'subject_id' => $slotSubject->id, 'teacher_id' => $slotSubject->teacher_id]
                );
            }
        }

        // ---- Timetable change requests ----
        TimetableChangeRequest::firstOrCreate(
            ['school_id' => $school->id, 'teacher_id' => $teacher->id, 'reason' => 'Requesting to swap Period 1 with Period 2 due to a scheduling conflict.'],
            [
                'current_section_id' => $section->id,
                'current_subject_id' => $subject->id,
                'current_timetable_slot_id' => $slots['Period 1']->id,
                'current_day_of_week' => 1,
                'requested_section_id' => $section->id,
                'requested_subject_id' => $subject->id,
                'requested_timetable_slot_id' => $slots['Period 2']->id,
                'requested_day_of_week' => 1,
                'status' => TimetableChangeRequest::STATUS_PENDING,
            ]
        );

        $secondTeacher = $teacherPool[0];
        TimetableChangeRequest::firstOrCreate(
            ['school_id' => $school->id, 'teacher_id' => $secondTeacher->id, 'reason' => 'Requesting to move Wednesday class to Thursday for a field trip.'],
            [
                'current_day_of_week' => 3,
                'requested_day_of_week' => 4,
                'status' => TimetableChangeRequest::STATUS_APPROVED,
                'admin_note' => 'Approved, please inform students in advance.',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now()->subDay(),
            ]
        );

        // ---- Documents ----
        Document::firstOrCreate(
            ['school_id' => $school->id, 'documentable_type' => Student::class, 'documentable_id' => $student->id, 'title' => 'Birth Certificate'],
            ['file_path' => 'documents/demo-birth-certificate.pdf', 'file_type' => 'pdf']
        );
        Document::firstOrCreate(
            ['school_id' => $school->id, 'documentable_type' => Teacher::class, 'documentable_id' => $teacher->id, 'title' => 'Teaching Certification'],
            ['file_path' => 'documents/demo-teaching-certification.pdf', 'file_type' => 'pdf']
        );
        Document::firstOrCreate(
            ['school_id' => $school->id, 'documentable_type' => Staff::class, 'documentable_id' => $staffMembers['Demo Staff']->id, 'title' => 'Employment Contract'],
            ['file_path' => 'documents/demo-employment-contract.pdf', 'file_type' => 'pdf']
        );

        // ---- Leave requests ----
        LeaveRequest::firstOrCreate(
            ['school_id' => $school->id, 'user_id' => $teacherUser->id, 'reason' => 'Family function out of town.'],
            [
                'from_date' => now()->addDays(5),
                'to_date' => now()->addDays(7),
                'status' => LeaveRequest::STATUS_PENDING,
            ]
        );
        LeaveRequest::firstOrCreate(
            ['school_id' => $school->id, 'user_id' => $parentUser->id, 'student_id' => $student->id, 'reason' => 'Annual medical check-up appointment.'],
            [
                'from_date' => now()->addDays(10),
                'to_date' => now()->addDays(10),
                'status' => LeaveRequest::STATUS_PENDING,
            ]
        );
        LeaveRequest::firstOrCreate(
            ['school_id' => $school->id, 'user_id' => $parentUser->id, 'student_id' => $student->id, 'reason' => 'Family wedding.'],
            [
                'from_date' => now()->subDays(15),
                'to_date' => now()->subDays(13),
                'status' => LeaveRequest::STATUS_APPROVED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now()->subDays(14),
            ]
        );
    }
}
