<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\LeadActivityModel;
use App\Models\LeadModel;
use App\Models\StudentModel;
use App\Models\UserModel;

class Leads extends BaseController
{
    private const STATUSES = ['new', 'contacted', 'counseling', 'application', 'enrolled', 'lost'];
    private const SOURCES = ['website', 'referral', 'social', 'walk_in', 'education_fair', 'other'];
    private const STUDY_LEVELS = ['Undergraduate', 'Postgraduate', 'Doctoral', 'Diploma', 'Other'];
    private const ACTIVITY_TYPES = ['call', 'email', 'whatsapp', 'counseling', 'application', 'other'];

    // Purpose: Displays filtered admissions leads, pipeline metrics, and counselor options.
    public function index()
    {
        $leadModel = new LeadModel();
        $filters = [
            'search'      => trim((string) $this->request->getGet('search')),
            'status'      => (string) $this->request->getGet('status'),
            'lead_source' => (string) $this->request->getGet('lead_source'),
            'assigned_to' => (string) $this->request->getGet('assigned_to'),
        ];

        return view('leads/index', [
            'title'      => 'Admissions leads',
            'leads'      => $leadModel->filtered($filters)->paginate(12),
            'pager'      => $leadModel->pager,
            'filters'    => $filters,
            'pipeline'   => $leadModel->pipelineCounts(),
            'metrics'    => $leadModel->dashboardMetrics(),
            'statuses'   => self::STATUSES,
            'sources'    => self::SOURCES,
            'counselors' => $this->counselors(),
        ]);
    }
    // End of index()

    // Purpose: Displays the form for creating an admissions lead.
    public function create()
    {
        return view('leads/form', $this->formData('Add a new lead', null));
    }
    // End of create()

    // Purpose: Validates and saves an admissions lead and its initial activity.
    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $attributes = $this->leadAttributes();
        $attributes['created_by'] = (int) session()->get('user_id');
        if (! $this->validCounselor($attributes['assigned_to'])) {
            return redirect()->back()->withInput()->with('error', 'Choose an active counselor from the list.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $leadModel = new LeadModel();
        $leadId = $leadModel->insert($attributes, true);
        (new LeadActivityModel())->insert([
            'lead_id'      => $leadId,
            'user_id'      => (int) session()->get('user_id'),
            'activity_type' => 'system',
            'details'      => 'Lead created.',
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'The lead could not be saved. Please try again.');
        }

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'CREATE_ADMISSIONS_LEAD', 'Created admissions lead ID ' . $leadId);

        return redirect()->to('/leads/profile/' . $leadId)->with('success', 'Lead added to your admissions pipeline.');
    }
    // End of store()

    // Purpose: Displays a lead profile with its activity timeline and enrollment details.
    public function profile(int $id)
    {
        $leadModel = new LeadModel();
        $lead = $leadModel->withCounselor($id);

        if (! $lead) {
            return redirect()->to('/leads')->with('error', 'That lead could not be found.');
        }

        return view('leads/profile', [
            'title'        => $lead['student_name'],
            'lead'         => $lead,
            'activities'   => (new LeadActivityModel())->forLead($id),
            'activityTypes' => self::ACTIVITY_TYPES,
            'initialPassword' => session()->getFlashdata('student_initial_password'),
        ]);
    }
    // End of profile()

    // Purpose: Displays enrollment options for an eligible application-stage lead.
    public function convert(int $id)
    {
        $lead = (new LeadModel())->withCounselor($id);
        if (! $lead) {
            return redirect()->to('/leads')->with('error', 'That lead could not be found.');
        }

        if ($lead['student_id']) {
            return redirect()->to('/leads/profile/' . $id)->with('error', 'This lead has already been converted to a student.');
        }

        if ($lead['status'] !== 'application') {
            return redirect()->to('/leads/profile/' . $id)->with('error', 'Move the lead to the Application stage before converting it to an enrolled student.');
        }

        $db = \Config\Database::connect();

        return view('leads/convert', [
            'title'          => 'Enroll ' . $lead['student_name'],
            'lead'           => $lead,
            'departments'    => $db->table('departments')->where('status', 'active')->orderBy('name')->get()->getResultArray(),
            'courses'        => $db->table('courses')->where('status', 'active')->orderBy('name')->get()->getResultArray(),
            'academic_years' => $db->table('academic_years')->orderBy('start_date', 'DESC')->get()->getResultArray(),
        ]);
    }
    // End of convert()

