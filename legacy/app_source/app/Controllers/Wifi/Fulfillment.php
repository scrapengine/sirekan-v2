<?php

namespace App\Controllers\Wan;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OltModel;
use App\Models\OntModel;
use App\Models\MetroModel;
use App\Models\StoModel;
use App\Models\FfwanModel;
use App\Models\LayananModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Hermawan\DataTables\DataTable;

use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Files\File;

class Fulfillment extends ResourceController
{
    public function __construct()
    {
        $this->dataFfwan = new FfwanModel();
        $this->dataOlt = new OltModel();
        $this->dataOnt = new OntModel();
        $this->dataMetro = new MetroModel();
        $this->dataSto = new StoModel();
        $this->dataLayanan = new LayananModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {

        $data = [
            'title'        => 'FULFILLMENT',
            'dataFfwan'    => $this->dataFfwan->findAll(),
            'dataFfwann'    => $this->dataFfwan->getAll(),
        ];

        return view('wifi/fulfillment/index', $data);
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
            'title' => 'FULFILLMENT',
            'validation' => \config\Services::validation(),
            'dataMetro' => $this->dataMetro->getAll(),
            'dataOlt' => $this->dataOlt->getAll(),
            'dataSto' => $this->dataSto->getAll(),
            'dataLayanan' => $this->dataLayanan->findAll(),
        ];
        return view('wifi/fulfillment/new', $data);
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
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'no_order' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nomor order tidak boleh kosong',
                    ],
                ],
                'nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama pelanggan tidak boleh kosong',
                    ],
                ],
                'evidence' => [
                    'rules' => 'max_size[evidence,2048]|is_image[evidence]|mime_in[evidence,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran gambar terlalu besar',
                        'is_image' => 'Format tidak sesuai',
                        'mime_in' => 'Format tidak sesuai',
                    ]
                ]
            ]
        )) {
            return redirect()->to('/wifi/fulfillment/new')->withInput();
        }

        $evidence = $this->request->getFile('evidence');


        if ($evidence->getError()) {
            $name_evidence = "";
        } else {

            // generate nama file random
            $name_evidence = $evidence->getRandomName();

            // pindahkan gambar
            $evidence->move('img/fulfillment', $name_evidence);
        }


        $data = $this->request->getPost();
        $addData = [
            'divisi' => 'WIFI',
            'evidence' => $name_evidence,
        ];

        $array_merge = array_merge($data, $addData);
        $this->dataFfwan->insert($array_merge);

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $nama = $this->request->getVar('nama');
        $serial_number = $this->request->getVar('serial_number');
        $dataOnt = $this->dataOnt->where('serial_number', $serial_number)->first();
        if ($dataOnt) {
            $this->db->table('dataont')->set('installed', $installed)->where(['serial_number' => $serial_number])->update();
            $this->db->table('dataont')->set('desc', $nama)->where(['serial_number' => $serial_number])->update();
        }

        return redirect()->to('/wifi/fulfillment')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $dataFfwan = $this->dataFfwan->find($id);
        $Olt = $dataFfwan->hostname_olt;
        if (is_object($dataFfwan)) {
            $data = [
                'title'      => 'FULFILLMENT',
                'validation' => \config\Services::validation(),
                'dataFfwan'  => $dataFfwan,
                'dataMetro'  => $this->dataMetro->getAll(),
                'dataOlt'    => $this->dataOlt->getAll(),
                'Olt'        => $this->dataOlt->find($Olt),
                'dataSto'    => $this->dataSto->getAll(),
                'dataLayanan'    => $this->dataLayanan->findAll(),
            ];
            return view('wifi/fulfillment/edit', $data);
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
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'no_order' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nomor order tidak boleh kosong',
                    ],
                ],
                'nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama pelanggan tidak boleh kosong',
                    ],
                ],
                'hostname_metro' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Hostname Metro tidak boleh kosong',
                    ],
                ],
                'evidence' => [
                    'rules' => 'max_size[evidence,2048]|is_image[evidence]|mime_in[evidence,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran gambar terlalu besar',
                        'is_image' => 'Format tidak sesuai',
                        'mime_in' => 'Format tidak sesuai',
                    ]
                ]
            ]
        )) {
            return redirect()->to('/wifi/fulfillment/' . $id . '/edit')->withInput();
        }
        // return var_dump($this->request->getVar('evidence_old'));
        $save_as = $this->request->getVar('save_as');
        if ($save_as == "on") {

            $name_evidence = $this->request->getVar('evidence_old');
            $data = $this->request->getPost();
            $addData = [
                'divisi' => 'WIFI',
                'evidence' => $name_evidence,
            ];

            $array_merge = array_merge($data, $addData);
            $this->dataFfwan->insert($array_merge);
        } else {
            $evidence = $this->request->getFile('evidence');

            // cek gambar, apakah tetap gambar lama
            if ($evidence->getError() == 4) {
                $name_evidence = $this->request->getVar('evidence_old');
            } else {

                // generate nama file random
                $name_evidence = $evidence->getRandomName();

                // pindahkan gambar
                $evidence->move('img/fulfillment', $name_evidence);

                //hapus gambar lama
                if ($this->request->getVar('evidence_old') != "") {
                    unlink('img/fulfillment/' . $this->request->getVar('evidence_old'));
                }
            }

            $data_post = $this->request->getPost();
            $addData = [
                'divisi' => 'WIFI',
                'evidence' => $name_evidence,
            ];

            $array_merge = array_merge($data_post, $addData);
            $this->dataFfwan->update($id, $array_merge);
        }

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $nama = $this->request->getVar('nama');
        $serial_number = $this->request->getVar('serial_number');
        $dataOnt = $this->dataOnt->where('serial_number', $serial_number)->first();
        if ($dataOnt) {
            $this->db->table('dataont')->set('installed', $installed)->where(['serial_number' => $serial_number])->update();
            $this->db->table('dataont')->set('desc', $nama)->where(['serial_number' => $serial_number])->update();
        }

        return redirect()->to('/wifi/fulfillment')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->dataFfwan->delete($id);

        return redirect()->to('/wifi/fulfillment')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {

        if ($this->request->isAJAX()) {

            //get spesific data
            $keyword = $this->request->getGet('keyword');
            $fromdate = $this->request->getGet('fromdate');
            $untildate = $this->request->getGet('untildate');
            $choice = $this->request->getGet('choice');
            $values = $this->request->getGet('values');

            $db = db_connect();
            $array = ['ffwan.deleted_at' => null, 'divisi' => 'WIFI'];
            $builder = $db->table('ffwan')
                ->select('idff, datasto.idsto, ffwan.idsto, tanggal, tanggal_install, tanggal_ps, layanan, no_order, nama, ffwan.alamat, tag_lokasi, order_type, datametro.hostname_metro ,datametro.ip_metro , dataolt.port_metro, ffwan.port_metro, dataolt.hostname_olt, dataolt.ip_olt, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, bandwidth, odc, odp, p_tarikan, lan, status, desc, divisi, evidence, service_id')
                ->where($array)
                ->join('datasto',  'datasto.idsto = ffwan.idsto')
                ->join('datametro',  'datametro.hostname_metro = ffwan.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = ffwan.hostname_olt');

            if ($fromdate != '' && $untildate != '') {
                $builder->where('tanggal BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
            }

            if ($values != '' && $choice != '') {
                foreach ($choice as $key => $c) {
                    if ($key == 0) {
                        $builder->like($c, $values[$key])->where($array)->where('tanggal BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                    } else {
                        $builder->orlike($c, $values[$key])->where($array)->where('tanggal BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                    }
                }
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataFfwan) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-outline-dark\" onclick=\"showDetail($dataFfwan->idff)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">
                        <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </button>
                    <a href=\"/wifi/fulfillment/$dataFfwan->idff/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idff=\"$dataFfwan->idff\" data-no_order=\"$dataFfwan->no_order\" data-nama=\"$dataFfwan->nama\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->edit('status', function ($dataFfwan) {
                    if ($dataFfwan->status == 'OPEN') {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'CLOSE') {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'REJECT') {
                        return '<h6><div class="badge badge-info font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                })
                ->toJson(true);
        }
    }


    public function listDataTrash()
    {

        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = ['ffwan.deleted_at !=' => null, 'divisi' => 'WIFI'];
            $builder = $db->table('ffwan')
                ->select('idff, datasto.idsto, ffwan.idsto, tanggal, tanggal_install, tanggal_ps, layanan, no_order, nama, ffwan.alamat, tag_lokasi, order_type, datametro.hostname_metro ,datametro.ip_metro , dataolt.port_metro, ffwan.port_metro, dataolt.hostname_olt, dataolt.ip_olt, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, bandwidth, odc, odp, p_tarikan, lan, status, desc, divisi, evidence, service_id')
                ->where($array)
                ->join('datasto',  'datasto.idsto = ffwan.idsto')
                ->join('datametro',  'datametro.hostname_metro = ffwan.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = ffwan.hostname_olt');

            if ($keyword != '') {
                $builder->like('datasto.idsto', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('tanggal', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('layanan', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('no_order', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('nama', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('order_type', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('datametro.hostname_metro', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('dataolt.hostname_olt', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('dataolt.ip_olt', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('hostname_ont', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('ip_ont', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('ont_type', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('serial_number', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('vlan', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('odc', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('odp', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('status', $keyword)->where('ffwan.deleted_at', null);
                $builder->orLike('desc', $keyword)->where('ffwan.deleted_at', null);
            };

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataFfwan) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-outline-dark\" onclick=\"showDetail($dataFfwan->idff)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">
                    <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </button>
                    <a href=\"/wifi/fulfillment/restore/$dataFfwan->idff\" type=\"button\" class=\"btn btn-sm btn-info\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idff=\"$dataFfwan->idff\" data-no_order=\"$dataFfwan->no_order\" data-nama=\"$dataFfwan->nama\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->edit('status', function ($dataFfwan) {
                    if ($dataFfwan->status == 'OPEN') {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'CLOSE') {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'REJECT') {
                        return '<h6><div class="badge badge-info font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success">' . $dataFfwan->status . '</div></h6>';
                })
                ->toJson(true);
        }
    }

    public function export()
    {
        $filename = 'FULFILLMENT-WAN-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $fromdate = $this->request->getGet('fromdate');
        $untildate = $this->request->getGet('untildate');
        $choice = $this->request->getGet('choice');
        $values = $this->request->getGet('values');

        // return print_r($keyword . $fromdate . $untildate . $idsto . $strvalue);
        $db = \Config\Database::connect();
        $array = ['ffwan.deleted_at' => null, 'divisi' => 'WIFI'];
        $builder = $db->table('ffwan');
        $builder->select('*, ffwan.idsto as sto, ffwan.alamat as almt')
            ->where($array)
            ->join('datasto',  'datasto.idsto = ffwan.idsto')
            ->join('datametro',  'datametro.hostname_metro = ffwan.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = ffwan.hostname_olt');

        if ($fromdate != '' && $untildate != '') {
            $builder->where('tanggal BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
        }

        if ($values != '' && $choice != '') {
            foreach ($choice as $key => $c) {
                if ($key == 0) {
                    $builder->like($c, $values[$key])->where($array)->where('tanggal BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                } else {
                    $builder->orlike($c, $values[$key])->where($array)->where('tanggal BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                }
            }
        };
        $query = $builder->get();
        $dataFfwan = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'TANGGAL');
        $sheet->setCellValue('C1', 'STO');
        $sheet->setCellValue('D1', 'LAYANAN');
        $sheet->setCellValue('E1', 'NO_ORDER');
        $sheet->setCellValue('F1', 'ORDER_TYPE');
        $sheet->setCellValue('G1', 'NAMA_PELANGGAN');
        $sheet->setCellValue('H1', 'ALAMAT');
        $sheet->setCellValue('I1', 'TAG_LOKASI');
        $sheet->setCellValue('J1', 'HOSTNAME_METRO');
        $sheet->setCellValue('K1', 'IP_METRO');
        $sheet->setCellValue('L1', 'PORT_METRO');
        $sheet->setCellValue('M1', 'HOSTNAME_OLT');
        $sheet->setCellValue('N1', 'IP_OLT');
        $sheet->setCellValue('O1', 'HOSTNAME_ONT');
        $sheet->setCellValue('P1', 'IP_ONT');
        $sheet->setCellValue('Q1', 'PORT_ONU');
        $sheet->setCellValue('R1', 'ONT_TYPE');
        $sheet->setCellValue('S1', 'SERIAL_NUMBER');
        $sheet->setCellValue('T1', 'VLAN');
        $sheet->setCellValue('U1', 'BANDWIDTH');
        $sheet->setCellValue('V1', 'ODC');
        $sheet->setCellValue('W1', 'ODP');
        $sheet->setCellValue('X1', 'P_TARIKAN');
        $sheet->setCellValue('Y1', 'STATUS');
        $sheet->setCellValue('Z1', 'KETERANGAN');

        // foreach ($dataFfwan as $d) {
        //     print_r($d->almt);
        // }
        //start coloum
        $coloumn = 2;
        foreach ($dataFfwan as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->tanggal);
            $sheet->setCellValue('C' . $coloumn, $d->sto);
            $sheet->setCellValue('D' . $coloumn, $d->layanan);
            $sheet->setCellValue('E' . $coloumn, $d->no_order);
            $sheet->setCellValue('F' . $coloumn, $d->order_type);
            $sheet->setCellValue('G' . $coloumn, $d->nama);
            $sheet->setCellValue('H' . $coloumn, $d->almt);
            $sheet->setCellValue('I' . $coloumn, $d->tag_lokasi);
            $sheet->setCellValue('J' . $coloumn, $d->hostname_metro);
            $sheet->setCellValue('K' . $coloumn, $d->ip_metro);
            $sheet->setCellValue('L' . $coloumn, $d->port_metro);
            $sheet->setCellValue('M' . $coloumn, $d->hostname_olt);
            $sheet->setCellValue('N' . $coloumn, $d->ip_olt);
            $sheet->setCellValue('O' . $coloumn, $d->hostname_ont);
            $sheet->setCellValue('P' . $coloumn, $d->ip_ont);
            $sheet->setCellValue('Q' . $coloumn, $d->port_onu);
            $sheet->setCellValue('R' . $coloumn, $d->ont_type);
            $sheet->setCellValue('S' . $coloumn, $d->serial_number);
            $sheet->setCellValue('T' . $coloumn, $d->vlan);
            $sheet->setCellValue('U' . $coloumn, $d->bandwidth);
            $sheet->setCellValue('V' . $coloumn, $d->odc);
            $sheet->setCellValue('W' . $coloumn, $d->odp);
            $sheet->setCellValue('X' . $coloumn, $d->p_tarikan);
            $sheet->setCellValue('Y' . $coloumn, $d->status);
            $sheet->setCellValue('Z' . $coloumn, $d->desc);
            $coloumn++;
        }

        $sheet->getStyle('A1:Z1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:Z1')->getFill()
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
        $sheet->getStyle('A1:Z' . ($coloumn - 1))->applyFromArray($styleArray);

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
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->getColumnDimension('X')->setAutoSize(true);
        $sheet->getColumnDimension('Y')->setAutoSize(true);
        $sheet->getColumnDimension('Z')->setAutoSize(true);


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

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('dataFfwan');
        $builder->select('*')->where('dataFfwan.deleted_at IS NOT NULL', null, false);
        $builder->join('datametro',  'datametro.hostname_metro = dataFfwan.hostname_metro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_olt', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_olt', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('platform', $keyword)->where('dataFfwan.deleted_at IS NOT NULL', null, false);
        }
        $query = $builder->get();
        $dataFfwan = $query->getResult();

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
        foreach ($dataFfwan as $d) {
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
            $dataFfwan = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataFfwan as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'tanggal'           => $value[0],
                    'idsto'             => $value[1],
                    'layanan'           => $value[2],
                    'no_order'          => $value[3],
                    'order_type'        => $value[4],
                    'nama'              => $value[5],
                    'alamat'            => $value[6],
                    'tag_lokasi'        => $value[6],
                    'hostname_metro'    => $value[7],
                    'port_metro'        => $value[8],
                    'hostname_olt'      => $value[9],
                    'port_onu'          => $value[10],
                    'hostname_ont'      => $value[11],
                    'ip_ont'            => $value[12],
                    'ont_type'          => $value[13],
                    'serial_number'     => $value[14],
                    'vlan'              => $value[15],
                    'bandwidth'         => $value[16],
                    'odc'               => $value[17],
                    'odp'               => $value[18],
                    'p_tarikan'         => $value[19],
                    'status'            => $value[20],
                    'desc'              => $value[21],
                    'divisi'            => 'WIFI',
                ];

                $this->dataFfwan->insert($data);
            }

            return
                redirect()->back()->with('success', 'Data Excel Berhasil Diimport');
        } else {
            return redirect()->back()->with('error', 'Format File Tidak Sesuai');
        }
    }

    public function trash()
    {

        $data = [
            'title'        => 'FULFILLMENT',
            'dataFfwan'      => $this->dataFfwan->findAll(),
        ];

        return view('wifi/fulfillment/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('ffwan')->set('deleted_at', null, true)->where(['idff' => $id])->update();
        } else {
            $this->db->table('ffwan')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/wifi/fulfillment')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wifi/fulfillment');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataFfwan->delete($id, true);
            return redirect()->to('/wifi/fulfillment/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataFfwan->purgeDeleted();
            return redirect()->to('/wifi/fulfillment/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }

    function detailData()
    {
        if ($this->request->isAJAX()) {

            //get spesific data
            $idff = $this->request->getGet('idff');
            $builder = $this->dataFfwan->GetById(array($idff));

?>
            <div id="accordion" role="tablist" aria-multiselectable="true" class="card-collapse">
                <div class="card card-plain">
                    <div class="card-header" role="tab" id="headingOne">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            #CUSTOMER

                            <i class="fas fa-chevron-down text-right"></i>
                        </a>
                    </div>

                    <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Tanggal</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['tanggal'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>STO</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['idsto'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Layanan</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['layanan'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>No Order</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['no_order'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Order Type</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['order_type'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['nama'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['alamat'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Coordinate</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['tag_lokasi'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <th>:</th>
                                        <td>
                                            <?php if ($builder[0]['status'] == 'OPEN') : ?>
                                                <h6>
                                                    <div class="badge badge-warning"><?= $builder[0]['status'] ?></div>
                                                </h6>
                                            <?php elseif ($builder[0]['status'] == 'CLOSED') : ?>
                                                <h6>
                                                    <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                </h6>
                                            <?php elseif ($builder[0]['status'] == 'REJECT') : ?>
                                                <h6>
                                                    <div class="badge badge-info"><?= $builder[0]['status'] ?></div>
                                                </h6>
                                            <?php else : ?>
                                                <h6>
                                                    <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                </h6>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['desc'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card card-plain">
                    <div class="card-header" role="tab" id="headingTwo">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            #METRO-OLT

                            <i class="fas fa-chevron-down text-right"></i>
                        </a>
                    </div>
                    <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Hostname Metro</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['hostname_metro'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Port Metro</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['port_metro'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Hostname OLT</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['hostname_olt'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>IP OLT</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['ip_olt'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Port Onu</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['port_onu'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card card-plain">
                    <div class="card-header" role="tab" id="headingThree">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            #ONT

                            <i class="fas fa-chevron-down text-right"></i>
                        </a>
                    </div>
                    <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Hostname ONT</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['hostname_ont'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>IP ONT</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['ip_ont'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>ONT Type</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['ont_type'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['serial_number'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Vlan</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['vlan'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Bandwidth</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['bandwidth'] ?><?= ($builder[0]['bandwidth'] != "") ? " Mbps" : "" ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card card-plain">
                    <div class="card-header" role="tab" id="headingFour">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            #OTHERS_DATA

                            <i class="fas fa-chevron-down text-right"></i>
                        </a>
                    </div>
                    <div id="collapseFour" class="collapse" role="tabpanel" aria-labelledby="headingFour">
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">ODC</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['odc'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>ODP</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['odp'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Panjang Tarikan</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['p_tarikan'] ?><?= ($builder[0]['p_tarikan'] != "") ? " Meter" : "" ?></td>
                                    </tr>
                                    <tr>
                                        <th>Evidence</th>
                                        <th>:</th>
                                        <td>
                                            <img class="rounded img-fluid" src="<?= ($builder[0]['evidence'] != '') ? base_url('/img/fulfillment/' . $builder[0]['evidence']) : ''; ?>" alt="<?= $builder[0]['evidence'] ?>">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<?php
        }
    }
}
