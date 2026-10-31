<?php

namespace App\Controllers;

use App\Models\FacultyModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\SubjectModel;
use App\Models\AuditLogModel;
use CodeIgniter\Database\Config;

class FacultyController extends BaseController
{
    // Purpose: Displays faculty members with their department and account details.
    public function index()
    {
        $facultyModel = new FacultyModel();

        $data = [
            'title'   => 'Faculty Members',
            'faculty' => $facultyModel->getFacultyWithDetails(),
        ];

        return view('faculty/index', $data);
    } 
    // End of index()

    // Purpose: Displays the faculty registration form with active departments.
    public function create()
    {
        $departmentModel = new DepartmentModel();

        $data = [
            'title'       => 'Add Faculty Member',
            'departments' => $departmentModel->getActiveDepartments(),
        ];

        return view('faculty/create', $data);
    } 
    // End of create()

    // Purpose: Validates and creates a faculty account and profile in a database transaction.
    public function store()
    {
        $rules = [
            'name'          => 'required|min_length[3]|max_length[150]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'phone'         => 'required|min_length[10]|max_length[20]',
            'department_id' => 'required|integer',
            'designation'   => 'required|min_length[2]',
            'joining_date'  => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = Config::connect();
        $db->transStart();

        try {
            // 1. Create User (Role ID 2 = Faculty)
            $userModel = new UserModel();
            $userId = $userModel->insert([
                'role_id'       => 2,
                'name'          => $this->request->getPost('name'),
                'email'         => $this->request->getPost('email'),
                'password_hash' => password_hash('Faculty@123', PASSWORD_BCRYPT),
                'phone'         => $this->request->getPost('phone'),
                'status'        => 'active',
            ]);

            // 2. Generate Employee ID
            $dept = $db->table('departments')->where('id', $this->request->getPost('department_id'))->get()->getRowArray();
            $empId = 'FAC-' . strtoupper($dept['code'] ?? 'GEN') . '-' . rand(1000, 9999);

            // 3. Create Faculty Record
            $facultyModel = new FacultyModel();
            $facultyModel->insert([
                'user_id'       => $userId,
                'employee_id'   => $empId,
                'department_id' => $this->request->getPost('department_id'),
                'designation'   => $this->request->getPost('designation'),
                'qualification' => $this->request->getPost('qualification'),
                'joining_date'  => $this->request->getPost('joining_date'),
                'status'        => 'active',
            ]);

            (new AuditLogModel())->logAction(session()->get('user_id'), 'CREATE_FACULTY', 'Added faculty member: ' . $empId);

            $db->transComplete();

            return redirect()->to('/faculty')->with('success', 'Faculty member registered with Employee ID: ' . $empId);
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error creating faculty: ' . $e->getMessage());
        }
    } 
    // End of store()

    // Purpose: Displays active faculty, subjects, and academic years for assignment.
    public function assignSubject()
    {
        $subjectModel = new SubjectModel();
        $db = Config::connect();

        $data = [
            'title'          => 'Assign Subject to Faculty',
            'faculty_list'   => $db->table('faculty')
                ->select('faculty.id, faculty.employee_id, users.name AS full_name')
                ->join('users', 'users.id = faculty.user_id')
                ->where('faculty.status', 'active')
                ->where('users.status', 'active')
                ->orderBy('users.name')
                ->get()
                ->getResultArray(),
            'subjects'       => $subjectModel->getSubjectsWithCourse(),
            'academic_years' => $db->table('academic_years')->get()->getResultArray(),
        ];

        return view('faculty/assign_subject', $data);
    } 
    // End of assignSubject()

    // Purpose: Validates and saves a faculty-to-subject assignment for an academic year.
    public function storeAssignment()
    {
        $rules = [
            'faculty_id'       => 'required|integer',
            'subject_id'       => 'required|integer',
            'academic_year_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = Config::connect();

        $activeFacultyExists = $db->table('faculty')
            ->join('users', 'users.id = faculty.user_id')
            ->where('faculty.id', $this->request->getPost('faculty_id'))
            ->where('faculty.status', 'active')
            ->where('users.status', 'active')
            ->countAllResults();
        if ($activeFacultyExists === 0) {
            return redirect()->back()->withInput()->with('error', 'Select an active faculty member for this subject assignment.');
        }
        
        // Prevent Duplicate Mapping
        $exists = $db->table('faculty_subjects')
                     ->where([
                         'faculty_id'       => $this->request->getPost('faculty_id'),
                         'subject_id'       => $this->request->getPost('subject_id'),
                         'academic_year_id' => $this->request->getPost('academic_year_id'),
                     ])->countAllResults();

        if ($exists > 0) {
            return redirect()->back()->withInput()->with('error', 'This subject is already assigned to this faculty member for the selected academic year.');
        }

        $db->table('faculty_subjects')->insert([
            'faculty_id'       => $this->request->getPost('faculty_id'),
            'subject_id'       => $this->request->getPost('subject_id'),
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        (new AuditLogModel())->logAction(session()->get('user_id'), 'ASSIGN_SUBJECT', 'Assigned subject ID ' . $this->request->getPost('subject_id') . ' to faculty ID ' . $this->request->getPost('faculty_id'));

        return redirect()->to('/faculty')->with('success', 'Subject successfully assigned to faculty member.');
    } 
    // End of storeAssignment()
}