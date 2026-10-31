<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendance';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['student_id', 'subject_id', 'faculty_id', 'academic_year_id', 'date', 'status'];
    protected $useTimestamps    = true;

    // Purpose: Returns per-subject attendance totals for a student and optional date filters.
    public function getStudentAttendanceSummary($studentId, array $filters = [])
    {
        $builder = $this->db->table('attendance')
            ->select("subjects.id AS subject_id, subjects.name AS subject_name, subjects.code AS subject_code, COUNT(*) AS total_classes, SUM(CASE WHEN attendance.status = 'Present' THEN 1 ELSE 0 END) AS present_count, SUM(CASE WHEN attendance.status = 'Absent' THEN 1 ELSE 0 END) AS absent_count, SUM(CASE WHEN attendance.status = 'Late' THEN 1 ELSE 0 END) AS late_count, SUM(CASE WHEN attendance.status = 'Excused' THEN 1 ELSE 0 END) AS excused_count", false)
            ->join('subjects', 'subjects.id = attendance.subject_id')
            ->where('attendance.student_id', $studentId);

        if (! empty($filters['subject_id'])) {
            $builder->where('attendance.subject_id', $filters['subject_id']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('attendance.date >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('attendance.date <=', $filters['date_to']);
        }

        return $builder->groupBy('attendance.subject_id, subjects.id, subjects.name, subjects.code')
            ->orderBy('subjects.name')
            ->get()
            ->getResultArray();
    } // End of getStudentAttendanceSummary()

    // Purpose: Returns a student’s detailed attendance records with subject and year labels.
    public function getStudentAttendanceRecords(int $studentId, array $filters = []): array
    {
        $builder = $this->db->table('attendance')
            ->select('attendance.*, subjects.name AS subject_name, subjects.code AS subject_code, academic_years.year_label')
            ->join('subjects', 'subjects.id = attendance.subject_id')
            ->join('academic_years', 'academic_years.id = attendance.academic_year_id')
            ->where('attendance.student_id', $studentId);

        if (! empty($filters['subject_id'])) {
            $builder->where('attendance.subject_id', $filters['subject_id']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('attendance.date >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('attendance.date <=', $filters['date_to']);
        }

        return $builder->orderBy('attendance.date', 'DESC')
            ->orderBy('subjects.name')
            ->get()
            ->getResultArray();
    } // End of getStudentAttendanceRecords()

    // Purpose: Calculates attendance totals and the student’s attendance rate.
    public function getStudentAttendanceStats(int $studentId, array $filters = []): array
    {
        $builder = $this->db->table('attendance')
            ->select("COUNT(*) AS total_classes, SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_count, SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_count, SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late_count, SUM(CASE WHEN status = 'Excused' THEN 1 ELSE 0 END) AS excused_count", false)
            ->where('student_id', $studentId);

        if (! empty($filters['subject_id'])) {
            $builder->where('subject_id', $filters['subject_id']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('date >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('date <=', $filters['date_to']);
        }

        $stats = $builder->get()->getRowArray() ?? [];
        $total = (int) ($stats['total_classes'] ?? 0);
        $present = (int) ($stats['present_count'] ?? 0);
        $late = (int) ($stats['late_count'] ?? 0);

        return [
            'total_classes' => $total,
            'present_count' => $present,
            'absent_count'  => (int) ($stats['absent_count'] ?? 0),
            'late_count'    => $late,
            'excused_count' => (int) ($stats['excused_count'] ?? 0),
            'attendance_rate' => $total > 0 ? (int) round(($present + $late) / $total * 100) : 0,
        ];
    } // End of getStudentAttendanceStats()

    // Purpose: Updates an existing attendance record or inserts a new one for the same class date.
    public function upsertAttendance(array $data): bool
    {
        $existing = $this->where('student_id', $data['student_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('date', $data['date'])
            ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'faculty_id' => $data['faculty_id'],
                'status'     => $data['status'],
            ]);
        }

        return (bool) $this->insert($data);
    } // End of upsertAttendance()

    // Purpose: Returns attendance totals grouped by subject for the selected report filters.
    public function getReportSummary(array $filters = []): array
    {
        $builder = $this->db->table('attendance')
            ->select("subjects.id AS subject_id, subjects.code AS subject_code, subjects.name AS subject_name, COUNT(*) AS total_classes, SUM(CASE WHEN attendance.status = 'Present' THEN 1 ELSE 0 END) AS present_count, SUM(CASE WHEN attendance.status = 'Absent' THEN 1 ELSE 0 END) AS absent_count, SUM(CASE WHEN attendance.status = 'Late' THEN 1 ELSE 0 END) AS late_count", false)
            ->join('subjects', 'subjects.id = attendance.subject_id')
            ->groupBy('attendance.subject_id, subjects.id, subjects.code, subjects.name')
            ->orderBy('subjects.name');

        if (! empty($filters['subject_id'])) {
            $builder->where('attendance.subject_id', $filters['subject_id']);
        }

        if (! empty($filters['date_from'])) {
            $builder->where('attendance.date >=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $builder->where('attendance.date <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    } // End of getReportSummary()
}