    // Purpose: Validates and converts an application-stage lead into a student transactionally.
    public function convertStore(int $id)
    {
        $leadModel = new LeadModel();
        $lead = $leadModel->find($id);

        if (! $lead) {
            return redirect()->to('/leads')->with('error', 'That lead could not be found.');
        }

        if ($lead['student_id'] || $lead['status'] !== 'application') {
            return redirect()->to('/leads/profile/' . $id)->with('error', 'Only an unconverted lead in the Application stage can be enrolled.');
        }

        $rules = [
            'email'            => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'department_id'    => 'required|integer',
            'course_id'        => 'required|integer',
            'semester'         => 'required|integer|greater_than[0]|less_than_equal_to[12]',
            'academic_year_id' => 'required|integer',
            'gender'           => 'required|in_list[Male,Female,Other]',
            'date_of_birth'    => 'required|valid_date',
            'admission_date'   => 'required|valid_date',
            'guardian_name'    => 'permit_empty|max_length[150]',
            'guardian_phone'   => 'permit_empty|max_length[20]',
            'address'          => 'permit_empty|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $departmentId = (int) $this->request->getPost('department_id');
        $courseId = (int) $this->request->getPost('course_id');
        $academicYearId = (int) $this->request->getPost('academic_year_id');
        $department = $db->table('departments')
            ->where('id', $departmentId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
        $course = $db->table('courses')
            ->where('id', $courseId)
            ->where('department_id', $departmentId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
        $academicYear = $db->table('academic_years')->where('id', $academicYearId)->get()->getRowArray();

        if (! $department || ! $course || ! $academicYear) {
            return redirect()->back()->withInput()->with('error', 'Choose an active department, a course in that department, and a valid academic year.');
        }

        if ((int) $this->request->getPost('semester') > (int) $course['total_semesters']) {
            return redirect()->back()->withInput()->with('error', 'The selected semester is not available for this course.');
        }

        if (strlen($lead['phone']) > 20) {
            return redirect()->back()->withInput()->with('error', 'The lead phone number is longer than the student account field allows. Edit the lead phone number before enrolling.');
        }

        $db->transStart();

        try {
            $initialPassword = bin2hex(random_bytes(8));
            $userModel = new UserModel();
            $userId = $userModel->insert([
                'role_id'       => 3,
                'name'          => $lead['student_name'],
                'email'         => trim((string) $this->request->getPost('email')),
                'password_hash' => password_hash($initialPassword, PASSWORD_DEFAULT),
                'phone'         => $lead['phone'],
                'avatar'        => 'default.png',
                'status'        => 'active',
            ]);

            if (! $userId) {
                throw new \RuntimeException('The student account could not be created.');
            }

            $studentModel = new StudentModel();
            $enrollmentNo = $studentModel->generateEnrollmentNo($department['code']);
            $studentId = $studentModel->insert([
                'user_id'          => $userId,
                'enrollment_no'    => $enrollmentNo,
                'department_id'    => $departmentId,
                'course_id'        => $courseId,
                'semester'         => (int) $this->request->getPost('semester'),
                'academic_year_id' => $academicYearId,
                'gender'           => $this->request->getPost('gender'),
                'date_of_birth'    => $this->request->getPost('date_of_birth'),
                'admission_date'   => $this->request->getPost('admission_date'),
                'address'          => trim((string) $this->request->getPost('address')) ?: null,
                'guardian_name'    => trim((string) $this->request->getPost('guardian_name')) ?: null,
                'guardian_phone'   => trim((string) $this->request->getPost('guardian_phone')) ?: null,
                'status'           => 'active',
            ]);

            if (! $studentId) {
                throw new \RuntimeException('The student profile could not be created.');
            }

            if (! $leadModel->update($id, [
                'status'         => 'enrolled',
                'student_id'     => $studentId,
                'converted_at'   => date('Y-m-d H:i:s'),
                'next_follow_up' => null,
            ])) {
                throw new \RuntimeException('The lead could not be marked as enrolled.');
            }
            if (! (new LeadActivityModel())->insert([
                'lead_id'       => $id,
                'user_id'       => (int) session()->get('user_id'),
                'activity_type' => 'system',
                'details'       => 'Lead won and enrolled as student ' . $enrollmentNo . '.',
                'created_at'    => date('Y-m-d H:i:s'),
            ])) {
                throw new \RuntimeException('The enrollment event could not be recorded.');
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('The lead conversion transaction failed.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Lead conversion failed for lead {leadId}: {message}', [
                'leadId' => $id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'The lead could not be converted. Please check the details and try again.');
        }

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'CONVERT_ADMISSIONS_LEAD', 'Converted lead ID ' . $id . ' to student ' . $enrollmentNo);

        return redirect()->to('/leads/profile/' . $id)
            ->with('success', 'Lead won and student enrolled. Enrollment No: ' . $enrollmentNo)
            ->with('student_initial_password', $initialPassword);
    } 
    // End of convertStore()

    // Purpose: Displays the edit form for an existing, unconverted lead.
    public function edit(int $id)
    {
        $lead = (new LeadModel())->find($id);
        if (! $lead) {
            return redirect()->to('/leads')->with('error', 'That lead could not be found.');
        }
        if ($lead['status'] === 'enrolled') {
            return redirect()->to('/leads/profile/' . $id)->with('error', 'Enrolled leads cannot be edited.');
        }

        return view('leads/form', $this->formData('Update lead details', $lead));
    } 
    // End of edit()

    // Purpose: Validates and updates a lead and records any stage change.
    public function update(int $id)
    {
        $leadModel = new LeadModel();
        $existing = $leadModel->find($id);
        if (! $existing) {
            return redirect()->to('/leads')->with('error', 'That lead could not be found.');
        }
        if ($existing['status'] === 'enrolled') {
            return redirect()->to('/leads/profile/' . $id)->with('error', 'Enrolled leads cannot be edited.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $attributes = $this->leadAttributes();
        if (! $this->validCounselor($attributes['assigned_to'])) {
            return redirect()->back()->withInput()->with('error', 'Choose an active counselor from the list.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $leadModel->update($id, $attributes);

        if ($existing['status'] !== $attributes['status']) {
            (new LeadActivityModel())->insert([
                'lead_id'       => $id,
                'user_id'       => (int) session()->get('user_id'),
                'activity_type' => 'status',
                'details'       => 'Stage changed from ' . ucfirst($existing['status']) . ' to ' . ucfirst($attributes['status']) . '.',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'The lead could not be updated. Please try again.');
        }

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'UPDATE_ADMISSIONS_LEAD', 'Updated admissions lead ID ' . $id);

        return redirect()->to('/leads/profile/' . $id)->with('success', 'Lead details updated.');
    } 
    // End of update()

    // Purpose: Validates and adds a new activity to a lead timeline.
    public function addActivity(int $id)
    {
        $lead = (new LeadModel())->find($id);
        if (! $lead) {
            return redirect()->to('/leads')->with('error', 'That lead could not be found.');
        }

        $rules = [
            'activity_type' => 'required|in_list[' . implode(',', self::ACTIVITY_TYPES) . ']',
            'details'       => 'required|min_length[3]|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new LeadActivityModel())->insert([
            'lead_id'       => $id,
            'user_id'       => (int) session()->get('user_id'),
            'activity_type' => $this->request->getPost('activity_type'),
            'details'       => trim((string) $this->request->getPost('details')),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        (new AuditLogModel())->logAction((int) session()->get('user_id'), 'ADD_LEAD_ACTIVITY', 'Added activity to admissions lead ID ' . $id);

        return redirect()->to('/leads/profile/' . $id)->with('success', 'Activity added to the lead timeline.');
    } 
    // End of addActivity()

    // Purpose: Returns the validation rules used for admissions lead details.
    private function validationRules(): array
    {
        return [
            'student_name'       => 'required|min_length[2]|max_length[150]',
            'email'              => 'permit_empty|valid_email|max_length[150]',
            'phone'              => 'required|min_length[8]|max_length[25]',
            'city'               => 'permit_empty|max_length[100]',
            'country'            => 'permit_empty|max_length[100]',
            'study_level'        => 'permit_empty|in_list[' . implode(',', self::STUDY_LEVELS) . ']',
            'interested_program' => 'permit_empty|max_length[150]',
            'preferred_university' => 'permit_empty|max_length[150]',
            'intake'             => 'permit_empty|max_length[30]',
            'lead_source'        => 'required|in_list[' . implode(',', self::SOURCES) . ']',
            'status'             => 'required|in_list[new,contacted,counseling,application,lost]',
            'assigned_to'        => 'permit_empty|integer',
            'next_follow_up'     => 'permit_empty|valid_date[Y-m-d\\TH:i]',
            'notes'              => 'permit_empty|max_length[5000]',
        ];
    } 
    // End of validationRules()

    // Purpose: Builds the normalized lead data from the submitted form fields.
    private function leadAttributes(): array
    {
        $followUp = trim((string) $this->request->getPost('next_follow_up'));

        return [
            'student_name'        => trim((string) $this->request->getPost('student_name')),
            'email'               => trim((string) $this->request->getPost('email')) ?: null,
            'phone'               => trim((string) $this->request->getPost('phone')),
            'city'                => trim((string) $this->request->getPost('city')) ?: null,
            'country'             => trim((string) $this->request->getPost('country')) ?: null,
            'study_level'         => $this->request->getPost('study_level') ?: null,
            'interested_program'  => trim((string) $this->request->getPost('interested_program')) ?: null,
            'preferred_university' => trim((string) $this->request->getPost('preferred_university')) ?: null,
            'intake'              => trim((string) $this->request->getPost('intake')) ?: null,
            'lead_source'         => $this->request->getPost('lead_source'),
            'status'              => $this->request->getPost('status'),
            'assigned_to'         => $this->request->getPost('assigned_to') ?: null,
            'next_follow_up'      => $followUp === '' ? null : date('Y-m-d H:i:s', strtotime($followUp)),
            'notes'               => trim((string) $this->request->getPost('notes')) ?: null,
        ];
    } 
    // End of leadAttributes()

    // Purpose: Builds the data required to render the lead creation or edit form.
    private function formData(string $title, ?array $lead): array
    {
        return [
            'title'       => $title,
            'lead'        => $lead,
            'statuses'    => array_values(array_diff(self::STATUSES, ['enrolled'])),
            'sources'     => self::SOURCES,
            'studyLevels' => self::STUDY_LEVELS,
            'counselors'  => $this->counselors(),
        ];
    } 
    // End of formData()

    // Purpose: Returns active users eligible to be assigned as lead counselors.
    private function counselors(): array
    {
        return (new UserModel())->select('users.id, users.name')
            ->join('roles', 'roles.id = users.role_id')
            ->whereIn('roles.name', ['Admin', 'Faculty'])
            ->where('users.status', 'active')
            ->orderBy('users.name')
            ->findAll();
    } 
    // End of counselors()

    // Purpose: Checks whether the selected counselor is an active eligible user.
    private function validCounselor(?int $counselorId): bool
    {
        if ($counselorId === null) {
            return true;
        }

        foreach ($this->counselors() as $counselor) {
            if ((int) $counselor['id'] === $counselorId) {
                return true;
            }
        }

        return false;
    } 
    // End of validCounselor()
}
