<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    // Purpose: Seeds the initial application user accounts.
    public function run()
    {
        $passwordHash = password_hash('Admin@123', PASSWORD_BCRYPT);

        $users = [
            [
                'role_id'       => 1, // Admin
                'name'          => 'System Administrator',
                'email'         => 'admin@unimanage.edu',
                'password_hash' => $passwordHash,
                'phone'         => '+919876543210',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'role_id'       => 2, // Faculty
                'name'          => 'Dr. Rajesh Kumar',
                'email'         => 'faculty@unimanage.edu',
                'password_hash' => password_hash('Faculty@123', PASSWORD_BCRYPT),
                'phone'         => '+919876543211',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'role_id'       => 3, // Student
                'name'          => 'Mandeep Chauhan',
                'email'         => 'student@unimanage.edu',
                'password_hash' => password_hash('Student@123', PASSWORD_BCRYPT),
                'phone'         => '+919876543212',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'role_id'       => 4, // Accountant
                'name'          => 'Finance Manager',
                'email'         => 'accountant@unimanage.edu',
                'password_hash' => password_hash('Accountant@123', PASSWORD_BCRYPT),
                'phone'         => '+919876543213',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->ignore(true)->insertBatch($users);
    } // End of run()
}