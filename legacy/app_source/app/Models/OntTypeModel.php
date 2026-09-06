<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class OntTypeModel extends Model
{
    protected $table          = 'ont_type';
    protected $primaryKey     = 'idtype';
    protected $returnType     = 'object';
    protected $allowedFields  = ['merk', 'type'];
    // protected $useTimestamps  = true;
    // protected $useSoftDeletes = true;

    // ...

    function getAll()
    {
        $builder = $this->db->table('ont_type');
        $builder->select('*');
        $builder->groupBy('merk');

        $query   = $builder->get();
        return $query->getResult();
    }

}
