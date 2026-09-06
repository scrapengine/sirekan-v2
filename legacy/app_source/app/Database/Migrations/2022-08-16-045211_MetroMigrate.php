<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataMetro extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idsto' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'hostname_metro' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'ip_metro' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('hostname_metro', true);
        $this->forge->addForeignKey('idsto', 'datasto', 'idsto');
        $this->forge->createTable('datametro');
    }

    public function down()
    {
        $this->forge->dropForeignKey('idsto', 'datametro_idsto_foreign');
        $this->forge->dropTable('datametro');
    }
}
