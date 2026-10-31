<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\CourseModel;
use App\Models\SubjectModel;
use App\Models\AuditLogModel;

class Academics extends BaseController
{
    // Purpose: Displays all departments for academic management.
    public function departments()
    {
        $departmentModel = new DepartmentModel();
        $data = [
            'title'       => 'Department Management',
            'departments' => $departmentModel->findAll(),
        ];
        return view('academics/departments', $data);
    } 
    // End of departments()

    // Purpose: Validates and saves a new department, then records the action.
    public function storeDepartment()
    {
        $rules = [
            'code' => 'required|is_unique[departments.code]|max_length[20]',
            'name' => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $departmentModel = new DepartmentModel();
        $departmentModel->insert([
            'code'   => strtoupper($this->request->getPost('code')),
            'name'   => $this->request->getPost('name'),
            'status' => 'active',
        ]);

        (new AuditLogModel())->logAction(session()->get('user_id'), 'CREATE_DEPARTMENT', 'Created department: ' . $this->request->getPost('name'));

        return redirect()->to('/academics/departments')->with('success', 'Department created successfully.');
    } 
    // End of storeDepartment()

    // Purpose: Displays courses with their associated departments.
    public function courses()
    {
        $courseModel = new CourseModel();
        $departmentModel = new DepartmentModel();

        $data = [
            'title'       => 'Course Management',
            'courses'     => $courseModel->getCoursesWithDepartment(),
            'departments' => $departmentModel->getActiveDepartments(),
        ];
        return view('academics/courses', $data);
    } 
    // End of courses()

    // Purpose: Validates and saves a new course, then records the action.
    public function storeCourse()
    {
        $rules = [
            'department_id'   => 'required|integer',
            'code'            => 'required|is_unique[courses.code]|max_length[20]',
            'name'            => 'required|min_length[3]|max_length[150]',
            'total_semesters' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $courseModel = new CourseModel();
        $courseModel->insert([
            'department_id'   => $this->request->getPost('department_id'),
            'code'            => strtoupper($this->request->getPost('code')),
            'name'            => $this->request->getPost('name'),
            'total_semesters' => $this->request->getPost('total_semesters'),
            'status'          => 'active',
        ]);

        (new AuditLogModel())->logAction(session()->get('user_id'), 'CREATE_COURSE', 'Created course: ' . $this->request->getPost('name'));

        return redirect()->to('/academics/courses')->with('success', 'Course created successfully.');
    } 
    // End of storeCourse()

    // Purpose: Displays subjects with their courses and active course options.
    public function subjects()
    {
        $subjectModel = new SubjectModel();
        $courseModel = new CourseModel();

        $data = [
            'title'    => 'Subject Management',
            'subjects' => $subjectModel->getSubjectsWithCourse(),
            'courses'  => $courseModel->where('status', 'active')->findAll(),
        ];
        return view('academics/subjects', $data);
    } 
    // End of subjects()

    // Purpose: Validates and saves a new subject, then records the action.
    public function storeSubject()
    {
        $rules = [
            'course_id' => 'required|integer',
            'semester'  => 'required|integer',
            'code'      => 'required|is_unique[subjects.code]|max_length[20]',
            'name'      => 'required|min_length[3]|max_length[150]',
            'type'      => 'required|in_list[Theory,Practical,Elective]',
            'credits'   => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $subjectModel = new SubjectModel();
        $subjectModel->insert([
            'course_id' => $this->request->getPost('course_id'),
            'semester'  => $this->request->getPost('semester'),
            'code'      => strtoupper($this->request->getPost('code')),
            'name'      => $this->request->getPost('name'),
            'type'      => $this->request->getPost('type'),
            'credits'   => $this->request->getPost('credits'),
            'status'    => 'active',
        ]);

        (new AuditLogModel())->logAction(session()->get('user_id'), 'CREATE_SUBJECT', 'Created subject: ' . $this->request->getPost('name'));

        return redirect()->to('/academics/subjects')->with('success', 'Subject created successfully.');
    } 
    // End of storeSubject()
}