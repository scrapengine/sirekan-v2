<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataOnt extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idont' => [
                'type'           => 'BIGINT',
                'constraint'     => 25,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'idsto' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'desc' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'usage' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'received' => [
                'type'       => 'DATE',
                'null' => true,
            ],
            'return' => [
                'type'       => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('idont', true);
        $this->forge->createTable('dataont');
    }

    public function down()
    {
        $this->forge->dropTable('dataont');
    }
}
