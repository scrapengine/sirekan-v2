<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class JobasrModel extends Model
{
    protected $table          = 'job_assurance';
    protected $primaryKey     = 'idjob';
    protected $returnType     = 'object';
    protected $allowedFields  = ['idjob', 'idasr', 'idnaker'];
    // protected $useTimestamps  = true;
    // protected $useSoftDeletes = true;

    // ...

    function getJob($id)
    {
        $builder = $this->db->table('job_assurance');
        $builder->select('*')->where('job_assurance.idasr', $id);
        $builder->join('assurance_wan',  'assurance_wan.idasr = job_assurance.idasr');
        $builder->join('naker',  'naker.idnaker = job_assurance.idnaker');

        $query   = $builder->get();
        return $query->getResult();
    }
}
