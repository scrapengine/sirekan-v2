<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class NodebModel extends Model
{
    protected $table          = 'datanodeb';
    protected $primaryKey     = 'idnodeb';
    protected $returnType     = 'object';
    protected $allowedFields  = ['idsto', 'site_id', 'site_name', 'hostname_metro', 'port_metro', 'hostname_olt', 'hostname_ont', 'ip_ont', 'port_onu', 'ont_type', 'serial_number', 'odc', 'odp', 'tikor_site', 'on_air', 'evidence'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    // ...

    function getAll($keyword = null)
    {
        $builder = $this->db->table('datanodeb');
        $builder->select('*')->where('datanodeb.deleted_at', null);
        $builder->select('datanodeb.port_metro as port_metro_nodeb');
        $builder->join('datasto',  'datasto.idsto = datanodeb.idsto');
        $builder->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro');
        $builder->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

        if ($keyword != '') {
            $builder->like('datasto.idsto', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('site_id', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('site_name', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('datametro.hostname_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('datametro.ip_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('datanodeb.port_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('dataolt.port_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('dataolt.hostname_olt', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('dataolt.ip_olt', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('port_onu', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('hostname_ont', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('ip_ont', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('ont_type', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('serial_number', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('odc', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('odp', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('tikor_site', $keyword)->where('datanodeb.deleted_at', null);
        };

        $query   = $builder->get();
        return $query->getResult();
    }
    function getTrash($keyword = null)
    {
        $builder = $this->db->table('datanodeb');
        $builder->select('*')->where('datanodeb.deleted_at !=', null);
        $builder->join('datasto',  'datasto.idsto = datanodeb.idsto');
        $builder->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro');
        $builder->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('site_id', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('site_name', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('datametro.ip_metro', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('dataolt.ip_olt', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_onu', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_ont', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_ont', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ont_type', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('serial_number', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('odc', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('odp', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('tikor_site', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function GetById($id)
    {
        $builder = $this->db->table('datanodeb')
            ->select('*')
            ->where('idnodeb', $id)
            ->select('idnodeb, datanodeb.idsto as idsto_nodeb, site_id, site_name, datanodeb.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, datanodeb.port_metro as port_metro_nodeb, datanodeb.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, serial_number, odc, odp, tikor_site, on_air, evidence')
            ->join('datasto',  'datasto.idsto = datanodeb.idsto')
            ->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

        $query   = $builder->get();
        $result = $query->getResultArray();

        if (!str_contains($result[0]['ont_type'], 'direct')) {
            $builder
                ->select('*')
                ->where('idnodeb', $id)
                ->select('idnodeb, datanodeb.idsto as idsto_nodeb, site_id, site_name, datanodeb.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, datanodeb.port_metro as port_metro_nodeb, datanodeb.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, datanodeb.serial_number as serial_number, odc, odp, tikor_site, on_air, evidence ,CONCAT(dataont.merk ,"-", dataont.type) as ont_type, dataont.merk as merk')
                ->join('dataont',  'dataont.serial_number = datanodeb.serial_number')
                ->join('datasto',  'datasto.idsto = datanodeb.idsto')
                ->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

            $query   = $builder->get();
            $result = $query->getResultArray();
        }

        return $result;
    }
}
