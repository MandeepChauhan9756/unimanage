<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['department_id', 'code', 'name', 'total_semesters', 'status'];
    protected $useTimestamps    = true;

    // Purpose: Returns courses together with their department names and codes.
    public function getCoursesWithDepartment()
    {
        return $this->select('courses.*, departments.name as department_name, departments.code as department_code')
                    ->join('departments', 'departments.id = courses.department_id')
                    ->findAll();
    } // End of getCoursesWithDepartment()
}