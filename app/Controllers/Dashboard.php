<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\StudentModel;
use App\Models\AttendanceModel;

class Dashboard extends BaseController
{
    // Purpose: Displays the dashboard appropriate to the signed-in user’s role.
    public function index()
    {
        $role = session()->get('role');
        if ($role === 'student') {
            $studentModel = new StudentModel();
            $studentRecord = $studentModel->where('user_id', session()->get('user_id'))->first();
            $student = $studentRecord ? $studentModel->getStudentDetail((int) $studentRecord['id']) : null;
            $feeSummary = $studentRecord ? (new \App\Models\FeeInvoiceModel())->studentSummary((int) $studentRecord['id']) : null;
            $attendanceModel = new AttendanceModel();
            $attendanceStats = $studentRecord ? $attendanceModel->getStudentAttendanceStats((int) $studentRecord['id']) : null;
            $attendanceRecords = $studentRecord ? array_slice($attendanceModel->getStudentAttendanceRecords((int) $studentRecord['id']), 0, 5) : [];

            return view('dashboard/student', [
                'title'             => 'Student dashboard',
                'student'           => $student,
                'feeSummary'        => $feeSummary,
                'attendanceStats'   => $attendanceStats,
                'attendanceRecords' => $attendanceRecords,
            ]);
        }

        if ($role === 'accountant') {
            return redirect()->to('/fees');
        }

        if (! in_array($role, ['admin', 'faculty'], true)) {
            return view('dashboard/unavailable', [
                'title' => 'Admissions workspace',
            ]);
        }

        $leadModel = new LeadModel();

        $data = [
            'title'       => 'Admissions overview',
            'role'        => $role,
            'metrics'     => $leadModel->dashboardMetrics(),
            'pipeline'    => $leadModel->pipelineCounts(),
            'followUps'   => $leadModel->followUps(),
            'recentLeads' => $leadModel->recentLeads(),
        ];

        return view('dashboard/index', $data);
    } 
    // End of index()
}