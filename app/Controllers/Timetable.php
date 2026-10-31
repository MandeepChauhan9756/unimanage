<?php

namespace App\Controllers;

use App\Models\TimetableModel;
use App\Models\CourseModel;
use App\Models\SubjectModel;
use App\Models\FacultyModel;
use App\Models\AuditLogModel;

class Timetable extends BaseController
{
    // Purpose: Displays timetable entries filtered by course, semester, and weekday.
    public function index()
    {
        $timetableModel = new TimetableModel();
        $courseModel = new CourseModel();

        $filters = [
            'course_id'   => $this->request->getGet('course_id'),
            'semester'    => $this->request->getGet('semester'),
            'day_of_week' => $this->request->getGet('day_of_week'),
        ];

        $data = [
            'title'     => 'Class Timetable',
            'timetables'=> $timetableModel->getTimetableWithDetails($filters),
            'courses'   => $courseModel->findAll(),
            'filters'   => $filters,
        ];

        return view('timetable/index', $data);
    } 
    // End of index()

    // Purpose: Displays the timetable form with course, subject, faculty, and year options.
    public function create()
    {
        $courseModel = new CourseModel();
        $subjectModel = new SubjectModel();
        $facultyModel = new FacultyModel();
        $db = \Config\Database::connect();

        $data = [
            'title'         => 'Create Timetable Entry',
            'courses'       => $courseModel->findAll(),
            'subjects'      => $subjectModel->findAll(),
            'faculty'       => $facultyModel->select('faculty.id, users.name')->join('users', 'users.id = faculty.user_id')->findAll(),
            'academic_years'=> $db->table('academic_years')->get()->getResultArray(),
        ];

        return view('timetable/create', $data);
    } 
    // End of create()

    // Purpose: Validates and saves a timetable entry, then records the action.
    public function store()
    {
        $rules = [
            'course_id'      => 'required|integer',
            'semester'       => 'required|integer',
            'subject_id'     => 'required|integer',
            'faculty_id'     => 'required|integer',
            'classroom'      => 'required',
            'day_of_week'    => 'required|in_list[Monday,Tuesday,Wednesday,Thursday,Friday,Saturday]',
            'start_time'     => 'required',
            'end_time'       => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $timetableModel = new TimetableModel();
        
        $timetableModel->insert([
            'course_id'        => $this->request->getPost('course_id'),
            'semester'         => $this->request->getPost('semester'),
            'subject_id'       => $this->request->getPost('subject_id'),
            'faculty_id'       => $this->request->getPost('faculty_id'),
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'classroom'        => $this->request->getPost('classroom'),
            'day_of_week'      => $this->request->getPost('day_of_week'),
            'start_time'       => $this->request->getPost('start_time'),
            'end_time'         => $this->request->getPost('end_time'),
        ]);

        (new AuditLogModel())->logAction(session()->get('user_id'), 'CREATE_TIMETABLE', 'Added class timetable entry.');

        return redirect()->to('/timetable')->with('success', 'Timetable slot created successfully.');
    } 
    // End of store()
}