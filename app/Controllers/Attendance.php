<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\SubjectModel;
use App\Models\StudentModel;
use App\Models\AuditLogModel;

class Attendance extends BaseController
{
    private const STATUSES = ['Present', 'Absent', 'Late', 'Excused'];

    // Purpose: Displays the paginated attendance records for staff.
    public function index()
    {
        $attendanceModel = new AttendanceModel();

        $data = [
            'title'      => 'Attendance Records',
            'attendance' => $attendanceModel->select('attendance.*, students.enrollment_no, users.name as student_name, subjects.name as subject_name, subjects.code as subject_code')
                ->join('students', 'students.id = attendance.student_id')
                ->join('users', 'users.id = students.user_id')
                ->join('subjects', 'subjects.id = attendance.subject_id')
                ->orderBy('attendance.date', 'DESC')
                ->paginate(15),
            'pager' => $attendanceModel->pager,
        ];

        return view('attendance/index', $data);
    } 
    // End of index()

    // Purpose: Displays the signed-in student’s attendance with validated filters and summaries.
    public function myAttendance()
    {
        $student = (new StudentModel())->where('user_id', session()->get('user_id'))->first();
        if (! $student) {
            return redirect()->to('/dashboard')->with('error', 'Your student record was not found. Please contact the university administrator.');
        }

        $filters = [
            'subject_id' => trim((string) $this->request->getGet('subject_id')),
            'date_from'  => trim((string) $this->request->getGet('date_from')),
            'date_to'    => trim((string) $this->request->getGet('date_to')),
        ];
        if (! $this->validateData($filters, [
            'subject_id' => 'permit_empty|integer',
            'date_from'  => 'permit_empty|valid_date[Y-m-d]',
            'date_to'    => 'permit_empty|valid_date[Y-m-d]',
        ])) {
            return redirect()->to('/attendance/my-attendance')->with('error', 'Please select a valid subject and date range.');
        }
        if ($filters['date_from'] !== '' && $filters['date_to'] !== '' && $filters['date_from'] > $filters['date_to']) {
            return redirect()->to('/attendance/my-attendance')->with('error', 'The start date must be on or before the end date.');
        }

        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')
            ->select('subjects.id, subjects.name, subjects.code')
            ->join('attendance', 'attendance.subject_id = subjects.id')
            ->where('attendance.student_id', $student['id'])
            ->groupBy('subjects.id, subjects.name, subjects.code')
            ->orderBy('subjects.name')
            ->get()
            ->getResultArray();
        $attendanceModel = new AttendanceModel();

        return view('attendance/my_attendance', [
            'title'    => 'My attendance',
            'filters'  => $filters,
            'subjects' => $subjects,
            'stats'    => $attendanceModel->getStudentAttendanceStats((int) $student['id'], $filters),
            'summary'  => $attendanceModel->getStudentAttendanceSummary((int) $student['id'], $filters),
            'records'  => $attendanceModel->getStudentAttendanceRecords((int) $student['id'], $filters),
        ]);
    } 
    // End of myAttendance()

    // Purpose: Validates report filters and displays attendance totals by subject.
    public function report()
    {
        $filters = [
            'subject_id' => trim((string) $this->request->getGet('subject_id')),
            'date_from'  => trim((string) $this->request->getGet('date_from')),
            'date_to'    => trim((string) $this->request->getGet('date_to')),
        ];

        $rules = [
            'subject_id' => 'permit_empty|integer',
            'date_from'  => 'permit_empty|valid_date[Y-m-d]',
            'date_to'    => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validateData($filters, $rules)) {
            return redirect()->to('/attendance')->with('error', 'Please select a valid subject and date range.');
        }

        if ($filters['date_from'] !== '' && $filters['date_to'] !== '' && $filters['date_from'] > $filters['date_to']) {
            return redirect()->to('/attendance')->with('error', 'The start date must be on or before the end date.');
        }

        $data = [
            'title'       => 'Attendance report',
            'filters'     => $filters,
            'summary'     => (new AttendanceModel())->getReportSummary($filters),
            'subjects'    => (new SubjectModel())->orderBy('name')->findAll(),
        ];

        return view('attendance/report', $data);
    } 
    // End of report()

    // Purpose: Builds the class attendance form with existing statuses and eligible students.
    public function markView()
    {
        $db = \Config\Database::connect();
        $subjectId = (int) $this->request->getGet('subject_id');
        $academicYearId = (int) $this->request->getGet('academic_year_id');
        $date = trim((string) $this->request->getGet('date')) ?: date('Y-m-d');
        $subjects = (new SubjectModel())->where('status', 'active')->orderBy('name')->findAll();
        $academicYears = $db->table('academic_years')->orderBy('start_date', 'DESC')->get()->getResultArray();
        $selectedSubject = $subjectId ? $db->table('subjects')->where('id', $subjectId)->where('status', 'active')->get()->getRowArray() : null;
        $students = [];
        $existingStatuses = [];

        if ($selectedSubject && $academicYearId && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $students = $this->studentsForClass($selectedSubject, $academicYearId);
            foreach ($db->table('attendance')
                ->where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYearId)
                ->where('date', $date)
                ->get()
                ->getResultArray() as $row) {
                $existingStatuses[(int) $row['student_id']] = $row['status'];
            }
        }

