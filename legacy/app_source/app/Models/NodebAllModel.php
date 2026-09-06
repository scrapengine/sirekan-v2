<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class NodebAllModel extends Model
{
    protected $table          = 'datanodeb_all';
    protected $primaryKey     = 'site_id_all';
    protected $returnType     = 'object';
    protected $allowedFields  = ['site_id_all', 'site_name_all', 'nsa', 'kabupaten', 'kecamatan', 'kelurahan', 'lat_long', 'tech', 'band_2g', 'band_3g', 'band_4g', 'band_updated', 'tp', 's_power', 'transport', 'sto'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    // ...

    function getAll($keyword = null)
    {
        $builder = $this->db->table('datanodeb_all');
        $builder->select('*')->where('datanodeb_all.deleted_at', null);
        $builder->select('datanodeb_all.port_metro as port_metro_nodeb');
        $builder->join('datasto',  'datasto.idsto = datanodeb_all.idsto');
        $builder->join('datametro',  'datametro.hostname_metro = datanodeb_all.hostname_metro');
        $builder->join('dataolt',  'dataolt.hostname_olt = datanodeb_all.hostname_olt');

        if ($keyword != '') {
            $builder->like('datasto.idsto', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('site_id', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('site_name', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('datametro.hostname_metro', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('datametro.ip_metro', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('datanodeb_all.port_metro', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('dataolt.port_metro', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('dataolt.hostname_olt', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('dataolt.ip_olt', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('port_onu', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('hostname_ont', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('ip_ont', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('ont_type', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('serial_number', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('odc', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('odp', $keyword)->where('datanodeb_all.deleted_at', null);
            $builder->orLike('tikor_site', $keyword)->where('datanodeb_all.deleted_at', null);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function getRadio()
    {
        $builder = $this->db->table('datanodeb_all');
        $builder->select('*')->where('deleted_at', null)->where('transport !=', 'Metro-E Telkom')->orWhere('transport', null);

        $query   = $builder->get();
        return $query->getResult();
    }

    function GetById($id)
    {
        $builder = $this->db->table('datanodeb_all')
            ->select('*')
            ->where('idnodeb', $id);

        $query   = $builder->get();
        $result = $query->getResultArray();

        return $result;
    }
}
