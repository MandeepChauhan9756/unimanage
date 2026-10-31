<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'user_id', 'enrollment_no', 'department_id', 'course_id', 'semester',
        'academic_year_id', 'gender', 'date_of_birth', 'admission_date',
        'address', 'guardian_name', 'guardian_phone', 'status'
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    // Purpose: Builds a student query with account and academic details and optional filters.
    public function getStudentsWithDetails($filters = [])
    {
        $builder = $this->select('students.*, users.name as full_name, users.email, users.phone, users.avatar, departments.name as department_name, courses.name as course_name, courses.code as course_code, academic_years.year_label')
                        ->join('users', 'users.id = students.user_id')
                        ->join('departments', 'departments.id = students.department_id')
                        ->join('courses', 'courses.id = students.course_id')
                        ->join('academic_years', 'academic_years.id = students.academic_year_id');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                    ->like('users.name', $search)
                    ->orLike('students.enrollment_no', $search)
                    ->orLike('users.email', $search)
                    ->orLike('users.phone', $search)
                    ->groupEnd();
        }

        if (!empty($filters['department_id'])) {
            $builder->where('students.department_id', $filters['department_id']);
        }

        if (!empty($filters['course_id'])) {
            $builder->where('students.course_id', $filters['course_id']);
        }

        if (!empty($filters['academic_year_id'])) {
            $builder->where('students.academic_year_id', $filters['academic_year_id']);
        }

        if (!empty($filters['semester'])) {
            $builder->where('students.semester', $filters['semester']);
        }

        if (!empty($filters['status'])) {
            $builder->where('students.status', $filters['status']);
        }

        return $builder->orderBy('students.id', 'DESC');
    } // End of getStudentsWithDetails()

    // Purpose: Returns one student profile with account, department, course, and year details.
    public function getStudentDetail($id)
    {
        return $this->select('students.*, users.name as full_name, users.email, users.phone, users.avatar, users.status as user_status, departments.name as department_name, departments.code as department_code, courses.name as course_name, courses.code as course_code, courses.total_semesters, academic_years.year_label')
                    ->join('users', 'users.id = students.user_id')
                    ->join('departments', 'departments.id = students.department_id')
                    ->join('courses', 'courses.id = students.course_id')
                    ->join('academic_years', 'academic_years.id = students.academic_year_id')
                    ->where('students.id', $id)
                    ->first();
    } // End of getStudentDetail()

    // Purpose: Generates the next enrollment number for a department and calendar year.
    public function generateEnrollmentNo($departmentCode)
    {
        $year = date('Y');
        $prefix = 'UNI-' . strtoupper($departmentCode) . '-' . $year . '-';
        
        $lastStudent = $this->select('enrollment_no')
                            ->like('enrollment_no', $prefix, 'after')
                            ->orderBy('id', 'DESC')
                            ->first();

        if ($lastStudent) {
            $lastSeq = (int) substr($lastStudent['enrollment_no'], -4);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }

        return $prefix . str_pad($newSeq, 4, '0', STR_PAD_LEFT);
    } // End of generateEnrollmentNo()
}