<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    // Purpose: Seeds the default academic departments.
    public function run()
    {
        $data = [
            ['code' => 'CS', 'name' => 'Computer Science & Engineering', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
            ['code' => 'IT', 'name' => 'Information Technology', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
            ['code' => 'EC', 'name' => 'Electronics & Communication', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
            ['code' => 'ME', 'name' => 'Mechanical Engineering', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('departments')->ignore(true)->insertBatch($data);
    } // End of run()
}