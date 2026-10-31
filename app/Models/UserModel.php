<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['role_id', 'name', 'email', 'password_hash', 'phone', 'avatar', 'status', 'last_login'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    // Purpose: Finds a user by email and includes the assigned role name.
    public function getUserWithRole(string $email)
    {
        return $this->select('users.*, roles.name as role_name')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.email', $email)
                    ->first();
    } // End of getUserWithRole()
}