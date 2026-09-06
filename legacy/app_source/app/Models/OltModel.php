<?php

namespace App\Models;

use CodeIgniter\Model;

use function PHPUnit\Framework\isNull;

class OltModel extends Model
{
    protected $table            = 'dataolt';
    protected $primaryKey       = 'hostname_olt';
    protected $useSoftDeletes   = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['witel', 'idsto', 'port_metro', 'ip_olt', 'hostname_olt', 'port_olt', 'platform', 'hostname_metro', 'type_olt'];

    // Dates
    protected $useTimestamps = true;

    function getAll($keyword = null)
    {
        $builder = $this->db->table('dataolt');
        $array = array('dataolt.deleted_at' => null, 'hostname_olt !=' => 'DIRECT_METRO', 'datametro.deleted_at' => null);
        $array2 = array('hostname_olt !=' => '-');
        $builder->select('*')->where($array)->where($array2);
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');

        if ($keyword != '') {
            $builder->like('witel', $keyword)->where($array)->where($array2);
            $builder->orLike('port_metro', $keyword)->where($array)->where($array2);
            $builder->orLike('ip_olt', $keyword)->where($array)->where($array2);
            $builder->orLike('hostname_olt', $keyword)->where($array)->where($array2);
            $builder->orLike('port_olt', $keyword)->where($array)->where($array2);
            $builder->orLike('platform', $keyword)->where($array)->where($array2);
        };

        $query   = $builder->get();
        return $query->getResult();
    }
    function getTrash($keyword = null)
    {
        $builder = $this->db->table('dataolt');
        $builder->select('*')->where('dataolt.deleted_at !=', null);
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');

        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('platform', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
        };

        $query   = $builder->get();
        return $query->getResult();
    }


    // pagination
    function getPaginated($num, $keyword = null)
    {
        $builder = $this->builder();
        $array = array('dataolt.deleted_at' => null, 'hostname_olt !=' => 'DIRECT_METRO', 'datametro.deleted_at' => null);
        $builder->select('*')->where($array);
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where($array);
            // $builder->orLike('idsto', $keyword)->where($array);
            $builder->orLike('ip_metro', $keyword)->where($array);
            // $builder->orLike('hostname_metro', $keyword)->where($array);
            $builder->orLike('port_metro', $keyword)->where($array);
            $builder->orLike('ip_olt', $keyword)->where($array);
            $builder->orLike('hostname_olt', $keyword)->where($array);
            $builder->orLike('port_olt', $keyword)->where($array);
            $builder->orLike('platform', $keyword)->where($array);
        };
        // $dataOlt =  $this->paginate($num);
        // $pager = $this->pager;
        // return $dataOlt;
        return [
            'dataOlt' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }


    // pagination Trash
    function getPaginatedTrash($num, $keyword = null)
    {
        $builder = $this->onlyDeleted()->builder();
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('platform', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
        };

        return [
            'dataOlt' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }
}
