<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    // Purpose: Seeds the application roles used for access control.
    public function run()
    {
        $data = [
            [
                'id'          => 1,
                'name'        => 'Admin',
                'description' => 'Full administrative control over the system.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 2,
                'name'        => 'Faculty',
                'description' => 'Access to assigned classes, attendance, and exam marks.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 3,
                'name'        => 'Student',
                'description' => 'Access to personal profile, grades, timetables, and fees.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 4,
                'name'        => 'Accountant',
                'description' => 'Access to fee collection, invoices, and financial reports.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->ignore(true)->insertBatch($data);
    } // End of run()
}