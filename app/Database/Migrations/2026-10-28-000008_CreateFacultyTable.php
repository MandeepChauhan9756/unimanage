<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFacultyTable extends Migration
{
    // Purpose: Creates or updates the database schema for 2026-10-28-000008_ create faculty.
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'employee_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'designation' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'qualification' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'joining_date' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('faculty');
    } // End of up()

    // Purpose: Reverts the database schema changes for 2026-10-28-000008_ create faculty.
    public function down()
    {
        $this->forge->dropTable('faculty');
    } // End of down()
}