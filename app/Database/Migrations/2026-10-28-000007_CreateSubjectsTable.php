<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubjectsTable extends Migration
{
    // Purpose: Creates or updates the database schema for 2026-10-28-000007_ create subjects.
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'semester' => [
                'type'       => 'INT',
                'constraint' => 2,
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
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['Theory', 'Practical', 'Elective'],
                'default'    => 'Theory',
            ],
            'credits' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 3,
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
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subjects');
    } // End of up()

    // Purpose: Reverts the database schema changes for 2026-10-28-000007_ create subjects.
    public function down()
    {
        $this->forge->dropTable('subjects');
    } // End of down()
}