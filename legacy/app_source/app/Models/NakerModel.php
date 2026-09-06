<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Database\MySQLi\Builder;
use CodeIgniter\Database\Query;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

use function GuzzleHttp\Promise\queue;

use function PHPUnit\Framework\isNull;

class NakerModel extends Model
{
    protected $table            = 'naker';
    protected $primaryKey       = 'idnaker';
    protected $returnType       = 'object';
    protected $allowedFields    = ['nik', 'nama', 'divisi', 'jobdesk', 'no_hp', 'idsto', 'labor'];

    protected $useSoftDeletes   = true;
    protected $useTimestamps = true;



    function getAll($keyword = null)
    {
        $array = array('naker.deleted_at' => null);
        $builder = $this->db->table('naker')->select('*')->where($array);
        if ($keyword != '') {
            $builder->like('nik', $keyword)->where($array);
            $builder->orLike('nama', $keyword)->where($array);
            $builder->orLike('divisi', $keyword)->where($array);
            $builder->orLike('jobdesk', $keyword)->where($array);
            $builder->orLike('no_hp', $keyword)->where($array);
            $builder->orLike('idsto', $keyword)->where($array);
            $builder->orLike('labor', $keyword)->where($array);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function getTrash($keyword = null)
    {
        $builder = $this->db->table('naker')->select('*')->where('naker.deleted_at !=', null);

        if ($keyword != '') {
            $builder->like('nik', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('nama', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('divisi', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('jobdesk', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('no_hp', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('labor', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
        };

        $query   = $builder->get();
        return $query->getResult();
    }


    // pagination
    function getPaginated($num, $keyword = null)
    {
        $builder = $this->builder();
        $array = array('naker.deleted_at' => null);
        $builder->select('*')->where('naker.deleted_at', null);
        if ($keyword != '') {
            $builder->like('nik', $keyword)->where($array);
            $builder->orLike('nama', $keyword)->where($array);
            $builder->orLike('divisi', $keyword)->where($array);
            $builder->orLike('jobdesk', $keyword)->where($array);
            $builder->orLike('no_hp', $keyword)->where($array);
            $builder->orLike('idsto', $keyword)->where($array);
            $builder->orLike('labor', $keyword)->where($array);
        };
        return [
            'naker' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }


    // pagination Trash
    function getPaginatedTrash($num, $keyword = null)
    {
        $builder = $this->onlyDeleted()->builder();

        if ($keyword != '') {
            $builder->like('nik', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('nama', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('divisi', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('jobdesk', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('no_hp', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            $builder->orLike('labor', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
        };

        return [
            'naker' => $this->paginate($num),
            'pager' => $this->pager,
        ];
    }
}
