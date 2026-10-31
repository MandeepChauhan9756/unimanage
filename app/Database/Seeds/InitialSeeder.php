<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    // Purpose: Seeds the initial application data required to get started.
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('DepartmentSeeder');
        $this->call('AcademicYearSeeder');
        $this->call('UserSeeder');
    } // End of run()
}