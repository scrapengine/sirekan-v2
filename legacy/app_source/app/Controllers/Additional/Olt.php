<?php

namespace App\Controllers\Additional;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OltModel;
use App\Models\MetroModel;
use App\Models\StoModel;
use App\Models\NodebModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Hermawan\DataTables\DataTable;

use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Files\File;

class Olt extends ResourceController
{
    public function __construct()
    {
        $this->dataOlt = new OltModel();
        $this->dataMetro = new MetroModel();
        $this->dataSto = new StoModel();
        $this->dataNodeb = new NodebModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    
     public function read()
     {
 
         $db = \Config\Database::connect();
         $array = array('dataolt.deleted_at' => null, 'hostname_olt !=' => 'DIRECT_METRO', 'datametro.deleted_at' => null);
         $array2 = array('hostname_olt !=' => '-');
         $builder = $db->table('dataolt')
             ->select('dataolt.witel, datasto.idsto, datametro.hostname_metro, datametro.ip_metro, port_metro, hostname_olt, ip_olt, port_olt, platform, type_olt')
             ->where($array)->where($array2)
             ->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro')
             ->join('datasto',  'datasto.idsto = datametro.idsto');
 
         $query = $builder->get();
         $dataOlt = $query->getResult();
        //  print_r($dataOlt);
 
         $data = array();
 
         foreach ($dataOlt as $d) {
 
            //  $ont_type = $d->ont_type;
            //  if (!str_contains($d->ont_type, 'direct')) {
            //      $ont_type = $d->ont_type_gabungan;
            //  }
 
            //  // $ont_merk = "";
            //  // if (!str_contains($d->ont_type, 'direct')) {
            //  //     $ont_merk = $d->merk;
            //  // }
 
            //  $port_metro = $d->port_metro_nodeb;
            //  if (str_contains($d->hostname_olt_nodeb, 'GPON')) {
            //      $port_metro = $d->port_metro_olt;
            //  }
 
            //  $type_olt = "";
            //  if (str_contains($d->port_onu, ':') && str_contains($d->port_onu, '/')) {
            //      $type_olt = $d->type_olt;
            //  } elseif (!str_contains($d->port_onu, ':') && str_contains($d->port_onu, '/')) {
            //      $type_olt = 'direct' . $d->type_olt;
            //  } elseif ($d->type_olt == null) {
            //      $type_olt = 'directMetro';
            //  }
 
 
             $data[] = array(
                 // "id" => $d->idnodeb,
                 "witel" => $d->witel,
                 "sto" => $d->idsto,
                 "hostname_metro" => $d->hostname_metro,
                 "ip_metro" => $d->ip_metro,
                 "port_metro" => $d->port_metro,
                 "type_olt" => $d->type_olt,
                 "hostname_olt" => $d->hostname_olt,
                 "ip_olt" => $d->ip_olt,
                 "port_olt" => $d->port_olt,
                 "platform" => $d->platform,
             );
         }
         return $this->respond($data, 200);
     }
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $dataOlt = $this->dataOlt->getAll($keyword);
        // $getPaginated = $this->dataOlt->getPaginated(10, $keyword);
        $data = [
            'title'        => 'OLT',
            'dataOlt'      => $dataOlt,
            // 'dataOlt'      => $getPaginated['dataOlt'],
            // 'pager'        => $getPaginated['pager'],
            // 'dataOlt2'     => $this->dataOlt->findAll(),
        ];
        helper('url');
        return view('additional/olt/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
        $data = [
            'title' => 'OLT',
            'validation' => \config\Services::validation(),
            'dataMetro' => $this->dataMetro->getAll(),
            'dataSto' => $this->dataSto->getAll(),
        ];
        return view('additional/olt/new', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        //validasi form input
        if (!$this->validate(
            [
                'hostname_metro' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Hostname Metro tidak boleh kosong',
                    ],
                ],
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'hostname_olt' => [
                    'rules' => 'required|is_unique[dataolt.hostname_olt]',
                    'errors' => [
                        'required' => 'Hostname OLT tidak boleh kosong',
                        'is_unique' => 'Hostname OLT sudah terdaftar',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/additional/olt/new')->withInput();
        }
        $data = $this->request->getPost();
        $this->dataOlt->insert($data);

        return redirect()->to('/additional/olt')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $dataOlt = $this->dataOlt->find($id);
        if (is_object($dataOlt)) {
            $data = [
                'title' => 'OLT',
                'validation' => \config\Services::validation(),
                'dataOlt' => $dataOlt,
                'dataMetro' => $this->dataMetro->getAll(),
                'dataSto' => $this->dataSto->getAll(),
                'dataMetro2' => $this->dataOlt->getAll(),
            ];
            return view('additional/olt/edit', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //validasi form input
        if (!$this->validate(
            [
                'hostname_metro' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Hostname Metro tidak boleh kosong',
                    ],
                ],
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'hostname_olt' => [
                    'rules' => "required|is_unique[dataolt.hostname_olt,hostname_olt,{$id}]",
                    'errors' => [
                        'required' => 'Hostname OLT tidak boleh kosong',
                        'is_unique' => 'Hostname OLT sudah terdaftar',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/additional/olt/' . $id . '/edit')->withInput();
        }

        $data = $this->request->getPost();

        $this->dataOlt->update($id, $data);

        return redirect()->to('/additional/olt')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $dataNodeb = $this->dataNodeb->first();
        if ($dataNodeb->hostname_olt == $id) {
            return redirect()->to('/additional/olt')->with('error', 'Tidak dapat dihapus! Data berelasi sudah digunakan di tabel lain.');
        }

        $this->dataOlt->delete($id);

        return redirect()->to('/additional/olt')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {

        if ($this->request->isAJAX()) {

            $keyword = $this->request->getGet('keyword');
            $fromdate = $this->request->getGet('fromdate');
            $untildate = $this->request->getGet('untildate');
            $choice = $this->request->getGet('choice');
            $values = $this->request->getGet('values');

            $db = db_connect();
            $array = array('dataolt.deleted_at' => null, 'hostname_olt !=' => 'DIRECT_METRO', 'datametro.deleted_at' => null);
            $array2 = array('hostname_olt !=' => '-');
            $builder = $db->table('dataolt')
                ->select('dataolt.witel, datasto.idsto, datametro.hostname_metro, datametro.ip_metro, port_metro, hostname_olt, ip_olt, port_olt, platform, type_olt')
                ->where($array)->where($array2)
                ->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro')
                ->join('datasto',  'datasto.idsto = datametro.idsto');


            if ($values != '' && $choice != '') {
                foreach ($choice as $key => $c) {
                    if ($key == 0) {
                        $builder->like($c, $values[$key])->where($array);
                    } else {
                        if (count(array_unique($choice)) < count($choice)) {
                            $builder->orlike($c, $values[$key])->where($array);
                        } else {
                            $builder->like($c, $values[$key])->where($array);
                        }
                    }
                }
            }


            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOlt) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <a href=\"/additional/olt/$dataOlt->hostname_olt/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-hostname_olt=\"$dataOlt->hostname_olt\">
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
            $builder = $db->table('dataolt')
                ->select('dataolt.witel, datasto.idsto, datametro.hostname_metro, datametro.ip_metro, port_metro, hostname_olt, ip_olt, port_olt, platform, type_olt')
                ->where('dataolt.deleted_at !=', null)
                ->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro')
                ->join('datasto',  'datasto.idsto = datametro.idsto');

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOlt) {
                    return "<div class=\"btn-group\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-info\" id=\"btnRestore\" data-hostname_olt=\"$dataOlt->hostname_olt\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </button>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-hostname_olt=\"$dataOlt->hostname_olt\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'OLT-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $fromdate = $this->request->getGet('fromdate');
        $untildate = $this->request->getGet('untildate');
        $choice = $this->request->getGet('choice');
        $values = $this->request->getGet('values');

        $db = \Config\Database::connect();
        $builder = $db->table('dataolt');
        $array = array('dataolt.deleted_at' => null, 'hostname_olt !=' => 'DIRECT_METRO', 'datametro.deleted_at' => null);
        $array2 = array('hostname_olt !=' => '-');
        $builder->select('*')->where($array)->where($array2);
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');


        if ($values != '' && $choice != '') {
            foreach ($choice as $key => $c) {
                if ($key == 0) {
                    $builder->like($c, $values[$key])->where($array);
                } else {
                    if (count(array_unique($choice)) < count($choice)) {
                        $builder->orlike($c, $values[$key])->where($array);
                    } else {
                        $builder->like($c, $values[$key])->where($array);
                    }
                }
            }
        }

        $query = $builder->get();
        $dataOlt = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Witel');
        $sheet->setCellValue('C1', 'STO');
        $sheet->setCellValue('D1', 'Hostname_Metro');
        $sheet->setCellValue('E1', 'IP Metro');
        $sheet->setCellValue('F1', 'Port Metro');
        $sheet->setCellValue('G1', 'Hostname OLT');
        $sheet->setCellValue('H1', 'IP OLT');
        $sheet->setCellValue('I1', 'Port OLT');
        $sheet->setCellValue('J1', 'Platform');
        $sheet->setCellValue('K1', 'Type');

        //start coloum
        $coloumn = 2;
        foreach ($dataOlt as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->witel);
            $sheet->setCellValue('C' . $coloumn, $d->idsto);
            $sheet->setCellValue('D' . $coloumn, $d->hostname_metro);
            $sheet->setCellValue('E' . $coloumn, $d->ip_metro);
            $sheet->setCellValue('F' . $coloumn, $d->port_metro);
            $sheet->setCellValue('G' . $coloumn, $d->hostname_olt);
            $sheet->setCellValue('H' . $coloumn, $d->ip_olt);
            $sheet->setCellValue('I' . $coloumn, $d->port_olt);
            $sheet->setCellValue('J' . $coloumn, $d->platform);
            $sheet->setCellValue('K' . $coloumn, $d->type_olt);
            $coloumn++;
        }

        $sheet->getStyle('A1:K1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('499ff2');
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
    public function export_trash()
    {

        $filename = 'OLT-' . date('ymd') . '.xlsx';
        //get all data
        // $dataOlt = $this->dataOlt->getAll();

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('dataolt');
        $builder->select('*')->where('dataolt.deleted_at IS NOT NULL', null, false);
        $builder->join('datametro',  'datametro.hostname_metro = dataolt.hostname_metro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_olt', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('platform', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
        }
        $query = $builder->get();
        $dataOlt = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Witel');
        $sheet->setCellValue('C1', 'STO');
        $sheet->setCellValue('D1', 'Hostname_Metro');
        $sheet->setCellValue('E1', 'Ip_Metro');
        $sheet->setCellValue('F1', 'Port_Metro');
        $sheet->setCellValue('G1', 'Hostname_Olt');
        $sheet->setCellValue('H1', 'Ip_Olt');
        $sheet->setCellValue('I1', 'Port_Olt');
        $sheet->setCellValue('J1', 'Platform');

        //start coloum
        $coloumn = 2;
        foreach ($dataOlt as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->witel);
            $sheet->setCellValue('C' . $coloumn, $d->idsto);
            $sheet->setCellValue('D' . $coloumn, $d->hostname_metro);
            $sheet->setCellValue('E' . $coloumn, $d->ip_metro);
            $sheet->setCellValue('F' . $coloumn, $d->port_metro);
            $sheet->setCellValue('G' . $coloumn, $d->hostname_olt);
            $sheet->setCellValue('H' . $coloumn, $d->ip_olt);
            $sheet->setCellValue('I' . $coloumn, $d->port_olt);
            $sheet->setCellValue('J' . $coloumn, $d->platform);
            $coloumn++;
        }

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('F0E68C');
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        $sheet->getStyle('A1:J' . ($coloumn - 1))->applyFromArray($styleArray);

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


        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
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
            $dataOlt = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataOlt as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'witel'          => $value[0],
                    'idsto'          => $value[1],
                    'hostname_metro' => $value[2],
                    'port_metro'     => $value[3],
                    'hostname_olt'   => $value[4],
                    'ip_olt'         => $value[5],
                    'port_olt'       => $value[6],
                    'platform'       => $value[7],
                    'type_olt'       => $value[8],
                ];
                $exist = $this->dataOlt->where('hostname_olt', $value[4])->first();
                if ($exist) {
                    $id = $exist->hostname_olt;
                    $this->dataOlt->update($id, $data);
                } else {
                    $this->dataOlt->insert($data);
                }
                // $this->dataOlt->insert($data);
            }

            return
                redirect()->back()->with('success', 'Data Excel Berhasil Diimport');
        } else {
            return redirect()->back()->with('error', 'Format File Tidak Sesuai');
        }
    }

    public function trash()
    {

        $keyword = $this->request->getGet('keyword');
        $dataOlt = $this->dataOlt->getTrash($keyword);
        // $getPaginatedTrash = $this->dataOlt->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'OLT',
            'dataOlt'      => $dataOlt,
            // 'dataOlt'      => $getPaginatedTrash['dataOlt'],
            // 'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('additional/olt/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('dataolt')->set('deleted_at', null, true)->where(['hostname_olt' => $id])->update();
        } else {
            $this->db->table('dataolt')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/additional/olt')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/additional/olt');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataOlt->delete($id, true);
            return redirect()->to('/additional/olt/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataOlt->purgeDeleted();
            return redirect()->to('/additional/olt/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }





    public function ip_metro()
    {
        $s = "SELECT ip_metro FROM datametro";
        $query = $this->db->query($s);
        foreach ($query->result() as $ip_metro) {
            $return_arr[] = $ip_metro->ip_metro;
        }
        echo json_encode($return_arr);
    }
}
