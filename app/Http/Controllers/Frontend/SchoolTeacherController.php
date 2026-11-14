<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SchoolMember;
use App\Http\Controllers\Frontend\Concerns\ManagesSchoolMembers;
use App\Services\SchoolMemberProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolTeacherController extends Controller
{
    use ManagesSchoolMembers;

    protected function memberRole(): string
    {
        return 'teacher';
    }

    public function index(): View
    {
        $teachers = SchoolMember::with('user')
            ->where('school_id', userAuth()->id)
            ->teachers()
            ->orderByDesc('id')
            ->paginate(15);

        return view('frontend.school-dashboard.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('frontend.school-dashboard.teachers.create');
    }

    public function downloadTemplate()
    {
        return $this->csvTemplate(['Name', 'Email', 'ID_Number', 'Password'], ['Jane Doe', 'teacher@example.com', 'TCH-001', ''], 'teachers_import_template.csv');
    }

    public function show(SchoolMember $member): View
    {
        if ((int) $member->school_id !== (int) userAuth()->id || $member->role_in_school !== 'teacher') {
            abort(403);
        }

        $member->load('user');

        // Get all courses assigned to this teacher by this school
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

        return view('frontend.school-dashboard.teachers.show', compact('member', 'assignments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'password'  => 'nullable|string|min:' . SchoolMemberProvisioner::MIN_PASSWORD . '|max:128',
            'id_number' => 'nullable|string|max:100',
        ]);

        return $this->addMember($request, ['id_number' => $request->id_number]);
    }

    public function import(Request $request): RedirectResponse
    {
        // Columns: Name, Email, ID_Number, Password (optional)
        return $this->importMembers($request, fn(array $row) => [
            'name' => $row[0] ?? '', 'email' => $row[1] ?? '', 'password' => $row[3] ?? null,
            'fields' => ['id_number' => $row[2] ?? null],
        ]);
    }

    public function toggleStatus(SchoolMember $member): RedirectResponse
    {
        return $this->toggleMemberStatus($member);
    }

    public function destroy(SchoolMember $member): RedirectResponse
    {
        if ((int) $member->school_id !== (int) userAuth()->id || $member->role_in_school !== 'teacher') {
            abort(403);
        }

        $member->delete();

        $notification = ['messege' => __('Teacher removed from school.'), 'alert-type' => 'success'];
        return redirect()->route('school.teachers.index')->with($notification);
    }
}
