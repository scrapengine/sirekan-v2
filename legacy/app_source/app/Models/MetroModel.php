<?php

namespace App\Models;

use CodeIgniter\Model;

class MetroModel extends Model
{
    protected $table            = 'datametro';
    protected $primaryKey       = 'hostname_metro';
    protected $returnType       = 'object';
    protected $allowedFields    = ['idsto', 'hostname_metro', 'ip_metro'];

    // Dates
    protected $useSoftDeletes   = true;
    protected $useTimestamps = true;


    function getAll($keyword = null)
    {
        $builder = $this->db->table('datametro');
        $array = array('datametro.deleted_at' => null, 'hostname_metro !=' => '-');
        $builder->select('*')->where($array);
        $builder->join('datasto',  'datasto.idsto = datametro.idsto');

        if ($keyword != '') {
            $builder->like('datasto.idsto', $keyword)->where($array);
            $builder->orLike('hostname_metro', $keyword)->where($array);
            $builder->orLike('ip_metro', $keyword)->where($array);
        };

        $query   = $builder->get();
        return $query->getResult();
    }


    function getTrash($keyword = null)
    {
        $builder = $this->db->table('datametro');
        $builder->select('*')->where('datametro.deleted_at !=', null);
        $builder->join('datasto',  'datasto.idsto = datametro.idsto');

        if ($keyword != '') {
            $builder->like('datasto.idsto', $keyword)->where('datametro.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('datametro.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('datametro.deleted_at IS NOT NULL', null, false);
        };

        $query   = $builder->get();
        return $query->getResult();
    }


    // pagination
    function getPaginated($num, $keyword = null)
    {
        $builder = $this->builder();
        $builder->select('*')->where('datametro.deleted_at', null);
        $builder->join('datasto',  'datasto.idsto = datametro.idsto');

        if ($keyword != '') {
            $builder->like('datasto.idsto', $keyword)->where('datametro.deleted_at', null);
            $builder->orLike('hostname_metro', $keyword)->where('datametro.deleted_at', null);
            $builder->orLike('ip_metro', $keyword)->where('datametro.deleted_at', null);
        };

        return [
            'dataMetro' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }


    // pagination Trash
    function getPaginatedTrash($num, $keyword = null)
    {
        $builder = $this->onlyDeleted()->builder();
        $builder->join('datasto',  'datasto.idsto = datametro.idsto');

        if ($keyword != '') {
            $builder->like('datasto.idsto', $keyword)->where('datametro.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('datametro.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('datametro.deleted_at IS NOT NULL', null, false);
        };

        return [
            'dataMetro' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }
}
