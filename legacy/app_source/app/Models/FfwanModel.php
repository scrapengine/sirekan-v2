<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class FfwanModel extends Model
{
    protected $table          = 'ffwan';
    protected $primaryKey     = 'idff';
    protected $returnType     = 'object';
    protected $allowedFields  = ['idsto', 'tanggal', 'tanggal_install', 'tanggal_ps', 'layanan', 'no_order', 'nama', 'alamat', 'tag_lokasi', 'order_type', 'hostname_metro', 'port_metro', 'hostname_olt', 'port_onu', 'hostname_ont', 'ip_ont', 'ont_type', 'serial_number', 'vlan', 'bandwidth', 'odc', 'odp', 'p_tarikan', 'lan', 'status', 'desc', 'divisi', 'evidence','service_id'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    // ...

    function getAll()
    // function getAll($fromdate, $untildate, $keyword = null)
    {
        $builder = $this->db->table('ffwan')
            ->select('idff, datasto.idsto, tanggal, tanggal_install, tanggal_ps, layanan, no_order, nama, ffwan.alamat, tag_lokasi, order_type, datametro.hostname_metro ,datametro.ip_metro , dataolt.port_metro, ffwan.port_metro, dataolt.hostname_olt, dataolt.ip_olt, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, bandwidth, odc, odp, p_tarikan, lan, status, desc, service_id')
            ->join('datasto',  'datasto.idsto = ffwan.idsto')
            ->join('datametro',  'datametro.hostname_metro = ffwan.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = ffwan.hostname_olt');

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

    public function rangeDate($first_date, $second_date)
    {
        $builder = $this->db->table('ffwan');
        $builder->select('*')
            ->from('tanggal')
            ->where("DATE_FORMAT(date,'%Y-%m-%d') >='$first_date'")
            ->where("DATE_FORMAT(date,'%Y-%m-%d') <='$second_date'");
        $query = $builder->get();
        return $query->getResult();
    }
    function countFfNow($divisi)
    {
        $db = \Config\Database::connect();
        $status = ['CLOSE', 'REJECT'];
        $array = array(
            'deleted_at' => null,
        );
        $builder = $db->table('ffwan')->select('*')->where($array)->whereNotIn('status', $status);
        if ($divisi != "All") {
            $builder->where('ffwan.divisi', $divisi);
        }

        return $builder->countAllResults();
    }

    function GetById($id)
    {
        $builder = $this->db->table('ffwan')
            ->select('idff, datasto.idsto, tanggal, tanggal_install, tanggal_ps, layanan, no_order, nama, ffwan.alamat, tag_lokasi, order_type, datametro.hostname_metro ,datametro.ip_metro , dataolt.port_metro, ffwan.port_metro, dataolt.hostname_olt, dataolt.ip_olt, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, bandwidth, odc, odp, p_tarikan, lan, status, desc, divisi, evidence, service_id')
            ->where('idff', $id)
            ->join('datasto',  'datasto.idsto = ffwan.idsto')
            ->join('datametro',  'datametro.hostname_metro = ffwan.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = ffwan.hostname_olt');

        $query   = $builder->get();
        return $query->getResultArray();
    }



    function countFf($divisi, $bulan)
    {
        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(ffwan.tanggal, "%Y-%m")' => $bulan
        );
        $builder = $db->table('ffwan')->select('*')->where($array);
        if ($divisi != "All") {
            $builder->where('ffwan.divisi', $divisi);
        }

        return $builder->countAllResults();
    }

    function countAllFf()
    {
        $db = \Config\Database::connect();
        $array = array('deleted_at' => null, 'ffwan.divisi' => 'WAN', 'DATE_FORMAT(ffwan.tanggal, "%Y-%m")' => '2022-10');
        $builder = $db->table('ffwan')->select('*')->where($array);
        return $builder->countAllResults();
    }

    function countFfOpen($divisi, $bulan)
    {
        $db = \Config\Database::connect();
        $status = ['OPEN'];
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(ffwan.tanggal, "%Y-%m")' => $bulan,
        );
        $builder = $db->table('ffwan')->select('*')->where($array)->whereIn('status', $status);
        if ($divisi != "All") {
            $builder->where('ffwan.divisi', $divisi);
        }

        return $builder->countAllResults();
    }

    function countFfClose($divisi, $bulan)
    {
        $db = \Config\Database::connect();
        $status = ['CLOSE'];
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(ffwan.tanggal, "%Y-%m")' => $bulan,
        );
        $builder = $db->table('ffwan')->select('*')->where($array)->whereIn('status', $status);
        if ($divisi != "All") {
            $builder->where('ffwan.divisi', $divisi);
        }

        return $builder->countAllResults();
    }

    function countFfReject($divisi, $bulan)
    {
        $db = \Config\Database::connect();
        $status = ['REJECT'];
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(ffwan.tanggal, "%Y-%m")' => $bulan,
        );
        $builder = $db->table('ffwan')->select('*')->where($array)->whereIn('status', $status);
        if ($divisi != "All") {
            $builder->where('ffwan.divisi', $divisi);
        }

        return $builder->countAllResults();
    }

    
    function GetServiceNo($id)
    {
        $builder = $this->db->table('ffwan')
            ->select('*')
            ->like('service_id', $id)
            ->orderBy('tanggal','ASC');

        $query   = $builder->get();
        return $query->getResult();
    }

}
