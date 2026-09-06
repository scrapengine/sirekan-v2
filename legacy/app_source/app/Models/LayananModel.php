<?php

namespace App\Models;

use CodeIgniter\Model;

use function PHPUnit\Framework\isNull;

class LayananModel extends Model
{
    protected $table            = 'layanan';
    protected $primaryKey       = 'idlayanan';
    protected $returnType       = 'object';
    protected $allowedFields    = ['idlayanan', 'layanan', 'desc_layanan'];
}
