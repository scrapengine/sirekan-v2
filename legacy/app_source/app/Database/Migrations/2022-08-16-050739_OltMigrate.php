<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataOlt extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'witel' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'idsto' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'hostname_metro' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'port_metro' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ip_olt' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'hostname_olt' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'port_olt' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'platform' => [
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
        $this->forge->addKey('hostname_olt', true);
        $this->forge->addForeignKey('idsto', 'datasto', 'idsto');
        $this->forge->addForeignKey('hostname_metro', 'datametro', 'hostname_metro');
        $this->forge->createTable('dataolt');
    }

    public function down()
    {
        $this->forge->dropForeignKey('idsto', 'datametro_idsto_foreign');
        $this->forge->dropForeignKey('hostname_metro', 'dataolt_hostname_metro_foreign');
        $this->forge->dropTable('dataolt');
    }
}
