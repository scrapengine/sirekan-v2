<?php

namespace App\Controllers\Wan;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OltModel;
use App\Models\OntModel;
use App\Models\MetroModel;
use App\Models\StoModel;
use App\Models\FfwanModel;
use App\Models\OloModel;
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
        $this->dataOlo = new OloModel();
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

        return view('wan/fulfillment/index', $data);
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
        return view('wan/fulfillment/new', $data);
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
            return redirect()->to('/wan/fulfillment/new')->withInput();
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
            'divisi' => 'WAN',
            'evidence' => $name_evidence,
        ];

        $array_merge = array_merge($data, $addData);
        $this->dataFfwan->insert($array_merge);

        $service_id = $this->request->getVar('service_id');
        if (strlen($service_id) > 0) {
            $exist = $this->dataOlo->where('service_id', $service_id)->first();
            $tanggal = $this->request->getVar('tanggal');
            $order_type = $this->request->getVar('order_type');
            $no_order = $this->request->getVar('no_order');
            $desc = $this->request->getVar('desc');
            $addData = [
                'desc' => "$tanggal | $order_type | $no_order | $desc",
            ];
            $array_merge = array_merge($array_merge, $addData);
            if ($exist) {
                $id = $exist->idolo;
                $this->dataOlo->update($id, $array_merge);
            } else {
                $this->dataOlo->insert($array_merge);
            }
        }

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $nama = $this->request->getVar('nama');
        $serial_number = $this->request->getVar('serial_number');
        $dataOnt = $this->dataOnt->where('serial_number', $serial_number)->first();
        if ($dataOnt) {
            $this->db->table('dataont')
                // Kolom installed hanya akan diisi sekali (saat masih kosong/NULL),
                // jika sudah ada nilainya maka tidak akan diubah lagi
                ->set(
                    'installed',
                    "IF(`installed` IS NULL OR `installed` = '', '$installed', `installed`)",
                    false
                )

                // Kolom desc akan digunakan sebagai log / catatan history
                // Aturan:
                // - Jika desc kosong/NULL → isi dengan "$installed $nama"
                // - Jika desc sudah ada:
                //     • Cek apakah "$nama" sudah pernah tercatat → kalau ya, biarkan (tidak ditambah lagi)
                //     • Kalau belum → tambahkan baris baru dengan format "$installed $nama"
                ->set(
                    'desc',
                    "IF(`desc` = '' OR `desc` IS NULL, 
                        '$installed $nama', 
                        IF(LOCATE('$nama', `desc`) > 0, 
                            `desc`, 
                            CONCAT(`desc`, '\n$installed $nama')
                        )
                    )",
                    false
                )

                // Update berdasarkan serial_number ONT
                ->where(['serial_number' => $serial_number])
                ->update();
        }

        //change null
        $this->db      = \Config\Database::connect();
        $this->db->table('dataolo')->set('hostname_ont', null, true)->where(['hostname_ont' => ''])->update();
        $this->db->table('dataolo')->set('ip_ont', null, true)->where(['ip_ont' => ''])->update();

        return redirect()->to('/wan/fulfillment')->with('success', 'Data berhasil disimpan.');
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
            return view('wan/fulfillment/edit', $data);
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
            return redirect()->to('/wan/fulfillment/' . $id . '/edit')->withInput();
        }
        // return var_dump($this->request->getVar('save_as'));
        $save_as = $this->request->getVar('save_as');
        if ($save_as == "on") {

            $name_evidence = $this->request->getVar('evidence_old');
            $data = $this->request->getPost();
            $addData = [
                'divisi' => 'WAN',
                'evidence' => $name_evidence,
            ];

            $array_merge = array_merge($data, $addData);
            $this->dataFfwan->insert($array_merge);

            // add to dataOlo
            $service_id = $this->request->getVar('service_id');
            if (strlen($service_id) > 0) {
                $exist = $this->dataOlo->where('service_id', $service_id)->first();
                $tanggal = $this->request->getVar('tanggal');
                $order_type = $this->request->getVar('order_type');
                $no_order = $this->request->getVar('no_order');
                $desc = $this->request->getVar('desc');
                $addData = [
                    'desc' => "$tanggal | $order_type | $no_order | $desc",
                ];
                $array_merge = array_merge($array_merge, $addData);
                if ($exist) {
                    $id = $exist->idolo;
                    $this->dataOlo->update($id, $array_merge);
                } else {
                    $this->dataOlo->insert($array_merge);
                }
            }
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
                'divisi' => 'WAN',
                'evidence' => $name_evidence,
            ];

            $array_merge = array_merge($data_post, $addData);
            $this->dataFfwan->update($id, $array_merge);

            //add to dataOlo
            $service_id = $this->request->getVar('service_id');
            if (strlen($service_id) > 0) {
                $exist = $this->dataOlo->where('service_id', $service_id)->first();
                $tanggal = $this->request->getVar('tanggal');
                $order_type = $this->request->getVar('order_type');
                $no_order = $this->request->getVar('no_order');
                $desc = $this->request->getVar('desc');
                $addData = [
                    'desc' => "$tanggal | $order_type | $no_order | $desc",
                ];
                $array_merge = array_merge($array_merge, $addData);
                if ($exist) {
                    $id = $exist->idolo;
                    $this->dataOlo->update($id, $array_merge);
                } else {
                    $this->dataOlo->insert($array_merge);
                }
            }
        }

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $nama = $this->request->getVar('nama');
        $serial_number = $this->request->getVar('serial_number');
        $dataOnt = $this->dataOnt->where('serial_number', $serial_number)->first();
        if ($dataOnt) {
            $this->db->table('dataont')
                // Kolom installed hanya akan diisi sekali (saat masih kosong/NULL),
                // jika sudah ada nilainya maka tidak akan diubah lagi
                ->set(
                    'installed',
                    "IF(`installed` IS NULL OR `installed` = '', '$installed', `installed`)",
                    false
                )

                // Kolom desc akan digunakan sebagai log / catatan history
                // Aturan:
                // - Jika desc kosong/NULL → isi dengan "$installed $nama"
                // - Jika desc sudah ada:
                //     • Cek apakah "$nama" sudah pernah tercatat → kalau ya, biarkan (tidak ditambah lagi)
                //     • Kalau belum → tambahkan baris baru dengan format "$installed $nama"
                ->set(
                    'desc',
                    "IF(`desc` = '' OR `desc` IS NULL, 
                        '$installed $nama', 
                        IF(LOCATE('$nama', `desc`) > 0, 
                            `desc`, 
                            CONCAT(`desc`, '\n$installed $nama')
                        )
                    )",
                    false
                )

                // Update berdasarkan serial_number ONT
                ->where(['serial_number' => $serial_number])
                ->update();
        }


        //change null
        $this->db      = \Config\Database::connect();
        $this->db->table('dataolo')->set('hostname_ont', null, true)->where(['hostname_ont' => ''])->update();
        $this->db->table('dataolo')->set('ip_ont', null, true)->where(['ip_ont' => ''])->update();

        return redirect()->to('/wan/fulfillment')->with('success', 'Data berhasil diupdate.');
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

        return redirect()->to('/wan/fulfillment')->with('success', 'Data berhasil dihapus.');
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
            $array = ['ffwan.deleted_at' => null, 'divisi' => 'WAN'];
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
                    <a href=\"/wan/fulfillment/$dataFfwan->idff/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
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
            $array = ['ffwan.deleted_at !=' => null, 'divisi' => 'WAN'];
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
                    <a href=\"/wan/fulfillment/restore/$dataFfwan->idff\" type=\"button\" class=\"btn btn-sm btn-info\">
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
        $array = ['ffwan.deleted_at' => null, 'divisi' => 'WAN'];
        $builder = $db->table('ffwan');
        $builder->select('*, ffwan.idsto as sto, ffwan.hostname_metro as hostname_metro_ff, ffwan.hostname_olt as hostname_olt_ff, ffwan.alamat as almt, ffwan.port_metro as port_metro_ff, dataolt.port_metro as port_metro_olt')
            ->where($array)
            ->orderBy('tanggal', 'DESC')
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
        $sheet->setCellValue('B1', 'TANGGAL ORDER');
        $sheet->setCellValue('C1', 'TANGGAL INSTALL');
        $sheet->setCellValue('D1', 'TANGGAL PS');
        $sheet->setCellValue('E1', 'STO');
        $sheet->setCellValue('F1', 'LAYANAN');
        $sheet->setCellValue('G1', 'NO_ORDER');
        $sheet->setCellValue('H1', 'ORDER_TYPE');
        $sheet->setCellValue('I1', 'NAMA_PELANGGAN');
        $sheet->setCellValue('J1', 'ALAMAT');
        $sheet->setCellValue('K1', 'TAG_LOKASI');
        $sheet->setCellValue('L1', 'HOSTNAME_METRO');
        $sheet->setCellValue('M1', 'IP_METRO');
        $sheet->setCellValue('N1', 'PORT_METRO');
        $sheet->setCellValue('O1', 'HOSTNAME_OLT');
        $sheet->setCellValue('P1', 'IP_OLT');
        $sheet->setCellValue('Q1', 'HOSTNAME_ONT');
        $sheet->setCellValue('R1', 'IP_ONT');
        $sheet->setCellValue('S1', 'PORT_ONU');
        $sheet->setCellValue('T1', 'ONT_TYPE');
        $sheet->setCellValue('U1', 'SERIAL_NUMBER');
        $sheet->setCellValue('V1', 'VLAN');
        $sheet->setCellValue('W1', 'BANDWIDTH');
        $sheet->setCellValue('X1', 'ODC');
        $sheet->setCellValue('Y1', 'ODP');
        $sheet->setCellValue('Z1', 'DROPCORE');
        $sheet->setCellValue('AA1', 'KABEL LAN');
        $sheet->setCellValue('AB1', 'STATUS');
        $sheet->setCellValue('AC1', 'SERVICE ID');
        $sheet->setCellValue('AD1', 'KETERANGAN');

        // foreach ($dataFfwan as $d) {
        //     print_r($d->almt);
        // }
        //start coloum
        $coloumn = 2;
        foreach ($dataFfwan as $d) {
            $port_metro = "";
            if ($d->port_metro_olt != null) {
                $port_metro = $d->port_metro_olt;
            }
            $port_metro = $d->port_metro_ff;

            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->tanggal);
            $sheet->setCellValue('C' . $coloumn, $d->tanggal_install);
            $sheet->setCellValue('D' . $coloumn, $d->tanggal_ps);
            $sheet->setCellValue('E' . $coloumn, $d->sto);
            $sheet->setCellValue('F' . $coloumn, $d->layanan);
            $sheet->setCellValue('G' . $coloumn, $d->no_order);
            $sheet->setCellValue('H' . $coloumn, $d->order_type);
            $sheet->setCellValue('I' . $coloumn, $d->nama);
            $sheet->setCellValue('J' . $coloumn, $d->almt);
            $sheet->setCellValue('K' . $coloumn, $d->tag_lokasi);
            $sheet->setCellValue('L' . $coloumn, $d->hostname_metro_ff);
            $sheet->setCellValue('M' . $coloumn, $d->ip_metro);
            $sheet->setCellValue('N' . $coloumn, $port_metro);
            $sheet->setCellValue('O' . $coloumn, $d->hostname_olt_ff);
            $sheet->setCellValue('P' . $coloumn, $d->ip_olt);
            $sheet->setCellValue('Q' . $coloumn, $d->hostname_ont);
            $sheet->setCellValue('R' . $coloumn, $d->ip_ont);
            $sheet->setCellValue('S' . $coloumn, $d->port_onu);
            $sheet->setCellValue('T' . $coloumn, $d->ont_type);
            $sheet->setCellValue('U' . $coloumn, $d->serial_number);
            $sheet->setCellValue('V' . $coloumn, $d->vlan);
            $sheet->setCellValue('W' . $coloumn, $d->bandwidth);
            $sheet->setCellValue('X' . $coloumn, $d->odc);
            $sheet->setCellValue('Y' . $coloumn, $d->odp);
            $sheet->setCellValue('Z' . $coloumn, $d->p_tarikan);
            $sheet->setCellValue('AA' . $coloumn, $d->lan);
            $sheet->setCellValue('AB' . $coloumn, $d->status);
            $sheet->setCellValue('AC' . $coloumn, $d->service_id);
            $sheet->setCellValue('AD' . $coloumn, $d->desc);
            $coloumn++;
        }

        $sheet->getStyle('A1:AD1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:AD1')->getFill()
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
        $sheet->getStyle('A1:AD' . ($coloumn - 1))->applyFromArray($styleArray);

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
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->getColumnDimension('AB')->setAutoSize(true);
        $sheet->getColumnDimension('AC')->setAutoSize(true);
        $sheet->getColumnDimension('AD')->setAutoSize(true);


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
                    'tanggal'           => $value[0] != null ? date('Y-m-d', strtotime($value[0])) : $value[0],
                    'tanggal_install'   => $value[1] != null ? date('Y-m-d', strtotime($value[1])) : $value[1],
                    'tanggal_ps'        => $value[2] != null ? date('Y-m-d', strtotime($value[2])) : $value[2],
                    'idsto'             => $value[3],
                    'layanan'           => $value[4],
                    'no_order'          => $value[5],
                    'order_type'        => $value[6],
                    'nama'              => $value[7],
                    'alamat'            => $value[8],
                    'tag_lokasi'        => $value[9],
                    'hostname_metro'    => $value[10] != null ? $value[10] : '-',
                    'port_metro'        => $value[11],
                    'hostname_olt'      => $value[12] != null ? $value[12] : '-',
                    'port_onu'          => $value[13],
                    'hostname_ont'      => $value[14],
                    'ip_ont'            => $value[15],
                    'ont_type'          => $value[16],
                    'serial_number'     => $value[17],
                    'vlan'              => $value[18],
                    'bandwidth'         => $value[19],
                    'odc'               => $value[20],
                    'odp'               => $value[21],
                    'p_tarikan'         => $value[22],
                    'lan'               => $value[23],
                    'status'            => $value[24],
                    'service_id'        => $value[25],
                    'desc'              => $value[26],
                    'divisi'            => 'WAN',
                ];

                $exist = $this->dataFfwan->where('no_order', $value[5])->first();
                if ($exist) {
                    $id = $exist->idff;
                    $this->dataFfwan->update($id, $data);
                    //add to dataOlo
                    $service_id = $value[25];
                    if (strlen($service_id) > 0) {
                        $exist = $this->dataOlo->where('service_id', $service_id)->first();
                        $tanggal = $value[0];
                        $order_type = $value[6];
                        $no_order = $value[5];
                        $desc = $value[26];
                        $addData = [
                            'desc' => "$tanggal | $order_type | $no_order | $desc",
                        ];
                        $array_merge = array_merge($data, $addData);
                        if ($exist) {
                            $id = $exist->idolo;
                            $this->dataOlo->update($id, $array_merge);
                        } else {
                            $this->dataOlo->insert($array_merge);
                        }
                    }
                } else {
                    $this->dataFfwan->insert($data);
                    //add to dataOlo
                    $service_id = $value[25];
                    if (strlen($service_id) > 0) {
                        $exist = $this->dataOlo->where('service_id', $service_id)->first();
                        $tanggal = $value[0];
                        $order_type = $value[6];
                        $no_order = $value[5];
                        $desc = $value[26];
                        $addData = [
                            'desc' => "$tanggal | $order_type | $no_order | $desc",
                        ];
                        $array_merge = array_merge($data, $addData);
                        if ($exist) {
                            $id = $exist->idolo;
                            $this->dataOlo->update($id, $array_merge);
                        } else {
                            $this->dataOlo->insert($array_merge);
                        }
                    }
                }
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

        return view('wan/fulfillment/trash', $data);
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
            return redirect()->to('/wan/fulfillment')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wan/fulfillment');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataFfwan->delete($id, true);
            return redirect()->to('/wan/fulfillment/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataFfwan->purgeDeleted();
            return redirect()->to('/wan/fulfillment/trash')->with('success', 'Data berhasil dihapus permanen.');
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
                                        <th style="width: 30%">Tanggal Order</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['tanggal'] ?></td>
                                    </tr>
                                    <?php if ($builder[0]['tanggal_install'] != null) : ?>
                                        <tr>
                                            <th style="width: 30%">Tanggal Install</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['tanggal_install'] ?></td>
                                        </tr>
                                    <?php endif ?>
                                    <?php if ($builder[0]['tanggal_ps'] != null) : ?>
                                        <tr>
                                            <th style="width: 30%">Tanggal PS</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['tanggal_ps'] ?></td>
                                        </tr>
                                    <?php endif ?>
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
                                    <tr>
                                        <th>Service ID</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['service_id'] ?></td>
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
                                        <th>Dropcore</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['p_tarikan'] ?><?= ($builder[0]['p_tarikan'] != "") ? " Meter" : "" ?></td>
                                    </tr>
                                    <tr>
                                        <th>Kabel LAN</th>
                                        <th>:</th>
                                        <td><?= $builder[0]['lan'] ?><?= ($builder[0]['lan'] != "") ? " Meter" : "" ?></td>
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
