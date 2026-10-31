<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table            = 'subjects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['course_id', 'semester', 'code', 'name', 'type', 'credits', 'status'];
    protected $useTimestamps    = true;

    // Purpose: Returns subjects together with their course names and codes.
    public function getSubjectsWithCourse()
    {
        return $this->select('subjects.*, courses.name as course_name, courses.code as course_code')
                    ->join('courses', 'courses.id = subjects.course_id')
                    ->findAll();
    } // End of getSubjectsWithCourse()
}