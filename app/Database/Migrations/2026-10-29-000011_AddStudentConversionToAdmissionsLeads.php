<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentConversionToAdmissionsLeads extends Migration
{
    // Purpose: Adds student conversion fields and relationships to admissions leads.
    public function up()
    {
        $this->forge->addColumn('admissions_leads', [
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'converted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('student_id', false, true, 'admissions_leads_student_id_unique');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'SET NULL', 'admissions_leads_student_id_fk');
        $this->forge->processIndexes('admissions_leads');
    } // End of up()

    // Purpose: Removes student conversion fields and relationships from admissions leads.
    public function down()
    {
        $this->forge->dropForeignKey('admissions_leads', 'admissions_leads_student_id_fk');
        $this->forge->dropColumn('admissions_leads', ['student_id', 'converted_at']);
    } // End of down()
}