        $data = [
            'title'           => 'Mark Class Attendance',
            'subjects'        => $subjects,
            'academic_years'  => $academicYears,
            'selected_subject_id' => $subjectId,
            'selected_year_id'=> $academicYearId,
            'selected_date'   => $date,
            'students'        => $students,
            'existing_statuses' => $existingStatuses,
            'faculty_options' => $selectedSubject && $academicYearId ? $this->facultyOptions($selectedSubject, $academicYearId) : [],
        ];

        return view('attendance/mark', $data);
    } 
    // End of markView()

    // Purpose: Builds the individual attendance form with current class and status details.
    public function markStudentView()
    {
        $db = \Config\Database::connect();
        $subjectId = (int) $this->request->getGet('subject_id');
        $academicYearId = (int) $this->request->getGet('academic_year_id');
        $selectedFacultyId = (int) $this->request->getGet('faculty_id');
        $subject = $subjectId
            ? $db->table('subjects')->where('id', $subjectId)->where('status', 'active')->get()->getRowArray()
            : null;
        $students = $subject && $academicYearId ? $this->studentsForClass($subject, $academicYearId) : [];
        $selectedStudentId = (int) $this->request->getGet('student_id');
        $date = trim((string) $this->request->getGet('date')) ?: date('Y-m-d');
        $existingStatus = null;
        $facultyOptions = $subject && $academicYearId ? $this->facultyOptions($subject, $academicYearId) : [];
        if (! in_array($selectedFacultyId, array_map('intval', array_column($facultyOptions, 'id')), true)) {
            $selectedFacultyId = 0;
        }

        if ($selectedStudentId && $subject && $academicYearId) {
            $existingStatus = $db->table('attendance')
                ->where('student_id', $selectedStudentId)
                ->where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYearId)
                ->where('date', $date)
                ->get()
                ->getRow('status');
        }

        return view('attendance/mark_student', [
            'title'             => 'Mark Individual Attendance',
            'subjects'          => (new SubjectModel())->where('status', 'active')->orderBy('name')->findAll(),
            'academic_years'    => $db->table('academic_years')->orderBy('start_date', 'DESC')->get()->getResultArray(),
            'selected_subject_id' => $subjectId,
            'selected_year_id'  => $academicYearId,
            'selected_faculty_id' => $selectedFacultyId,
            'selected_student_id' => $selectedStudentId,
            'selected_date'     => $date,
            'students'          => $students,
            'existing_status'   => $existingStatus,
            'faculty_options'   => $facultyOptions,
        ]);
    } 
    // End of markStudentView()

    // Purpose: Validates and saves attendance for a class session.
    public function store()
    {
        $subjectId = (int) $this->request->getPost('subject_id');
        $date = trim((string) $this->request->getPost('date'));
        $academicYearId = (int) $this->request->getPost('academic_year_id');
        $facultyId = $this->validatedFacultyId($subjectId, $academicYearId);
        $statuses = $this->request->getPost('status');

        if (! is_array($statuses) || $statuses === [] || ! $facultyId) {
            return redirect()->back()->withInput()->with('error', 'Select a subject, academic year, faculty member and attendance statuses.');
        }

        if (! $this->validClassContext($subjectId, $academicYearId, $date)) {
            return redirect()->back()->withInput()->with('error', 'Select a valid subject, academic year and attendance date.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $attendanceModel = new AttendanceModel();
        $subject = $db->table('subjects')->where('id', $subjectId)->get()->getRowArray();
        $validStudentIds = array_column($this->studentsForClass($subject, $academicYearId), 'id');
        $savedCount = 0;

        foreach ($statuses as $studentId => $status) {
            $studentId = filter_var($studentId, FILTER_VALIDATE_INT);
            if (! $studentId || ! in_array((int) $studentId, array_map('intval', $validStudentIds), true) || ! in_array($status, self::STATUSES, true)) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'The submitted roster contains an invalid student or attendance status.');
            }

            if (! $attendanceModel->upsertAttendance([
                'student_id'       => (int) $studentId,
                'subject_id'       => $subjectId,
                'faculty_id'       => $facultyId,
                'academic_year_id' => $academicYearId,
                'date'             => $date,
                'status'           => $status,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ])) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Attendance could not be saved for the selected roster.');
            }
            $savedCount++;
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Attendance could not be saved. Please try again.');
        }

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'MARK_ATTENDANCE', 'Saved class attendance for subject ID ' . $subjectId . ' on ' . $date);

        return redirect()->to('/attendance')->with('success', 'Attendance saved for ' . $savedCount . ' students.');
    } 
    // End of store()

    // Purpose: Validates and saves attendance for an individual student.
    public function storeStudent()
    {
        $rules = [
            'student_id'       => 'required|integer',
            'subject_id'       => 'required|integer',
            'academic_year_id' => 'required|integer',
            'date'             => 'required|valid_date[Y-m-d]',
            'status'           => 'required|in_list[' . implode(',', self::STATUSES) . ']',
            'faculty_id'       => 'permit_empty|integer',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentId = (int) $this->request->getPost('student_id');
        $subjectId = (int) $this->request->getPost('subject_id');
        $academicYearId = (int) $this->request->getPost('academic_year_id');
        $date = (string) $this->request->getPost('date');
        $facultyId = $this->validatedFacultyId($subjectId, $academicYearId);

        if (! $facultyId || ! $this->validClassContext($subjectId, $academicYearId, $date)) {
            return redirect()->back()->withInput()->with('error', 'The subject, academic year or faculty assignment is invalid.');
        }

        $db = \Config\Database::connect();
        $subject = $db->table('subjects')->where('id', $subjectId)->get()->getRowArray();
        $validStudentIds = array_map('intval', array_column($this->studentsForClass($subject, $academicYearId), 'id'));
        if (! in_array($studentId, $validStudentIds, true)) {
            return redirect()->back()->withInput()->with('error', 'The selected student is not enrolled in this subject for the selected academic year.');
        }

        $db->transStart();
        $saved = (new AttendanceModel())->upsertAttendance([
            'student_id'       => $studentId,
            'subject_id'       => $subjectId,
            'faculty_id'       => $facultyId,
            'academic_year_id' => $academicYearId,
            'date'             => $date,
            'status'           => $this->request->getPost('status'),
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
        $db->transComplete();

        if (! $saved || $db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Attendance could not be saved for this student.');
        }

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'MARK_STUDENT_ATTENDANCE', 'Saved attendance for student ID ' . $studentId . ', subject ID ' . $subjectId . ' on ' . $date);

        return redirect()->to('/attendance')->with('success', 'Individual student attendance saved successfully.');
    } 
    // End of storeStudent()

    // Purpose: Checks that the subject, academic year, and date form a valid class context.
    private function validClassContext(int $subjectId, int $academicYearId, string $date): bool
    {
        if (! $subjectId || ! $academicYearId || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        $db = \Config\Database::connect();
        $subject = $db->table('subjects')->where('id', $subjectId)->where('status', 'active')->get()->getRowArray();
        $year = $db->table('academic_years')->where('id', $academicYearId)->get()->getRowArray();

        return $subject !== null && $year !== null && strtotime($date) !== false && date('Y-m-d', strtotime($date)) === $date && $date <= date('Y-m-d');
    } 
    // End of validClassContext()

    // Purpose: Returns active students enrolled in the selected subject and academic year.
    private function studentsForClass(array $subject, int $academicYearId): array
    {
        return (new StudentModel())->getStudentsWithDetails([
            'course_id'        => $subject['course_id'],
            'semester'         => $subject['semester'],
            'academic_year_id' => $academicYearId,
            'status'           => 'active',
        ])->findAll();
    } 
    // End of studentsForClass()

    // Purpose: Returns faculty members assigned to the selected subject and academic year.
    private function facultyOptions(array $subject, int $academicYearId): array
    {
        return \Config\Database::connect()->table('faculty_subjects')
            ->select('faculty.id, users.name')
            ->join('faculty', 'faculty.id = faculty_subjects.faculty_id')
            ->join('users', 'users.id = faculty.user_id')
            ->where('faculty_subjects.subject_id', $subject['id'])
            ->where('faculty_subjects.academic_year_id', $academicYearId)
            ->where('faculty.status', 'active')
            ->where('users.status', 'active')
            ->groupBy('faculty.id, users.name')
            ->orderBy('users.name')
            ->get()
            ->getResultArray();
    } 
    // End of facultyOptions()

    // Purpose: Returns the submitted faculty ID only when it is valid for the class.
    private function validatedFacultyId(int $subjectId, int $academicYearId): ?int
    {
        if (session()->get('role') === 'faculty') {
            $faculty = \Config\Database::connect()->table('faculty')
                ->where('user_id', session()->get('user_id'))
                ->where('status', 'active')
                ->get()
                ->getRowArray();
            if (! $faculty) {
                return null;
            }
            $facultyId = (int) $faculty['id'];
        } else {
            $facultyId = (int) $this->request->getPost('faculty_id');
            if (! $facultyId) {
                return null;
            }
        }

        $assigned = \Config\Database::connect()->table('faculty_subjects')
            ->where('faculty_id', $facultyId)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->countAllResults();

        return $assigned > 0 ? $facultyId : null;
    } 
    // End of validatedFacultyId()
}