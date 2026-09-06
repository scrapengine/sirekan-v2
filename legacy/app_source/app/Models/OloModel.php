<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class OloModel extends Model
{
    protected $table          = 'dataolo';
    protected $primaryKey     = 'idolo';
    protected $returnType     = 'object';
    protected $allowedFields  = ['service_id', 'idsto', 'layanan', 'nama', 'alamat', 'tag_lokasi', 'hostname_metro', 'port_metro', 'hostname_olt', 'port_onu', 'hostname_ont', 'ip_ont', 'ont_type', 'serial_number', 'vlan', 'odc', 'odp', 'desc', 'evidence'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    // ...

    function getAll()
    // function getAll($fromdate, $untildate, $keyword = null)
    {
        $builder = $this->db->table('dataolo')
            ->select('idolo, service_id, dataolo.idsto as idsto_olo, dataolo.hostname_metro as hostname_metro_olo, ip_metro, dataolt.port_metro as port_metro_olt, dataolo.port_metro as port_metro_olo, dataolo.hostname_olt as hostname_olt_olo, ip_olt, layanan, nama, dataolo.alamat as alamat_olo, tag_lokasi, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, odc, odp, desc, evidence')
            // ->where('dataolo.deleted_at', null)
            // ->where('tanggal BETWEEN "' . strval($fromdate) . '" and "' . strval($untildate) . '"')
            // ->where('tanggal >=', strval($fromdate))
            // ->where('tanggal <=', strval($untildate))
            ->join('datasto',  'datasto.idsto = dataolo.idsto')
            ->join('datametro',  'datametro.hostname_metro = dataolo.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = dataolo.hostname_olt');


        $query   = $builder->get();
        return $query->getResult();
    }

    function GetById($id)
    {
        $builder = $this->db->table('dataolo')
            ->select('idolo, service_id, dataolo.idsto as idsto_olo, dataolo.hostname_metro as hostname_metro_olo, ip_metro, dataolt.port_metro as port_metro_olt, dataolo.port_metro as port_metro_olo, dataolo.hostname_olt as hostname_olt_olo, ip_olt, layanan, nama, dataolo.alamat as alamat_olo, tag_lokasi, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, odc, odp, desc, evidence')
            ->where('idolo', $id)
            ->join('datasto',  'datasto.idsto = dataolo.idsto')
            ->join('datametro',  'datametro.hostname_metro = dataolo.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = dataolo.hostname_olt');

        $query   = $builder->get();
        return $query->getResultArray();
    }

}
