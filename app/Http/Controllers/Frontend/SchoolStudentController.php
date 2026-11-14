<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SchoolMember;
use App\Http\Controllers\Frontend\Concerns\ManagesSchoolMembers;
use App\Services\SchoolMemberProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolStudentController extends Controller
{
    use ManagesSchoolMembers;

    protected function memberRole(): string
    {
        return 'student';
    }

    public function index(): View
    {
        $students = SchoolMember::with('user')
            ->where('school_id', userAuth()->id)
            ->students()
            ->orderByDesc('id')
            ->paginate(15);

        return view('frontend.school-dashboard.students.index', compact('students'));
    }

    public function create(): View
    {
        $grades = ['Nursery', 'LKG', 'UKG', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $sections = ['A', 'B', 'C', 'D', 'E', 'F'];
        $boards = ['CBSE', 'ICSE', 'State Board', 'IB (International Baccalaureate)', 'Cambridge (IGCSE)', 'Other'];
        
        $currentYear = (int) date('Y');
        $academicYears = [
            ($currentYear - 2) . '-' . ($currentYear - 1),
            ($currentYear - 1) . '-' . $currentYear,
            $currentYear . '-' . ($currentYear + 1),
            ($currentYear + 1) . '-' . ($currentYear + 2),
            ($currentYear + 2) . '-' . ($currentYear + 3),
        ];

        return view('frontend.school-dashboard.students.create', compact('grades', 'sections', 'boards', 'academicYears'));
    }

    public function downloadTemplate()
    {
        return $this->csvTemplate(['Name', 'Email', 'Roll_Number', 'Grade', 'Section', 'Academic_Year', 'Board', 'Password'],
            ['John Smith', 'student@example.com', 'STD-101', 'Grade 6', 'A', '2026-2027', 'CBSE', ''], 'students_import_template.csv');
    }

    public function show(SchoolMember $member): View
    {
        if ((int) $member->school_id !== (int) userAuth()->id || $member->role_in_school !== 'student') {
            abort(403);
        }

        $member->load('user');

        // Get all courses assigned to this student by this school
        $assignments = \App\Models\SchoolCourseAssignment::with('course')
            ->where('school_id', userAuth()->id)
            ->where('user_id', $member->user_id)
            ->get();

        foreach ($assignments as $assignment) {
            $course = $assignment->course;
            if ($course) {
                $totalLectures = \App\Models\CourseChapterItem::whereHas('chapter', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->count();

                $watchedCount = \App\Models\CourseProgress::where('user_id', $member->user_id)
                    ->where('course_id', $course->id)
                    ->where('watched', 1)
                    ->count();

                $assignment->total_lectures = $totalLectures;
                $assignment->watched_lectures = $watchedCount;
                $assignment->progress_percent = $totalLectures > 0 ? round(($watchedCount / $totalLectures) * 100) : 0;
            }
        }

        // Get quiz results for this student
        $quizResults = \App\Models\QuizResult::with(['quiz.chapterItem.chapter.course'])
            ->where('user_id', $member->user_id)
            ->orderByDesc('id')
            ->take(20)
            ->get();

        return view('frontend.school-dashboard.students.show', compact('member', 'assignments', 'quizResults'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'password'      => 'nullable|string|min:' . SchoolMemberProvisioner::MIN_PASSWORD . '|max:128',
            'id_number'     => 'nullable|string|max:100',
            'grade'         => 'nullable|string|max:50',
            'section'       => 'nullable|string|max:20',
            'academic_year' => 'nullable|string|max:20',
            'board'         => 'nullable|string|max:50',
        ]);

        return $this->addMember($request, $request->only(['id_number', 'grade', 'section', 'academic_year', 'board']));
    }

    public function import(Request $request): RedirectResponse
    {
        // Full format: Name, Email, Roll_Number, Grade, Section, Academic_Year, Board, Password (optional)
        // Short format: Name, Email, Roll_Number, Password (optional)
        return $this->importMembers($request, function (array $row) {
            $full = count($row) >= 8;
            return [
                'name' => $row[0] ?? '', 'email' => $row[1] ?? '', 'password' => $full ? ($row[7] ?? null) : ($row[3] ?? null),
                'fields' => [
                    'id_number' => $row[2] ?? null, 'grade' => $full ? mb_substr($row[3] ?? '', 0, 50) : null,
                    'section' => $full ? mb_substr($row[4] ?? '', 0, 20) : null, 'academic_year' => $full ? mb_substr($row[5] ?? '', 0, 20) : null,
                    'board' => $full ? mb_substr($row[6] ?? '', 0, 50) : null,
                ],
            ];
        });
    }

    public function toggleStatus(SchoolMember $member): RedirectResponse
    {
        return $this->toggleMemberStatus($member);
    }

    public function destroy(SchoolMember $member): RedirectResponse
    {
        if ((int) $member->school_id !== (int) userAuth()->id || $member->role_in_school !== 'student') {
            abort(403);
        }

        $member->delete();

        $notification = ['messege' => __('Student removed from school.'), 'alert-type' => 'success'];
        return redirect()->route('school.students.index')->with($notification);
    }
}
