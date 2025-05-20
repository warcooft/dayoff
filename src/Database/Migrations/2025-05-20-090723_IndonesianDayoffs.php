<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IndonesianDayOffs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'null'           => false,
            ],
            'date'       => ['type' => 'DATE', 'null' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 225, 'null' => true],

            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $attributes = ['ENGINE' => 'InnoDB'];

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('indonesian_dayoffs', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('indonesian_dayoffs', true, true);
    }
}
