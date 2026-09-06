<?php

namespace App\Models;

use CodeIgniter\Model;

use function PHPUnit\Framework\isNull;

class StoModel extends Model
{
    protected $table            = 'datasto';
    protected $primaryKey       = 'idsto';
    protected $returnType       = 'object';
    protected $allowedFields    = ['idsto', 'nama_sto', 'longitude', 'latitude', 'alamat', 'witel'];

    protected $useSoftDeletes   = true;
    protected $useTimestamps = true;



    function getAll($keyword = null)
    {
        $array = array('datasto.deleted_at' => null, 'idsto !=' => '-');
        $builder = $this->db->table('datasto')->select('*')->where($array);
        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('nama_sto', $keyword)->where($array);
            $builder->orLike('longitude', $keyword)->where($array);
            $builder->orLike('latitude', $keyword)->where($array);
            $builder->orLike('alamat', $keyword)->where($array);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function getTrash($keyword = null)
    {
        $builder = $this->db->table('dataolt')->select('*')->where('datasto.deleted_at !=', null);
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('nama_sto', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('longitude', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('latitude', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('alamat', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
        };

        $query   = $builder->get();
        return $query->getResult();
    }


    // pagination
    function getPaginated($num, $keyword = null)
    {
        $builder = $this->builder();
        $builder->select('*')->where('datasto.deleted_at', null);
        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('datasto.deleted_at', null);
            $builder->orLike('nama_sto', $keyword)->where('datasto.deleted_at', null);
            $builder->orLike('longitude', $keyword)->where('datasto.deleted_at', null);
            $builder->orLike('latitude', $keyword)->where('datasto.deleted_at', null);
            $builder->orLike('alamat', $keyword)->where('datasto.deleted_at', null);
        };
        return [
            'dataSto' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }


    // pagination Trash
    function getPaginatedTrash($num, $keyword = null)
    {
        $builder = $this->onlyDeleted()->builder();

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('nama_sto', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('longitude', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('latitude', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
            $builder->orLike('alamat', $keyword)->where('datasto.deleted_at IS NOT NULL', null, false);
        };

        return [
            'dataSto' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }
}
