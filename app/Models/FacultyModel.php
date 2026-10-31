<?php

namespace App\Models;

use CodeIgniter\Model;

class FacultyModel extends Model
{
    protected $table            = 'faculty';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'employee_id', 'department_id', 'designation', 'qualification', 'joining_date', 'status'];
    protected $useTimestamps    = true;

    // Purpose: Returns faculty profiles with account and department details.
    public function getFacultyWithDetails()
    {
        return $this->select('faculty.*, users.name as full_name, users.email, users.phone, users.avatar, departments.name as department_name')
                    ->join('users', 'users.id = faculty.user_id')
                    ->join('departments', 'departments.id = faculty.department_id')
                    ->findAll();
    } // End of getFacultyWithDetails()
}