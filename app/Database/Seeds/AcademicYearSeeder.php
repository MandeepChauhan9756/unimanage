<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    // Purpose: Seeds the default academic year records.
    public function run()
    {
        $data = [
            ['year_label' => '2025-2026', 'start_date' => '2025-08-01', 'end_date' => '2026-06-30', 'is_current' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['year_label' => '2026-2027', 'start_date' => '2026-08-01', 'end_date' => '2027-06-30', 'is_current' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('academic_years')->ignore(true)->insertBatch($data);
    } // End of run()
}