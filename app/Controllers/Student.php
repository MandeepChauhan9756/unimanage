<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\AuditLogModel;
use App\Models\CourseModel;

class Student extends BaseController
{
    // Purpose: Displays the filtered and paginated student directory.
    public function index()
    {
        $studentModel = new StudentModel();
        $departmentModel = new DepartmentModel();

        $filters = [
            'search'        => $this->request->getGet('search'),
            'department_id' => $this->request->getGet('department_id'),
            'course_id'     => $this->request->getGet('course_id'),
            'academic_year_id' => $this->request->getGet('academic_year_id'),
            'semester'      => $this->request->getGet('semester'),
            'status'        => $this->request->getGet('status'),
        ];

        $perPage = 10;
        $studentsQuery = $studentModel->getStudentsWithDetails($filters);
        $db = \Config\Database::connect();

        $data = [
            'title'       => 'Student Management',
            'students'    => $studentsQuery->paginate($perPage),
            'pager'       => $studentModel->pager,
            'departments' => $departmentModel->where('status', 'active')->findAll(),
            'courses'     => (new CourseModel())->where('status', 'active')->orderBy('name')->findAll(),
            'academic_years' => $db->table('academic_years')->orderBy('start_date', 'DESC')->get()->getResultArray(),
            'filters'     => $filters,
        ];

        return view('students/index', $data);
    } 
    // End of index()

    // Purpose: Displays the student enrollment form with active academic options.
    public function create()
    {
        $departmentModel = new DepartmentModel();
        $db = \Config\Database::connect();
        $academicYears = $db->table('academic_years')->orderBy('start_date', 'DESC')->get()->getResultArray();

        $data = [
            'title'         => 'Add New Student',
            'departments'   => $departmentModel->where('status', 'active')->findAll(),
            'courses'       => (new CourseModel())->where('status', 'active')->orderBy('name')->findAll(),
            'academic_years'=> $academicYears,
        ];

        return view('students/create', $data);
    } 
    // End of create()

