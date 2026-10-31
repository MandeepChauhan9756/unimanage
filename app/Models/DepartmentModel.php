<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table            = 'departments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['code', 'name', 'status'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Purpose: Returns departments whose status is active.
    /**
     * Get active departments
     */
    public function getActiveDepartments()
    {
        return $this->where('status', 'active')->findAll();
    } // End of getActiveDepartments()
}