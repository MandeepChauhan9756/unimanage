<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcademicFoundationTables extends Migration
{
    // Purpose: Creates or updates the database schema for 2026-10-28-000003_ create academic foundation tables.
    public function up()
    {
        // Departments Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('departments');

        // Academic Years Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'year_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 20, // e.g., "2026-2027"
                'unique'     => true,
            ],
            'start_date' => ['type' => 'DATE'],
            'end_date'   => ['type' => 'DATE'],
            'is_current' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('academic_years');
    } // End of up()

    // Purpose: Reverts the database schema changes for 2026-10-28-000003_ create academic foundation tables.
    public function down()
    {
        $this->forge->dropTable('academic_years');
        $this->forge->dropTable('departments');
    } // End of down()
}