<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataNodeb extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idnodeb' => [
                'type'           => 'BIGINT',
                'constraint'     => 25,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'site_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'site_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'portmetro' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'hostname_ont' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ip_ont' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'port_onu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ont_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'odc' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'odp' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'tikor_site' => [
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
            'hostname_olt' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('idnodeb', true);
        $this->forge->addForeignKey('idsto', 'datasto', 'idsto');
        $this->forge->addForeignKey('hostname_olt', 'dataolt', 'hostname_olt');
        $this->forge->addForeignKey('hostname_metro', 'datametro', 'hostname_metro');
        $this->forge->createTable('datanodeb');
    }

    public function down()
    {
        $this->forge->dropForeignKey('hostname_metro', 'datanodeb_hostname_metro_foreign');
        $this->forge->dropForeignKey('hostname_olt', 'datanodeb_hostname_olt_foreign');
        $this->forge->dropForeignKey('idsto', 'datanodeb_idsto_foreign');
        $this->forge->dropTable('datanodeb');
    }
}
