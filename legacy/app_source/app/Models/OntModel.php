<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class OntModel extends Model
{
    protected $table          = 'dataont';
    protected $primaryKey     = 'idont';
    protected $returnType     = 'object';
    protected $allowedFields  = ['idsto', 'merk', 'type', 'serial_number', 'status', 'desc', 'allocation', 'installed', 'received', 'return'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    // ...

    function getAll($keyword = null)
    {
        $builder = $this->db->table('dataont');
        $builder->select('*')->where('dataont.deleted_at', null);

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('type', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('serial_number', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('status', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('desc', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('installed', $keyword)->where('dataont.deleted_at', null);
        };

        $query   = $builder->get();
        return $query->getResult();
    }


    function getStock($keyword = null)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null);
        $builder->select('*')->where($array);

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('merk', $keyword)->where($array);
            $builder->orLike('type', $keyword)->where($array);
            $builder->orLike('serial_number', $keyword)->where($array);
            $builder->orLike('status', $keyword)->where($array);
            $builder->orLike('desc', $keyword)->where($array);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function getInstalled($keyword = null)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.return' => null);
        $builder->select('*')->where($array);

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('merk', $keyword)->where($array);
            $builder->orLike('type', $keyword)->where($array);
            $builder->orLike('serial_number', $keyword)->where($array);
            $builder->orLike('status', $keyword)->where($array);
            $builder->orLike('desc', $keyword)->where($array);
            $builder->orLike('installed', $keyword)->where($array);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function getReturn($keyword = null)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null);
        $builder->select('*')->where($array);

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('merk', $keyword)->where($array);
            $builder->orLike('type', $keyword)->where($array);
            $builder->orLike('serial_number', $keyword)->where($array);
            $builder->orLike('status', $keyword)->where($array);
            $builder->orLike('desc', $keyword)->where($array);
        };

        $query   = $builder->get();
        return $query->getResult();
    }


    function searchSn($keyword = null)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.serial_number' => $keyword);
        $builder->select('*')->where($array);

        $query   = $builder->get();
        return $query->getResult();
    }


    function getTrash($keyword = null)
    {
        $builder = $this->db->table('dataont');
        $builder->select('*')->where('dataont.deleted_at !=', null);

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('dataont.deleted_at IS NOT NULL', null, false);
            $builder->orLike('merk', $keyword)->where('dataont.deleted_at IS NOT NULL', null, false);
            $builder->orLike('type', $keyword)->where('dataont.deleted_at IS NOT NULL', null, false);
            $builder->orLike('serial_number', $keyword)->where('dataont.deleted_at IS NOT NULL', null, false);
            $builder->orLike('status', $keyword)->where('dataont.deleted_at IS NOT NULL', null, false);
            $builder->orLike('desc', $keyword)->where('dataont.deleted_at IS NOT NULL', null, false);
            $builder->orLike('installed', $keyword)->where('dataont.deleted_at IS NOT NULL', null, false);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function insertsn($data, $s)
    {
        $sql = "INSERT INTO dataont (idsto, merk, type, serial_number, status, desc, allocation, installed, received, return) VALUES ($data, $s)";
        $this->db->query($sql);
        return TRUE;
    }

    function getGroup($field)
    {
        $builder = $this->db->table('dataont')->join('ont_type',  'ont_type.type = dataont.type');
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null);
        return $builder->select($field)->where($array)->orderBy('idtype','ASC')->distinct()->get();
    }

    function jumlahAsr($sto,$type,$allocation)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');
        // $builder->select('*')->where($array);
        $builder->join('datasto',  'datasto.idsto = dataont.idsto');
        $builder->selectCount('serial_number', 'jumlah');
        $builder->where($array)->where($sto)->where($type)->where($allocation);
        $query   = $builder->get();
        return $query;
    }
    

    function jumlahFf($type,$allocation)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');
        // $builder->select('*')->where($array);
        $builder->join('datasto',  'datasto.idsto = dataont.idsto');
        $builder->selectCount('serial_number', 'jumlah');
        $builder->where($array)->where($type)->where($allocation);
        $query   = $builder->get();
        return $query;
    }
    

    function jumlahOlo($type,$allocation)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');
        // $builder->select('*')->where($array);
        $builder->join('datasto',  'datasto.idsto = dataont.idsto');
        $builder->selectCount('serial_number', 'jumlah');
        $builder->where($array)->where($type)->where($allocation);
        $query   = $builder->get();
        return $query;
    }
    
    

    function jumlahRusak($type)
    {
        $builder = $this->db->table('dataont');
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'RUSAK');
        // $builder->select('*')->where($array);
        $builder->join('datasto',  'datasto.idsto = dataont.idsto');
        $builder->selectCount('serial_number', 'jumlah');
        $builder->where($array)->where($type);
        $query   = $builder->get();
        return $query;
    }
    
    function totalWhere($type)
    {
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null);
        return $this->selectCount('serial_number', 'total')->where($array)->where($type)->first();
    }
    
    function totalWhereHorizontal($sto,$allocation)
    {
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');
        return $this->selectCount('serial_number', 'total')->where($array)->where($sto)->where($allocation)->first();
    }

    function totalWhereFfOlo($allocation)
    {
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');
        return $this->selectCount('serial_number', 'total')->where($array)->where($allocation)->first();
    }

    function totalWhereRusak()
    {
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'RUSAK');
        return $this->selectCount('serial_number', 'total')->where($array)->first();
    }

    function totalWhereAll()
    {
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null);
        return $this->selectCount('serial_number', 'total')->where($array)->first();
    }
}
