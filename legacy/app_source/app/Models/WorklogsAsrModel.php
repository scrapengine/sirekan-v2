<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Model;

class WorklogsAsrModel extends Model
{
    protected $table          = 'worklogs_asr';
    protected $primaryKey     = 'idlog';
    protected $returnType     = 'object';
    protected $allowedFields  = ['idasr', 'record', 'created_by', 'owner_group', 'date', 'summary'];
    // protected $useTimestamps  = true;
    // protected $useSoftDeletes = true;

    // ...

    function getWorklogs($id)
    {
        $builder = $this->db->table('worklogs_asr');
        $builder->select('*, worklogs_asr.owner_group as og , worklogs_asr.summary as summ')->where('worklogs_asr.idasr', $id);
        $builder->join('assurance_wan',  'assurance_wan.idasr = worklogs_asr.idasr');

        $query   = $builder->get();
        return $query->getResult();
    }
}
