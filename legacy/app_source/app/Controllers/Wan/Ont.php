<?php

namespace App\Controllers\Wan;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\RESTful\ResourcePresenter;
use App\Models\OntModel;
use App\Models\StoModel;
use App\Models\OntTypeModel;
use Config\Services;
use DateTimeZone;
use \Hermawan\DataTables\DataTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Ont extends ResourceController
{
    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    protected $helpers = ['custom'];
    public function __construct()
    {
        $this->dataOnt = new OntModel();
        $this->dataSto = new StoModel();
        $this->dataOntType = new OntTypeModel();
    }


    public function ontTotal()
    {

        $temp = [];
        $byType = [];
        $i = 0;
        $getTypeOnt = $this->dataOnt->getGroup('dataont.type')->getResultArray();

        foreach ($this->dataSto->getAll() as $idsto) :
            $temp[$i]['idsto'] = $idsto->idsto;
            // $temp[$i] = 'Total';
            // foreach ($this->dataSto->getAll() as $idsto) :
            //     $temp[$i]['idsto'] = $this->dataOnt->totalWhereHorizontal(['idsto' => $idsto->idsto])->total;

            // endforeach;

            foreach ($getTypeOnt as $typeOnt) :
                $tempType = $this->dataOnt->jumlahAsr(['dataont.idsto' => $idsto->idsto], ['type' => $typeOnt['type']], ['allocation' => 'ASSURANCE'])->getRowArray();

                $temp[$i][$typeOnt['type']] = $tempType['jumlah'];

            endforeach;

            array_push($byType, $temp[$i]);
            $i++;

        endforeach;
        // ==========================================
        $temp[$i]['idsto'] = 'FULFILLMENT';
        foreach ($getTypeOnt as $typeOnt) :
            $tempType = $this->dataOnt->jumlahFf(['type' => $typeOnt['type']], ['allocation' => 'FULFILLMENT'])->getRowArray();

            $temp[$i][$typeOnt['type']] = $tempType['jumlah'];
        endforeach;
        array_push($byType, $temp[$i]);

        // ==========================================
        $temp[$i]['idsto'] = 'OLO';
        foreach ($getTypeOnt as $typeOnt) :
            $tempType = $this->dataOnt->jumlahFf(['type' => $typeOnt['type']], ['allocation' => 'OLO'])->getRowArray();

            $temp[$i][$typeOnt['type']] = $tempType['jumlah'];
        endforeach;
        array_push($byType, $temp[$i]);

        // ==========================================
        $temp[$i]['idsto'] = 'RUSAK';
        foreach ($getTypeOnt as $typeOnt) :
            $tempType = $this->dataOnt->jumlahRusak(['type' => $typeOnt['type']])->getRowArray();

            $temp[$i][$typeOnt['type']] = $tempType['jumlah'];
        endforeach;
        array_push($byType, $temp[$i]);

        // ==========================================
        $temp[$i]['idsto'] = 'Total';
        foreach ($getTypeOnt as $typeOnt) :
            $temp[$i][$typeOnt['type']] = $this->dataOnt->totalWhere(['type' => $typeOnt['type']])->total;

        endforeach;
        array_push($byType, $temp[$i]);

        //total samping

        $temp_t = [];
        $byType_t = [];
        $i = 0;
        $getTypeOnt = $this->dataOnt->getGroup('dataont.type')->getResultArray();
        foreach ($this->dataSto->getAll() as $idsto) :
            $temp_t = $this->dataOnt->totalWhereHorizontal(['idsto' => $idsto->idsto], ['allocation' => 'ASSURANCE'])->total;

            array_push($byType_t, $temp_t);

        endforeach;

        $temp_t = $this->dataOnt->totalWhereFfOlo(['allocation' => 'FULFILLMENT'])->total;
        array_push($byType_t, $temp_t);

        $temp_t = $this->dataOnt->totalWhereFfOlo(['allocation' => 'OLO'])->total;
        array_push($byType_t, $temp_t);

        $temp_t = $this->dataOnt->totalWhereRusak()->total;
        array_push($byType_t, $temp_t);

        $temp_t = $this->dataOnt->totalWhereAll()->total;
        array_push($byType_t, $temp_t);

        //combine 2 array
        $zipped = array_map(null, $byType, $byType_t);

        $data = [
            'title' => 'TotalONT',
            'pivot' => $zipped,
            'total' => $byType_t,
            'type' => $getTypeOnt,
        ];
        return $this->respond($data, 200);
    }



    public function searchSn()
    {

        $this->db      = \Config\Database::connect();
        $sn = $this->request->getGet('s');
        $sto = $this->request->getGet('sto');
        $alokasi = $this->request->getGet('al');
        $status = $this->request->getGet('st');
        $builder = $this->db->table('dataont');
        if ($sto == null && $alokasi == null && $status == null) {
            $array = array('dataont.deleted_at' => null, 'dataont.serial_number' => $sn);
            $builder->select('idont, datanodeb.idsto as idsto_n, dataont.idsto as idsto, merk, type, dataont.serial_number, status, desc, allocation, installed, received, return, dataont.created_at, dataont.updated_at')
                ->join('datanodeb',  'datanodeb.serial_number = dataont.serial_number', 'left')
                ->where($array)
                ->groupBy('idont');

            $query   = $builder->get();
            $dataOnt = $query->getResult();

            $data = array();

            foreach ($dataOnt as $d) {
                $idsto = "";
                if ($d->idsto_n != null) {
                    $idsto = $d->idsto_n;
                } else {
                    $idsto = $d->idsto;
                }


                $data[] = array(
                    "id" => $d->idont,
                    "idsto" => $idsto,
                    "merk" => $d->merk,
                    "type" => $d->type,
                    "serial_number" => $d->serial_number,
                    "status" => $d->status,
                    "desc" => $d->desc,
                    "allocation" => $d->allocation,
                    "installed" => $d->installed,
                    "received" => $d->received,
                    "return" => $d->return,
                    "created_at" => $d->created_at,
                    "updated_at" => $d->updated_at,
                );
            }
            
        }
        if ($sn == null && $alokasi == null && $status == null) {
            $array = array('dataont.deleted_at' => null, 'dataont.idsto' => $sto, 'dataont.installed' => null, 'dataont.return' => null);
            $builder->select('idsto, merk, type, dataont.serial_number, status, desc, allocation, installed, received, return, dataont.created_at, dataont.updated_at')
                ->where($array)->orderBy('status', 'asc')->orderBy('allocation','asc');
            $query   = $builder->get();
            $dataOnt = $query->getResult();

            $data = array();

            foreach ($dataOnt as $d) {
                $data[] = array(
                    // "id" => $d->idnodeb,
                    "idsto" => $d->idsto,
                    "merk" => $d->merk,
                    "type" => $d->type,
                    "serial_number" => $d->serial_number,
                    "status" => $d->status,
                    "desc" => $d->desc,
                    "allocation" => $d->allocation,
                    "installed" => $d->installed,
                    "received" => $d->received,
                    "return" => $d->return,
                    "created_at" => $d->created_at,
                    "updated_at" => $d->updated_at,
                );
            }
        }
        if ($sn == null && $sto == null && $status == null) {
            $array = array('dataont.deleted_at' => null, 'dataont.allocation' => $alokasi, 'dataont.installed' => null, 'dataont.return' => null);
            $builder->select('idsto, merk, type, dataont.serial_number, status, desc, allocation, installed, received, return, dataont.created_at, dataont.updated_at')
                ->where($array);
            $query   = $builder->get();
            $dataOnt = $query->getResult();

            $data = array();

            foreach ($dataOnt as $d) {
                $data[] = array(
                    // "id" => $d->idnodeb,
                    "idsto" => $d->idsto,
                    "merk" => $d->merk,
                    "type" => $d->type,
                    "serial_number" => $d->serial_number,
                    "status" => $d->status,
                    "desc" => $d->desc,
                    "allocation" => $d->allocation,
                    "installed" => $d->installed,
                    "received" => $d->received,
                    "return" => $d->return,
                    "created_at" => $d->created_at,
                    "updated_at" => $d->updated_at,
                );
            }
        }
        if ($sn == null && $alokasi == null && $sto == null) {
            $array = array('dataont.deleted_at' => null, 'dataont.status' => $status, 'dataont.installed' => null, 'dataont.return' => null);
            $builder->select('idsto, merk, type, dataont.serial_number, status, desc, allocation, installed, received, return, dataont.created_at, dataont.updated_at')
                ->where($array);
            $query   = $builder->get();
            $dataOnt = $query->getResult();

            $data = array();

            foreach ($dataOnt as $d) {
                $data[] = array(
                    // "id" => $d->idnodeb,
                    "idsto" => $d->idsto,
                    "merk" => $d->merk,
                    "type" => $d->type,
                    "serial_number" => $d->serial_number,
                    "status" => $d->status,
                    "desc" => $d->desc,
                    "allocation" => $d->allocation,
                    "installed" => $d->installed,
                    "received" => $d->received,
                    "return" => $d->return,
                    "created_at" => $d->created_at,
                    "updated_at" => $d->updated_at,
                );
            }
        }

        $hasil = [
            'data' => $data,
            'count' => count($data),
        ];

        return $this->respond($hasil, 200);
    }


    public function index()
    {

        $keyword = $this->request->getGet('keyword');
        $dataOnt = $this->dataOnt->getAll($keyword);
        $data = [
            'title' => 'ONT',
            'dataOnt' => $dataOnt,
            'dataOntStock' => $this->dataOnt->getStock(),
        ];
        helper('url');
        return view('/wan/ont/index', $data);
    }

    /**
     * Present a view to present a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $data = [
            'title' => 'ONT',
            'validation' => \config\Services::validation(),
            'dataSto' => $this->dataSto->getAll(),
            'ontMerk' => $this->dataOntType->getAll(),
            'ontType' => $this->dataOntType->findAll(),
        ];
        return view('wan/ont/new', $data);
    }

    /**
     * Process the creation/insertion of a new resource object.
     * This should be a POST.
     *
     * @return mixed
     */
    public function create()
    {
        //validasi form input
        if (!$this->validate(
            [
                'serial_number' => [
                    'rules' => 'required|is_unique[dataont.serial_number]',
                    'errors' => [
                        'required' => 'SN tidak boleh kosong',
                        'is_unique' => 'SN sudah terdaftar',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/wan/ont/new')->withInput();
        }
        // $data = $this->request->getPost();
        // $this->dataOnt->insert($data);

        $ont_type = explode('-', $this->request->getVar('ont_type'));
        $merk = $ont_type[0];
        $type = $ont_type[1];

        $serial_number = $this->request->getVar('serial_number');
        $separator = explode(',', $serial_number);
        $data = [];
        $value = array($data);
        $no = 0;
        foreach ($separator as $s) {
            $datas[$s] = array(
                'merk'              => $merk,
                'type'              => $type,
                'status'            => $this->request->getVar('status'),
                'idsto'             => $this->request->getVar('idsto'),
                'desc'              => $this->request->getVar('desc'),
                'allocation'        => $this->request->getVar('allocation'),
                'installed'         => $this->request->getVar('installed'),
                'received'          => $this->request->getVar('received'),
                'return'            => $this->request->getVar('return'),
                'serial_number'     => $s,
            );

            $this->dataOnt->insert($datas[$s]);
            $no++;
        }


        //change date to null
        $this->db      = \Config\Database::connect();
        $this->db->table('dataont')->set('received', NULL, true)->where(['received' => '0000-00-00'])->update();
        $this->db->table('dataont')->set('installed', NULL, true)->where(['installed' => '0000-00-00'])->update();
        $this->db->table('dataont')->set('return', NULL, true)->where(['return' => '0000-00-00'])->update();

        return redirect()->to('/wan/ont')->with('success', 'Data berhasil disimpan.');
    }
    public function ontType()
    {
        $data = $this->request->getPost();
        $this->dataOntType->insert($data);
        return redirect()->to('/wan/ont/new');
    }

    /**
     * Present a view to edit the properties of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $dataOnt = $this->dataOnt->find($id);
        if (is_object($dataOnt)) {
            $data = [
                'title' => 'ONT',
                'validation' => \config\Services::validation(),
                'dataOnt' => $dataOnt,
                'dataSto' => $this->dataSto->getAll(),
                'ontMerk' => $this->dataOntType->getAll(),
                'ontType' => $this->dataOntType->findAll(),
            ];
            return view('wan/ont/edit', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * Process the updating, full or partial, of a specific resource object.
     * This should be a POST.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $dataOnt = $this->dataOnt->find($id);
        $sn = $dataOnt->serial_number;
        //validasi form input
        if (!$this->validate(
            [
                'serial_number' => [
                    'rules' => "required|is_unique[dataont.serial_number,serial_number,{$sn}]",
                    'errors' => [
                        'required' => 'SN tidak boleh kosong',
                        'is_unique' => 'SN sudah terdaftar',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/wan/ont/' . $id . '/edit')->withInput();
        }


        $ont_type = explode('-', $this->request->getVar('ont_type'));
        $merk = $ont_type[0];
        $type = $ont_type[1];

        $data = array(
            'merk'              => $merk,
            'type'              => $type,
            'status'            => $this->request->getVar('status'),
            'idsto'             => $this->request->getVar('idsto'),
            'desc'              => $this->request->getVar('desc'),
            'allocation'        => $this->request->getVar('allocation'),
            'installed'         => $this->request->getVar('installed'),
            'received'          => $this->request->getVar('received'),
            'return'            => $this->request->getVar('return'),
            'serial_number'     => $this->request->getVar('serial_number'),
        );

        $this->dataOnt->update($id, $data);

        $this->db      = \Config\Database::connect();
        $return = $this->request->getVar('return');
        $installed = $this->request->getVar('installed');
        if ($return != "") {
            $this->db->table('dataont')->set('installed', NULL, true)->where(['installed' => $installed])->update();
        }

        //change date to null
        $this->db->table('dataont')->set('received', NULL, true)->where(['received' => '0000-00-00'])->update();
        $this->db->table('dataont')->set('installed', NULL, true)->where(['installed' => '0000-00-00'])->update();
        $this->db->table('dataont')->set('return', NULL, true)->where(['return' => '0000-00-00'])->update();

        return redirect()->to('/wan/ont')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Present a view to confirm the deletion of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function remove($id = null)
    {
        //
    }

    /**
     * Process the deletion of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->dataOnt->delete($id);
        return redirect()->to('/wan/ont')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {
        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $builder = $db->table('dataont')
                ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return, created_at')
                ->where('dataont.deleted_at', null);
            // ->orderBy('dataont.created_at', 'DESC');

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where('dataont.deleted_at', null);
                $builder->orLike('merk', $keyword)->where('dataont.deleted_at', null);
                $builder->orLike('type', $keyword)->where('dataont.deleted_at', null);
                $builder->orLike('serial_number', $keyword)->where('dataont.deleted_at', null);
                $builder->orLike('status', $keyword)->where('dataont.deleted_at', null);
                $builder->orLike('desc', $keyword)->where('dataont.deleted_at', null);
                $builder->orLike('installed', $keyword)->where('dataont.deleted_at', null);
            };

            return DataTable::of($builder)
                ->edit('status', function ($dataOnt) {
                    if ($dataOnt->status == 'RUSAK') {
                        return '<span class="text-danger font-weight-bold">' . $dataOnt->status . '</span>';
                    }
                    return $dataOnt->status;
                })
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOnt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/ont/$dataOnt->idont/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idont=\"$dataOnt->idont\" data-serial_number=\"$dataOnt->serial_number\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function listDataStock()
    {
        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation =' => 'ASSURANCE');
            $builder = $db->table('dataont')
                ->select('idont, idsto, merk, type, serial_number, status, desc, allocation, received, return')
                ->where($array);

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where($array);
                $builder->orLike('merk', $keyword)->where($array);
                $builder->orLike('type', $keyword)->where($array);
                $builder->orLike('serial_number', $keyword)->where($array);
                $builder->orLike('status', $keyword)->where($array);
                $builder->orLike('desc', $keyword)->where($array);
            };

            return DataTable::of($builder)
                ->edit('status', function ($dataOnt) {
                    if ($dataOnt->status == 'RUSAK') {
                        return '<span class="text-danger font-weight-bold">' . $dataOnt->status . '</span>';
                    }
                    return $dataOnt->status;
                })
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOnt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/ont/$dataOnt->idont/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idont=\"$dataOnt->idont\" data-serial_number=\"$dataOnt->serial_number\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function listDataStockFf()
    {
        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation =' => 'FULFILLMENT');
            $builder = $db->table('dataont')
                ->select('idont, idsto, merk, type, serial_number, status, desc, allocation, received, return')
                ->where($array);

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where($array);
                $builder->orLike('merk', $keyword)->where($array);
                $builder->orLike('type', $keyword)->where($array);
                $builder->orLike('serial_number', $keyword)->where($array);
                $builder->orLike('status', $keyword)->where($array);
                $builder->orLike('desc', $keyword)->where($array);
            };

            return DataTable::of($builder)
                ->edit('status', function ($dataOnt) {
                    if ($dataOnt->status == 'RUSAK') {
                        return '<span class="text-danger font-weight-bold">' . $dataOnt->status . '</span>';
                    }
                    return $dataOnt->status;
                })
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOnt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/ont/$dataOnt->idont/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idont=\"$dataOnt->idont\" data-serial_number=\"$dataOnt->serial_number\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function listDataStockOlo()
    {
        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation' => "OLO");
            $builder = $db->table('dataont')
                ->select('idont, idsto, merk, type, serial_number, status, desc, allocation, received, return')
                ->where($array);

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where($array);
                $builder->orLike('merk', $keyword)->where($array);
                $builder->orLike('type', $keyword)->where($array);
                $builder->orLike('serial_number', $keyword)->where($array);
                $builder->orLike('status', $keyword)->where($array);
                $builder->orLike('desc', $keyword)->where($array);
            };

            return DataTable::of($builder)
                ->edit('status', function ($dataOnt) {
                    if ($dataOnt->status == 'RUSAK') {
                        return '<span class="text-danger font-weight-bold">' . $dataOnt->status . '</span>';
                    }
                    return $dataOnt->status;
                })
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOnt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/ont/$dataOnt->idont/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idont=\"$dataOnt->idont\" data-serial_number=\"$dataOnt->serial_number\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function listDataInstalled()
    {
        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = array('dataont.deleted_at' => null, 'dataont.return' => null, 'dataont.installed !=' => null);
            $builder = $db->table('dataont')
                ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return')
                ->where($array);

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where($array);
                $builder->orLike('merk', $keyword)->where($array);
                $builder->orLike('type', $keyword)->where($array);
                $builder->orLike('serial_number', $keyword)->where($array);
                $builder->orLike('status', $keyword)->where($array);
                $builder->orLike('desc', $keyword)->where($array);
                $builder->orLike('installed', $keyword)->where($array);
            };

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOnt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/ont/$dataOnt->idont/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idont=\"$dataOnt->idont\" data-serial_number=\"$dataOnt->serial_number\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function listDataReturn()
    {
        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = array('dataont.deleted_at' => null, 'dataont.return !=' => null);
            $builder = $db->table('dataont')
                ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return')
                ->where($array);

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where($array);
                $builder->orLike('merk', $keyword)->where($array);
                $builder->orLike('type', $keyword)->where($array);
                $builder->orLike('serial_number', $keyword)->where($array);
                $builder->orLike('status', $keyword)->where($array);
                $builder->orLike('desc', $keyword)->where($array);
            };

            return DataTable::of($builder)
                ->edit('status', function ($dataOnt) {
                    if ($dataOnt->status == 'RUSAK') {
                        return '<span class="text-danger font-weight-bold">' . $dataOnt->status . '</span>';
                    }
                    return $dataOnt->status;
                })
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOnt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/ont/$dataOnt->idont/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idont=\"$dataOnt->idont\" data-serial_number=\"$dataOnt->serial_number\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function listDataTrash()
    {
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $builder = $db->table('dataont')
                ->select('idont, idsto, merk, type, serial_number, status, allocation, desc, installed, received, return')
                ->where('dataont.deleted_at !=', null);

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOnt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <a href=\"/wan/ont/restore/$dataOnt->idont\" type=\"button\" class=\"btn btn-sm btn-info\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idont=\"$dataOnt->idont\" data-serial_number=\"$dataOnt->serial_number\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function import()
    {

        $file = $this->request->getFile('file_excel');
        $extension = $file->getClientExtension();

        if ($extension == 'xlsx' || $extension == 'xls') {
            if ($extension == 'xls') {
                $reader = new \Phpoffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $reader->load($file);
            $dataOnt = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataOnt as $d => $value) {
                if ($d == 0) {
                    continue;
                }

                $data = [
                    'merk'               => $value[0],
                    'type'               => $value[1],
                    'serial_number'      => $value[2],
                    'status'             => $value[3],
                    'idsto'              => $value[4],
                    'allocation'         => $value[5] == null && $value[2] != null  ? "-" : $value[5],
                    'desc'               => $value[6],
                    'received'           => $value[7] != null ? date('Y-m-d', strtotime($value[7])) : $value[7],
                    'installed'          => $value[8] != null ? date('Y-m-d', strtotime($value[8])) : $value[8],
                    'return'             => $value[9] != null ? date('Y-m-d', strtotime($value[9])) : $value[9],
                ];
                // return print_r($data);
                $this->dataOnt->insert($data);

                //change date to null
                $this->db      = \Config\Database::connect();
                $this->db->table('dataont')->set('received', NULL, true)->where(['received' => '0000-00-00'])->update();
                $this->db->table('dataont')->set('installed', NULL, true)->where(['installed' => '0000-00-00'])->update();
                $this->db->table('dataont')->set('return', NULL, true)->where(['return' => '0000-00-00'])->update();
            }

            return
                redirect()->back()->with('success', 'Data Excel Berhasil Diimport');
        } else {
            return redirect()->back()->with('error', 'Format File Tidak Sesuai');
        }
    }

    public function export_trash()
    {

        $filename = 'METRO-' . date('ymd') . '.xlsx';
        //get all data
        // $dataOlt = $this->dataOlt->getAll();

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('datametro');
        $builder->select('*')->where('dataolt.deleted_at IS NOT NULL', null, false);
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
        }

        $query = $builder->get();
        $tableQuery = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'STO');
        $sheet->setCellValue('C1', 'HOSTNAME METRO');
        $sheet->setCellValue('D1', 'IP METRO');

        //start coloum
        $coloumn = 2;
        foreach ($tableQuery as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->idsto);
            $sheet->setCellValue('C' . $coloumn, $d->hostname_metro);
            $sheet->setCellValue('D' . $coloumn, $d->ip_metro);
            $coloumn++;
        }

        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF3232');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        $sheet->getStyle('A1:D' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);


        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function trash()
    {

        $keyword = $this->request->getGet('keyword');
        $dataOnt = $this->dataOnt->getTrash($keyword);
        // $getPaginatedTrash = $this->dataOnt->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'ONT',
            'dataOnt'      => $dataOnt,
            // 'dataOnt'      => $getPaginatedTrash['dataOnt'],
            // 'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('wan/ont/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('dataont')->set('deleted_at', null, true)->where(['idont' => $id])->update();
        } else {
            $this->db->table('dataont')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/wan/ont')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wan/ont');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataOnt->delete($id, true);
            return redirect()->to('/wan/ont/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataOnt->purgeDeleted();
            return redirect()->to('/wan/ont/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }

    public function export()
    {

        $filename = 'ONT-' . date('ymd-his') . '.xlsx';

        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('dataont');
        $builder->select('*')->where('dataont.deleted_at', null)->orderBy('dataont.created_at', 'DESC');;

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('merk', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('type', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('serial_number', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('status', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('desc', $keyword)->where('dataont.deleted_at', null);
            $builder->orLike('installed', $keyword)->where('dataont.deleted_at', null);
        }

        $query = $builder->get();
        $tableQuery = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'MERK');
        $sheet->setCellValue('C1', 'TYPE');
        $sheet->setCellValue('D1', 'SERIAL NUMBER');
        $sheet->setCellValue('E1', 'STATUS');
        $sheet->setCellValue('F1', 'STO');
        $sheet->setCellValue('G1', 'ALLOCATION');
        $sheet->setCellValue('H1', 'RECEIVED');
        $sheet->setCellValue('I1', 'INSTALLED');
        $sheet->setCellValue('J1', 'RETURN');
        $sheet->setCellValue('K1', 'DESCRIPTION');

        //start coloum
        $coloumn = 2;
        foreach ($tableQuery as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->merk);
            $sheet->setCellValue('C' . $coloumn, $d->type);
            $sheet->setCellValue('D' . $coloumn, $d->serial_number);
            $sheet->setCellValue('E' . $coloumn, $d->status);
            $sheet->setCellValue('F' . $coloumn, $d->idsto);
            $sheet->setCellValue('G' . $coloumn, $d->allocation);
            $sheet->setCellValue('H' . $coloumn, $d->received);
            $sheet->setCellValue('I' . $coloumn, $d->installed);
            $sheet->setCellValue('J' . $coloumn, $d->return);
            $sheet->setCellValue('K' . $coloumn, $d->desc);
            $coloumn++;
        }

        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF3232');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        $sheet->getStyle('A1:K' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }


    public function exportStock()
    {

        $filename = 'ONT-STOCK-' . date('ymd-his') . '.xlsx';

        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();

        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation !=' => "OLO");
        $builder = $db->table('dataont')
            ->select('idont, merk, idsto, type, serial_number, status, desc, allocation, received, return')
            ->where($array)
            ->orderBy('dataont.idsto', 'ASC');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array)->orWhere('dataont.allocation', null);
            $builder->orLike('merk', $keyword)->where($array)->orWhere('dataont.allocation', null);
            $builder->orLike('type', $keyword)->where($array)->orWhere('dataont.allocation', null);
            $builder->orLike('serial_number', $keyword)->where($array)->orWhere('dataont.allocation', null);
            $builder->orLike('status', $keyword)->where($array)->orWhere('dataont.allocation', null);
            $builder->orLike('desc', $keyword)->where($array)->orWhere('dataont.allocation', null);
        };


        $query = $builder->get();
        $tableQuery = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'MERK');
        $sheet->setCellValue('C1', 'TYPE');
        $sheet->setCellValue('D1', 'SERIAL NUMBER');
        $sheet->setCellValue('E1', 'STATUS');
        $sheet->setCellValue('F1', 'STO');
        $sheet->setCellValue('G1', 'ALLOCATION');
        $sheet->setCellValue('H1', 'RECEIVED');
        $sheet->setCellValue('I1', 'DESCRIPTION');

        //start coloum
        $coloumn = 2;
        foreach ($tableQuery as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->merk);
            $sheet->setCellValue('C' . $coloumn, $d->type);
            $sheet->setCellValue('D' . $coloumn, $d->serial_number);
            $sheet->setCellValue('E' . $coloumn, $d->status);
            $sheet->setCellValue('F' . $coloumn, $d->idsto);
            $sheet->setCellValue('G' . $coloumn, $d->allocation);
            $sheet->setCellValue('H' . $coloumn, $d->received);
            $sheet->setCellValue('I' . $coloumn, $d->desc);
            $coloumn++;
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF3232');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        $sheet->getStyle('A1:I' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }


    public function exportStockOlo()
    {

        $filename = 'ONT-OLO-' . date('ymd-his') . '.xlsx';

        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation' => "OLO");
        $builder = $db->table('dataont')
            ->select('idont, idsto, merk, type, serial_number, status, desc, allocation, received, return')
            ->where($array)
            ->orderBy('dataont.idsto', 'ASC');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('merk', $keyword)->where($array);
            $builder->orLike('type', $keyword)->where($array);
            $builder->orLike('serial_number', $keyword)->where($array);
            $builder->orLike('status', $keyword)->where($array);
            $builder->orLike('desc', $keyword)->where($array);
        };

        $query = $builder->get();
        $tableQuery = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'MERK');
        $sheet->setCellValue('C1', 'TYPE');
        $sheet->setCellValue('D1', 'SERIAL NUMBER');
        $sheet->setCellValue('E1', 'STATUS');
        $sheet->setCellValue('F1', 'STO');
        $sheet->setCellValue('G1', 'ALLOCATION');
        $sheet->setCellValue('H1', 'RECEIVED');
        $sheet->setCellValue('I1', 'DESCRIPTION');

        //start coloum
        $coloumn = 2;
        foreach ($tableQuery as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->merk);
            $sheet->setCellValue('C' . $coloumn, $d->type);
            $sheet->setCellValue('D' . $coloumn, $d->serial_number);
            $sheet->setCellValue('E' . $coloumn, $d->status);
            $sheet->setCellValue('F' . $coloumn, $d->idsto);
            $sheet->setCellValue('G' . $coloumn, $d->allocation);
            $sheet->setCellValue('H' . $coloumn, $d->received);
            $sheet->setCellValue('I' . $coloumn, $d->desc);
            $coloumn++;
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF3232');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        $sheet->getStyle('A1:I' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }


    public function exportinstalled()
    {

        $filename = 'ONT-installed-' . date('ymd-his') . '.xlsx';

        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $array = array('dataont.deleted_at' => null, 'dataont.return' => null, 'dataont.installed !=' => null);
        $builder = $db->table('dataont')
            ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return, allocation')
            ->where($array)
            ->orderBy('dataont.installed', 'DESC');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('merk', $keyword)->where($array);
            $builder->orLike('type', $keyword)->where($array);
            $builder->orLike('serial_number', $keyword)->where($array);
            $builder->orLike('status', $keyword)->where($array);
            $builder->orLike('desc', $keyword)->where($array);
            $builder->orLike('installed', $keyword)->where($array);
        };

        $query = $builder->get();
        $tableQuery = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'MERK');
        $sheet->setCellValue('C1', 'TYPE');
        $sheet->setCellValue('D1', 'SERIAL NUMBER');
        $sheet->setCellValue('E1', 'ALLOCATION');
        $sheet->setCellValue('F1', 'RECEIVED');
        $sheet->setCellValue('G1', 'INSTALLED');
        $sheet->setCellValue('H1', 'DESCRIPTION');

        //start coloum
        $coloumn = 2;
        foreach ($tableQuery as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->merk);
            $sheet->setCellValue('C' . $coloumn, $d->type);
            $sheet->setCellValue('D' . $coloumn, $d->serial_number);
            $sheet->setCellValue('E' . $coloumn, $d->allocation);
            $sheet->setCellValue('F' . $coloumn, $d->received);
            $sheet->setCellValue('G' . $coloumn, $d->installed);
            $sheet->setCellValue('H' . $coloumn, $d->desc);
            $coloumn++;
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF3232');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        $sheet->getStyle('A1:G' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }


    public function exportReturn()
    {

        $filename = 'ONT-RETURN-' . date('ymd-his') . '.xlsx';

        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $array = array('dataont.deleted_at' => null, 'dataont.return !=' => null);
        $builder = $db->table('dataont')
            ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return')
            ->where($array)
            ->orderBy('dataont.return', 'DESC');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('merk', $keyword)->where($array);
            $builder->orLike('type', $keyword)->where($array);
            $builder->orLike('serial_number', $keyword)->where($array);
            $builder->orLike('status', $keyword)->where($array);
            $builder->orLike('desc', $keyword)->where($array);
        };


        $query = $builder->get();
        $tableQuery = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'MERK');
        $sheet->setCellValue('C1', 'TYPE');
        $sheet->setCellValue('D1', 'SERIAL NUMBER');
        $sheet->setCellValue('E1', 'STATUS');
        $sheet->setCellValue('F1', 'RECEIVED');
        $sheet->setCellValue('G1', 'RETURN');
        $sheet->setCellValue('H1', 'DESCRIPTION');

        //start coloum
        $coloumn = 2;
        foreach ($tableQuery as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->merk);
            $sheet->setCellValue('C' . $coloumn, $d->type);
            $sheet->setCellValue('D' . $coloumn, $d->serial_number);
            $sheet->setCellValue('E' . $coloumn, $d->status);
            $sheet->setCellValue('F' . $coloumn, $d->received);
            $sheet->setCellValue('G' . $coloumn, $d->return);
            $sheet->setCellValue('H' . $coloumn, $d->desc);
            $coloumn++;
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF3232');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        $sheet->getStyle('A1:H' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
