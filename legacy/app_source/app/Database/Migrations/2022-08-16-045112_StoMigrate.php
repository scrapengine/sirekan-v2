<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataSto extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idsto' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nama_sto' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'longitude' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'latitude' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
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
        $this->forge->addKey('idsto', true);
        $this->forge->createTable('datasto');
    }

    public function down()
    {
        $this->forge->dropTable('datasto');
    }
}