    // Purpose: Validates and creates a student account and profile in a database transaction.
    public function store()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[150]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'phone'            => 'required|min_length[10]|max_length[20]',
            'department_id'    => 'required|integer',
            'course_id'        => 'required|integer',
            'semester'         => 'required|integer|greater_than[0]|less_than_equal_to[12]',
            'academic_year_id' => 'required|integer',
            'gender'           => 'required|in_list[Male,Female,Other]',
            'date_of_birth'    => 'required|valid_date',
            'admission_date'   => 'required|valid_date',
        ];

        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['avatar'] = 'uploaded[avatar]|max_size[avatar,2048]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $departmentId = (int) $this->request->getPost('department_id');
        $courseId = (int) $this->request->getPost('course_id');
        $academicYearId = (int) $this->request->getPost('academic_year_id');
        $course = $db->table('courses')
            ->where('id', $courseId)
            ->where('department_id', $departmentId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
        $department = $db->table('departments')
            ->where('id', $departmentId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
        $academicYear = $db->table('academic_years')->where('id', $academicYearId)->get()->getRowArray();

        if (!$department || !$course || !$academicYear) {
            return redirect()->back()->withInput()->with('error', 'Choose an active department, a course in that department, and a valid academic year.');
        }

        if ((int) $this->request->getPost('semester') > (int) $course['total_semesters']) {
            return redirect()->back()->withInput()->with('error', 'The selected semester is not available for this course.');
        }

        $db->transStart();
        $avatarPath = null;

        try {
            $avatarName = 'default.png';
            if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
                $uploadDirectory = FCPATH . 'uploads/avatars';
                if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                    throw new \RuntimeException('The avatar upload directory could not be created.');
                }

                $avatarName = $avatar->getRandomName();
                $avatar->move($uploadDirectory, $avatarName);
                $avatarPath = $uploadDirectory . DIRECTORY_SEPARATOR . $avatarName;
            }

            $userModel = new UserModel();
            $userId = $userModel->insert([
                'role_id'       => 3,
                'name'          => $this->request->getPost('name'),
                'email'         => $this->request->getPost('email'),
                'password_hash' => password_hash('Student@123', PASSWORD_BCRYPT),
                'phone'         => $this->request->getPost('phone'),
                'avatar'        => $avatarName,
                'status'        => 'active',
            ]);
            if (!$userId) {
                throw new \RuntimeException('The student account could not be created.');
            }

            $studentModel = new StudentModel();
            $enrollmentNo = $studentModel->generateEnrollmentNo($department['code']);

            $studentId = $studentModel->insert([
                'user_id'          => $userId,
                'enrollment_no'    => $enrollmentNo,
                'department_id'    => $departmentId,
                'course_id'        => $courseId,
                'semester'         => $this->request->getPost('semester'),
                'academic_year_id' => $academicYearId,
                'gender'           => $this->request->getPost('gender'),
                'date_of_birth'    => $this->request->getPost('date_of_birth'),
                'admission_date'   => $this->request->getPost('admission_date'),
                'address'          => $this->request->getPost('address'),
                'guardian_name'    => $this->request->getPost('guardian_name'),
                'guardian_phone'   => $this->request->getPost('guardian_phone'),
                'status'           => 'active',
            ]);
            if (!$studentId) {
                throw new \RuntimeException('The student profile could not be created.');
            }

            (new AuditLogModel())->logAction(session()->get('user_id'), 'CREATE_STUDENT', 'Created student record: ' . $enrollmentNo);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('The student transaction failed.');
            }

            return redirect()->to('/students')->with('success', 'Student successfully enrolled with Enrollment No: ' . $enrollmentNo);
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($avatarPath && is_file($avatarPath)) {
                unlink($avatarPath);
            }
            log_message('error', 'Student enrollment failed: {message}', ['message' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'The student could not be enrolled. Please check the details and try again.');
        }
    } 
    // End of store()

    // Purpose: Displays a student profile to an authorized viewer.
    public function profile($id)
    {
        $studentModel = new StudentModel();
        $student = $studentModel->getStudentDetail($id);

        if (!$student) {
            $destination = session()->get('role') === 'student' ? '/dashboard' : '/students';
            return redirect()->to($destination)->with('error', 'Student record not found.');
        }

        // Authorization check: Students can only view their own profile
        if (session()->get('role') === 'student') {
            $myStudentRecord = $studentModel->where('user_id', session()->get('user_id'))->first();
            if (!$myStudentRecord || $myStudentRecord['id'] != $id) {
                return redirect()->to('/dashboard')->with('error', 'Unauthorized access to student profile.');
            }
        }

        $data = [
            'title'          => 'Student Profile - ' . $student['full_name'],
            'student'        => $student,
            'initialPassword'=> session()->getFlashdata('student_reset_password'),
        ];

        return view('students/profile', $data);
    } 
    // End of profile()

    // Purpose: Displays the signed-in student’s own profile.
    public function myProfile()
    {
        $student = (new StudentModel())->where('user_id', session()->get('user_id'))->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Your student profile could not be found. Please contact an administrator.');
        }

        return redirect()->to('/students/profile/' . $student['id']);
    } 
    // End of myProfile()

    // Purpose: Changes the status of the selected student account and profile.
    public function toggleStatus($id)
    {
        $studentModel = new StudentModel();
        $student = $studentModel->find($id);

        if (!$student) {
            return redirect()->to('/students')->with('error', 'Student not found.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($student['user_id']);

        $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
        
        $userModel->update($student['user_id'], ['status' => $newStatus]);
        $studentModel->update($id, ['status' => $newStatus]);

        (new AuditLogModel())->logAction(session()->get('user_id'), 'TOGGLE_STUDENT_STATUS', 'Changed status of student ID ' . $id . ' to ' . $newStatus);

        return redirect()->to('/students')->with('success', 'Student status updated successfully.');
    } 
    // End of toggleStatus()

    // Purpose: Resets the selected student account password.
    public function resetPassword($id)
    {
        $student = (new StudentModel())->find($id);
        if (!$student) {
            return redirect()->to('/students')->with('error', 'Student not found.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($student['user_id']);
        if (!$user || (int) $user['role_id'] !== 3) {
            return redirect()->to('/students/profile/' . $id)->with('error', 'The student account could not be found.');
        }

        try {
            $temporaryPassword = bin2hex(random_bytes(6));
        } catch (\Throwable $e) {
            log_message('error', 'Student password reset token generation failed for student {studentId}: {message}', [
                'studentId' => $id,
                'message'   => $e->getMessage(),
            ]);

            return redirect()->to('/students/profile/' . $id)->with('error', 'A new password could not be generated. Please try again.');
        }

        if (!$userModel->update($student['user_id'], [
            'password_hash' => password_hash($temporaryPassword, PASSWORD_DEFAULT),
        ])) {
            log_message('error', 'Student password reset failed for student {studentId}.', ['studentId' => $id]);
            return redirect()->to('/students/profile/' . $id)->with('error', 'The student password could not be reset. Please try again.');
        }

        (new AuditLogModel())->logAction(
            (int) session()->get('user_id'),
            'RESET_STUDENT_PASSWORD',
            'Reset login password for student ID ' . $id
        );

        return redirect()->to('/students/profile/' . $id)
            ->with('success', 'Student password reset. Share the temporary password securely; it is shown only once.')
            ->with('student_reset_password', $temporaryPassword);
    } 
    // End of resetPassword()

    // Purpose: Exports the filtered student directory as a CSV download.
    public function exportCsv()
    {
        $studentModel = new StudentModel();
        $filters = [
            'search'        => $this->request->getGet('search'),
            'department_id' => $this->request->getGet('department_id'),
            'course_id'     => $this->request->getGet('course_id'),
            'academic_year_id' => $this->request->getGet('academic_year_id'),
            'semester'      => $this->request->getGet('semester'),
            'status'        => $this->request->getGet('status'),
        ];
        $students = $studentModel->getStudentsWithDetails($filters)->findAll();

        $filename = 'students_export_' . date('Y-m-d') . '.csv';
        
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Enrollment No', 'Full Name', 'Email', 'Phone', 'Department', 'Course', 'Academic Year', 'Semester', 'Status', 'Admission Date']);

        foreach ($students as $s) {
            fputcsv($output, [
                $s['enrollment_no'],
                $s['full_name'],
                $s['email'],
                $s['phone'],
                $s['department_name'],
                $s['course_name'],
                $s['year_label'],
                $s['semester'],
                $s['status'],
                $s['admission_date']
            ]);
        }

        fclose($output);
        exit;
    } 
    // End of exportCsv()
}