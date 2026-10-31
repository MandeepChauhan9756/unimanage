<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFacultySubjectsTable extends Migration
{
    // Purpose: Creates or updates the database schema for 2026-10-28-000009_ create faculty subjects.
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'faculty_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'subject_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'academic_year_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('faculty_id', 'faculty', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subject_id', 'subjects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('academic_year_id', 'academic_years', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('faculty_subjects');
    } // End of up()

    // Purpose: Reverts the database schema changes for 2026-10-28-000009_ create faculty subjects.
    public function down()
    {
        $this->forge->dropTable('faculty_subjects');
    } // End of down()
}