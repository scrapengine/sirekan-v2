<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class CactiModel extends Model
{
    protected $table          = 'cacti';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $allowedFields  = ['id', 'graph_id', 'idnodeb'];
    // protected $useTimestamps  = true;
    // protected $useSoftDeletes = true;

    // ...

    function getId($id)
    {
        $builder = $this->db->table('cacti');
        $builder->select('*')->where('cacti.idnodeb', $id);
        $builder->join('datanodeb',  'datanodeb.idnodeb = cacti.idnodeb');

        $query   = $builder->get();
        return $query->getResultArray();
    }
}
