<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AssuranceWanMigrate extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'incident' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'summary' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'owner_group' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'owner' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'external_ticketid' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'customer_segment' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'service_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'service_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'reported_date' => [
                'type'       => 'DATETIME',
                'null' => true,
            ],
            'lapul' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'gaul' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ttr_customer' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ttr_nasional' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ttr_regional' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ttr_witel' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ttr_mitra' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ttr_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'ttr_pending' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'pending_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'status_date' => [
                'type'       => 'DATETIME',
                'null' => true,
            ],
            'resolved_by' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'workzone' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'witel' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'regional' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'actual_solution' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'incident_domain' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'resolved_date' => [
                'type'       => 'DATETIME',
                'null' => true,
            ],
            'jumlah_site_tsel_nossa' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'kategori_site_tsel' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'impacted_site_tsel' => [
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
        $this->forge->addKey('incident', true);
        $this->forge->createTable('assurance_wan');
    }

    public function down()
    {
        $this->forge->dropTable('assurance_wan');
    }
}
// class AssuranceWanMigrate extends Migration
// {
//     public function up()
//     {
//         $this->forge->addField([
//             'incident' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'customer_name' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'contact_name' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'contact_email' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'summary' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'owner_group' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'owner' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'last_update_work_log' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'last_work_log_date' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'count_custinfo' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'last_custinfo' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'assigned_to' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'booking_date' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'assigned_by' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'reported_priority' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'source' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'subsidiary' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'external_ticketid' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'external_ticketstatus' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'segment' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'channel' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'customer_segment' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'customer_type' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'closed_by' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'customer_id' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'service_id' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'service_no' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'service_type' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'top_priority' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'slg' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'technology' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'datek_induk_gamas' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'datek' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'rk_name' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ibooster_alertid' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'induk_gamas' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'related_to_global_issue' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'reported_date' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'lapul' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'gaul' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ttr_customer' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ttr_nasional' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ttr_regional' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ttr_witel' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ttr_mitra' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ttr_agent' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'ttr_pending' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'pending_reason' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'status' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'hasil_ukur' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'osm_resolve_code' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'last_update_ticket' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'status_date' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'resolved_by' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'workzone' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'witel' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'regional' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'incidents_symptom' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'solutions_segment' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'actual_solution' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'incident_domain' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'onu_rx_before_after' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'scc_fisik_inet' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'scc_logic' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'complete_wo_by' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'kode_produk' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'resolved_date' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'jumlah_site_tsel_nossa' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'kategori_site_tsel' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'impacted_site_tsel' => [
//                 'type'       => 'VARCHAR',
//                 'constraint' => '100',
//                 'null' => true,
//             ],
//             'created_at' => [
//                 'type'       => 'DATETIME',
//                 'null' => true,
//             ],
//             'updated_at' => [
//                 'type'       => 'DATETIME',
//                 'null' => true,
//             ],
//             'deleted_at' => [
//                 'type'       => 'DATETIME',
//                 'null' => true,
//             ],
//         ]);
//         $this->forge->addKey('incident', true);
//         $this->forge->createTable('assurance_wan');
//     }

//     public function down()
//     {
//         $this->forge->dropTable('assurance_wan');
//     }
// }
