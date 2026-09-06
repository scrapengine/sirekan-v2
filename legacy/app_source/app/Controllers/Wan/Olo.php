<?php

namespace App\Controllers\Wan;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OltModel;
use App\Models\OntModel;
use App\Models\MetroModel;
use App\Models\StoModel;
use App\Models\OloModel;
use App\Models\FfwanModel;
use App\Models\AsrwanModel;
use App\Models\CactiModel;
use App\Models\LayananModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Hermawan\DataTables\DataTable;
use phpseclib3\Net\SSH2;
use SoapClient;
use CodeIgniter\Database\BaseBuilder;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;


class Olo extends ResourceController
{
    public function __construct()
    {
        $this->dataOlo = new OloModel();
        $this->dataOlt = new OltModel();
        $this->dataOnt = new OntModel();
        $this->dataMetro = new MetroModel();
        $this->dataSto = new StoModel();
        $this->dataAsrwan = new AsrwanModel();
        $this->dataCacti = new CactiModel();
        $this->dataLayanan = new LayananModel();
        $this->dataFfwan = new FfwanModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */


    public function read()
    {

        $db = \Config\Database::connect();
        $array = ['dataOlo.deleted_at' => null]; //AGR023
        $builder = $db->table('dataOlo')
            // ->select('idolo, dataOlo.idsto as idsto_nodeb, service_id, nama, dataOlo.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, dataolt.type_olt as type_olt, dataOlo.port_metro as port_metro_olo, dataOlo.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, dataOlo.serial_number as serial_number, odc, odp, tikor_site, on_air')
            ->select('dataOlo.idolo as idolo, dataOlo.idsto as idsto_nodeb, service_id, nama, dataOlo.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, dataolt.type_olt as type_olt, dataOlo.port_metro as port_metro_olo, dataOlo.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, dataont.merk as merk, CONCAT(dataont.merk ,"-", dataont.type) as ont_type_gabungan, dataOlo.serial_number as serial_number, odc, odp, tikor_site, on_air, graph_id, cacti.idolo as cacti_idolo')
            ->where($array)
            ->groupBy('idolo')
            ->join('cacti',  'cacti.idolo = dataOlo.idolo', 'left')
            ->join('dataont',  'dataont.serial_number = dataOlo.serial_number')
            ->join('datasto',  'datasto.idsto = dataOlo.idsto')
            ->join('datametro',  'datametro.hostname_metro = dataOlo.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = dataOlo.hostname_olt');

        $builder->groupBy('idolo');
        // $builder->having('COUNT(idolo > 1)');


        $query = $builder->get();
        $dataOlo = $query->getResult();
        // print_r($dataOlo);

        $data = array();

        foreach ($dataOlo as $d) {

            $ont_type = $d->ont_type;
            if (!str_contains($d->ont_type, 'direct')) {
                $ont_type = $d->ont_type_gabungan;
            }

            // $ont_merk = "";
            // if (!str_contains($d->ont_type, 'direct')) {
            //     $ont_merk = $d->merk;
            // }

            $port_metro = $d->port_metro_olo;
            if (str_contains($d->hostname_olt_nodeb, 'GPON')) {
                $port_metro = $d->port_metro_olt;
            }

            $type_olt = "";
            if (str_contains($d->port_onu, ':') && str_contains($d->port_onu, '/')) {
                $type_olt = $d->type_olt;
            } elseif (!str_contains($d->port_onu, ':') && str_contains($d->port_onu, '/')) {
                $type_olt = 'direct' . $d->type_olt;
            } elseif ($d->type_olt == null) {
                $type_olt = 'directMetro';
            }


            $data[] = array(
                // "id" => $d->idolo,
                "sto" => $d->idsto_nodeb,
                "service_id" => $d->service_id,
                "nama" => $d->nama,
                "hostname_metro" => $d->hostname_metro_nodeb,
                "ip_metro" => $d->ip_metro,
                "port_metro" => $port_metro,
                "type_olt" => $type_olt,
                "hostname_olt" => $d->hostname_olt_nodeb,
                "ip_olt" => $d->ip_olt,
                "port_onu" => $d->port_onu,
                "hostname_ont" => $d->hostname_ont,
                "ip_ont" => $d->ip_ont,
                // "merk" => $ont_merk,
                "ont_type" => $ont_type,
                "serial_number" => $d->serial_number,
                "odc" => $d->odc,
                "odp" => $d->odp,
                "tikor_site" => $d->tikor_site,
                "on_air" => $d->on_air,
                "graph_id" => $d->graph_id,
            );
        }
        return $this->respond($data, 200);
    }
    public function index()
    {

        $keyword = $this->request->getGet('keyword');
        $dataOlo = $this->dataOlo->getAll($keyword);
        // $getPaginated = $this->dataOlo->getPaginated(10, $keyword);
        $data = [
            'title'        => 'OLO',
            'dataOlo'      => $dataOlo,
        ];
        return view('wan/olo/index', $data);
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
            'title' => 'OLO',
            'validation' => \config\Services::validation(),
            'dataMetro' => $this->dataMetro->getAll(),
            'dataOlt' => $this->dataOlt->getAll(),
            'dataSto' => $this->dataSto->getAll(),
            'dataLayanan' => $this->dataLayanan->findAll(),
        ];
        return view('wan/olo/new', $data);
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
                'service_id' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Site id tidak boleh kosong',
                    ],
                ],
                'evidence' => [
                    'rules' => 'max_size[evidence,2048]|is_image[evidence]|mime_in[evidence,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran gambar terlalu besar',
                        'is_image' => 'Format tidak sesuai',
                        'mime_in' => 'Format tidak sesuai',
                    ]
                ],
            ]
        )) {
            return redirect()->to('/wan/olo/new')->withInput();
        }

        $data = $this->request->getPost();

        $this->dataOlo->insert($data);

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $service_id = $this->request->getVar('service_id');
        $serial_number = $this->request->getVar('serial_number');
        $dataOnt = $this->dataOnt->where('serial_number', $serial_number)->first();
        // if ($dataOnt) {
        //     $this->db->table('dataont')->set('installed', $installed)->where(['serial_number' => $serial_number])->update();
        //     $this->db->table('dataont')->set('desc', 'Installed to ' . $service_id)->where(['serial_number' => $serial_number])->update();
        // }
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

        // $graph_id = $this->request->getVar('graph_id');

        // if ($graph_id) {
        //     $data = array(
        //         'graph_id'       => $graph_id,
        //         'idolo'     => $this->dataOlo->getInsertID(),
        //     );
        //     $this->dataCacti->insert($data);
        // }

        //change null
        $this->db      = \Config\Database::connect();
        $this->db->table('dataolo')->set('hostname_ont', null, true)->where(['hostname_ont' => ''])->update();
        $this->db->table('dataolo')->set('ip_ont', null, true)->where(['ip_ont' => ''])->update();

        return redirect()->to('/wan/olo')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {

        $dataOlo = $this->dataOlo->find($id);
        $Olt = $dataOlo->hostname_olt;
        if (is_object($dataOlo)) {
            $data = [
                'title'      => 'OLO',
                'validation' => \config\Services::validation(),
                'dataOlo'    => $dataOlo,
                'dataMetro'  => $this->dataMetro->getAll(),
                'dataOlt'    => $this->dataOlt->getAll(),
                'Olt'        => $this->dataOlt->find($Olt),
                'dataSto'    => $this->dataSto->getAll(),
                'dataLayanan'    => $this->dataLayanan->findAll(),
            ];
            return view('wan/olo/edit', $data);
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
                'service_id' => [
                    'rules' => "required",
                    'errors' => [
                        'required' => 'Site id tidak boleh kosong',
                    ],
                ],
                'evidence' => [
                    'rules' => 'max_size[evidence,2048]|is_image[evidence]|mime_in[evidence,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran gambar terlalu besar',
                        'is_image' => 'Format tidak sesuai',
                        'mime_in' => 'Format tidak sesuai',
                    ]
                ],
            ]
        )) {
            return redirect()->to('/wan/olo/' . $id . '/edit')->withInput();
        }

        $data = $this->request->getPost();

        $this->dataOlo->update($id, $data);

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $service_id = $this->request->getVar('service_id');
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

        return redirect()->to('/wan/olo')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->dataOlo->delete($id);

        return redirect()->to('/wan/olo')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {
        if ($this->request->isAJAX()) {

            //get spesific data
            $keyword = $this->request->getGet('keyword');
            $choice = $this->request->getGet('choice');
            $values = $this->request->getGet('values');

            $db = db_connect();
            $array = ['dataolo.deleted_at' => null];
            $builder = $db->table('dataolo')
                ->select('idolo, idff, dataolo.service_id as service_id, ffwan.service_id as sidff, dataolo.idsto as idsto_olo, dataolo.hostname_metro as hostname_metro_olo, ip_metro, dataolt.port_metro as port_metro_olt, dataolo.port_metro as port_metro_olo, dataolo.hostname_olt as hostname_olt_olo, ip_olt, dataolo.layanan as layanan, dataolo.nama as nama, dataolo.alamat as alamat_olo, dataolo.tag_lokasi as tag_lokasi, dataolo.port_onu as port_onu, dataolo.hostname_ont as hostname_ont, dataolo.ip_ont as ip_ont, dataolo.ont_type as ont_type, dataolo.serial_number as serial_number, dataolo.vlan as vlan, dataolo.odc as odc, dataolo.odp as odp, dataolo.desc as desc, dataolo.evidence as evidence,ffwan.bandwidth as bandwidth')
                ->where($array)
                ->join('datasto',  'datasto.idsto = dataolo.idsto')
                ->join('datametro',  'datametro.hostname_metro = dataolo.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = dataolo.hostname_olt')
                ->join('ffwan',  'ffwan.service_id = dataolo.service_id AND ffwan.deleted_at IS NULL');

            // to get last value from group by
            $builder->whereIn('idff', function (BaseBuilder $builder) {
                return $builder->select('MAX(idff)', false)->from('ffwan')->groupBy('service_id');
            });

            if ($values != '' && $choice != '') {
                foreach ($choice as $key => $c) {
                    if ($key == 0) {
                        $builder->like($c, $values[$key])->where($array);
                    } else {
                        $builder->orlike($c, $values[$key])->where($array);
                    }
                }
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column                
                ->add('action', function ($dataOlo) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/olo/detail/$dataOlo->idolo\" type=\"button\" class=\"btn btn-sm btn-outline-dark\">
                    <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </a>
                    <a href=\"/wan/olo/$dataOlo->idolo/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idolo=\"$dataOlo->idolo\" data-service_id=\"$dataOlo->service_id\" data-nama=\"$dataOlo->nama\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                    </div>";
                })
                ->add('port_metro', function ($dataOlo) {
                    if ($dataOlo->port_metro_olt != null) {
                        return $dataOlo->port_metro_olt;
                    }
                    return $dataOlo->port_metro_olo;
                })
                ->edit('nama', function ($dataFfwan) {
                    if (str_contains($dataFfwan->desc, 'Disconnect')) {
                        return '<h6><div class="badge badge-warning font-weight-bold">DO - ' . $dataFfwan->nama . '</div></h6>';
                    }
                    return $dataFfwan->nama;
                })
                ->edit('bandwidth', function ($dataFfwan) {
                    return $dataFfwan->bandwidth . " Mbps";
                })
                ->toJson(true);
        }
    }

    public function listDataTrash()
    {
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = ['dataolo.deleted_at !=' => null];
            $builder = $db->table('dataolo')
                ->select('idolo, service_id, dataolo.idsto as idsto_olo, dataolo.hostname_metro as hostname_metro_olo, ip_metro, dataolt.port_metro as port_metro_olt, dataolo.port_metro as port_metro_olo, dataolo.hostname_olt as hostname_olt_olo, ip_olt, layanan, nama, dataolo.alamat as alamat_olo, tag_lokasi, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, odc, odp, desc, evidence')
                ->where($array)
                ->join('datasto',  'datasto.idsto = dataolo.idsto')
                ->join('datametro',  'datametro.hostname_metro = dataolo.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = dataolo.hostname_olt');

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataOlo) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-outline-dark\" onclick=\"showDetail($dataOlo->idolo)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">
                    <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </button>
                    <a href=\"/wan/olo/restore/$dataOlo->idolo\" type=\"button\" class=\"btn btn-sm btn-info\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idolo=\"$dataOlo->idolo\" data-service_id=\"$dataOlo->service_id\" data-nama=\"$dataOlo->nama\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->add('port_metro', function ($dataOlo) {
                    if ($dataOlo->port_metro_olt != null) {
                        return $dataOlo->port_metro_olt;
                    }
                    return $dataOlo->port_metro_olo;
                })
                ->toJson(true);
        }
    }

    public function export()
    {
        // $this->db      = \Config\Database::connect();
        // $builder = $this->db->table('ffwan')->groupBy('service_id');
        // $query   = $builder->get();
        // return print_r($query->getResult());
        // foreach ($dataFfwan as $d) {
        //     $this->db->table('dataolo')->set('bandwidth', $d->bandwidth)->where(['bandwidth' => null])->update();
        // }

        $filename = 'OLO-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $choice = $this->request->getGet('choice');
        $values = $this->request->getGet('values');

        $db = \Config\Database::connect();
        $array = ['dataolo.deleted_at' => null];
        $builder = $db->table('dataolo')
            ->select('idolo, idff, dataolo.service_id as sid, ffwan.service_id as sidff, dataolo.idsto as idsto_olo, dataolo.hostname_metro as hostname_metro_olo, ip_metro, dataolt.port_metro as port_metro_olt, dataolo.port_metro as port_metro_olo, dataolo.hostname_olt as hostname_olt_olo, ip_olt, dataolo.layanan as layanan, dataolo.nama as nama, dataolo.alamat as alamat_olo, dataolo.tag_lokasi as tag_lokasi, dataolo.port_onu as port_onu, dataolo.hostname_ont as hostname_ont, dataolo.ip_ont as ip_ont, dataolo.ont_type as ont_type, dataolo.serial_number as serial_number, dataolo.vlan as vlan, dataolo.odc as odc, dataolo.odp as odp, dataolo.desc as desc, dataolo.evidence as evidence,ffwan.bandwidth as bandwidth')
            ->where($array)
            ->join('datasto',  'datasto.idsto = dataolo.idsto')
            ->join('datametro',  'datametro.hostname_metro = dataolo.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = dataolo.hostname_olt')
            ->join('ffwan',  'ffwan.service_id = dataolo.service_id');

        // to get last value from group by
        $builder->whereIn('idff', function (BaseBuilder $builder) {
            return $builder->select('MAX(idff)', false)->from('ffwan')->groupBy('service_id');
        });

        if ($values != '' && $choice != '') {
            foreach ($choice as $key => $c) {
                if ($key == 0) {
                    $builder->like($c, $values[$key])->where($array);
                } else {
                    $builder->orlike($c, $values[$key])->where($array);
                }
            }
        }

        $query = $builder->get();
        $dataOlo = $query->getResult();


        $spreadsheet = new Spreadsheet();

        // Sheet 1 (default)
        $sheetActive = $spreadsheet->getActiveSheet();
        $sheetActive->setTitle('Active');

        // Sheet 2 (Disconnect)
        $sheetDisconnect = $spreadsheet->createSheet();
        $sheetDisconnect->setTitle('Disconnect');

        function setHeader($sheet)
        {
            $sheet->setCellValue('A1', 'NO');
            $sheet->setCellValue('B1', 'STO');
            $sheet->setCellValue('C1', 'LAYANAN');
            $sheet->setCellValue('D1', 'SERVICE_ID');
            $sheet->setCellValue('E1', 'NAMA_PELANGGAN');
            $sheet->setCellValue('F1', 'ALAMAT');
            $sheet->setCellValue('G1', 'TAG_LOKASI');
            $sheet->setCellValue('H1', 'BANDWIDTH');
            $sheet->setCellValue('I1', 'HOSTNAME_METRO');
            $sheet->setCellValue('J1', 'IP_METRO');
            $sheet->setCellValue('K1', 'PORT_METRO');
            $sheet->setCellValue('L1', 'HOSTNAME_OLT');
            $sheet->setCellValue('M1', 'IP_OLT');
            $sheet->setCellValue('N1', 'PORT_ONU');
            $sheet->setCellValue('O1', 'VLAN');
            $sheet->setCellValue('P1', 'HOSTNAME_ONT');
            $sheet->setCellValue('Q1', 'IP_ONT');
            $sheet->setCellValue('R1', 'ONT_TYPE');
            $sheet->setCellValue('S1', 'SERIAL_NUMBER');
            $sheet->setCellValue('T1', 'ODC');
            $sheet->setCellValue('U1', 'ODP');
            $sheet->setCellValue('V1', 'KETERANGAN');
        }

        setHeader($sheetActive);
        setHeader($sheetDisconnect);


        $rowActive = 2;
        $rowDisconnect = 2;

        foreach ($dataOlo as $d) {

            // tentukan sheet tujuan
            if (stripos($d->desc, 'Disconnect') !== false) {
                $sheet = $sheetDisconnect;
                $row = $rowDisconnect++;
            } else {
                $sheet = $sheetActive;
                $row = $rowActive++;
            }

            $port_metro = $d->port_metro_olt ?? $d->port_metro_olo;

            $sheet->setCellValue('A'.$row, $row - 1);
            $sheet->setCellValue('B'.$row, $d->idsto_olo);
            $sheet->setCellValue('C'.$row, $d->layanan);
            $sheet->setCellValue('D'.$row, $d->sid);
            $sheet->setCellValue('E'.$row, $d->nama);
            $sheet->setCellValue('F'.$row, $d->alamat_olo);
            $sheet->setCellValue('G'.$row, $d->tag_lokasi);
            $sheet->setCellValue('H'.$row, $d->bandwidth.' Mbps');
            $sheet->setCellValue('I'.$row, $d->hostname_metro_olo);
            $sheet->setCellValue('J'.$row, $d->ip_metro);
            $sheet->setCellValue('K'.$row, $port_metro);
            $sheet->setCellValue('L'.$row, $d->hostname_olt_olo);
            $sheet->setCellValue('M'.$row, $d->ip_olt);
            $sheet->setCellValue('N'.$row, $d->port_onu);
            $sheet->setCellValue('O'.$row, $d->vlan);
            $sheet->setCellValue('P'.$row, $d->hostname_ont);
            $sheet->setCellValue('Q'.$row, $d->ip_ont);
            $sheet->setCellValue('R'.$row, $d->ont_type);
            $sheet->setCellValue('S'.$row, $d->serial_number);
            $sheet->setCellValue('T'.$row, $d->odc);
            $sheet->setCellValue('U'.$row, $d->odp);
            $sheet->setCellValue('V'.$row, $d->desc);
        }


        //start conditional if DO
        $greenStyle = new Style(false, true);
        $greenStyle->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getEndColor()->setARGB(Color::COLOR_YELLOW);
        $greenStyle->getFont()->setColor(new Color(Color::COLOR_DARKRED));

        $cellRange = 'A1:V100';
        $conditionalStyles = [];
        $wizardFactory = new Wizard($cellRange);
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

        $textWizard->contains('Disconnect')
            ->setStyle($greenStyle);
        $conditionalStyles[] = $textWizard->getConditional();

        $sheetActive
            ->getStyle($textWizard->getCellRange())
            ->setConditionalStyles($conditionalStyles);

        $sheetDisconnect
            ->getStyle($textWizard->getCellRange())
            ->setConditionalStyles($conditionalStyles);


        //end

        // header Active
        $sheetActive->getStyle('A1:V1')->getFont()->setBold(true)
            ->getColor()->setARGB(Color::COLOR_WHITE);
        $sheetActive->getStyle('A1:V1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('499ff2');

        // header Disconnect
        $sheetDisconnect->getStyle('A1:V1')->getFont()->setBold(true)
            ->getColor()->setARGB(Color::COLOR_WHITE);
        $sheetDisconnect->getStyle('A1:V1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('499ff2');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ];
        
        $sheetActive->getStyle('A1:V' . ($rowActive - 1))
            ->applyFromArray($styleArray);

        $sheetDisconnect->getStyle('A1:V' . ($rowDisconnect - 1))
            ->applyFromArray($styleArray);

        foreach (range('A', 'V') as $col) {
            $sheetActive->getColumnDimension($col)->setAutoSize(true);
            $sheetDisconnect->getColumnDimension($col)->setAutoSize(true);
        }

        // $sheet->getColumnDimension('A')->setAutoSize(true);
        // $sheet->getColumnDimension('B')->setAutoSize(true);
        // $sheet->getColumnDimension('C')->setAutoSize(true);
        // $sheet->getColumnDimension('D')->setAutoSize(true);
        // $sheet->getColumnDimension('E')->setAutoSize(true);
        // $sheet->getColumnDimension('F')->setAutoSize(true);
        // $sheet->getColumnDimension('G')->setAutoSize(true);
        // $sheet->getColumnDimension('H')->setAutoSize(true);
        // $sheet->getColumnDimension('I')->setAutoSize(true);
        // $sheet->getColumnDimension('J')->setAutoSize(true);
        // $sheet->getColumnDimension('K')->setAutoSize(true);
        // $sheet->getColumnDimension('L')->setAutoSize(true);
        // $sheet->getColumnDimension('M')->setAutoSize(true);
        // $sheet->getColumnDimension('N')->setAutoSize(true);
        // $sheet->getColumnDimension('O')->setAutoSize(true);
        // $sheet->getColumnDimension('P')->setAutoSize(true);
        // $sheet->getColumnDimension('Q')->setAutoSize(true);
        // $sheet->getColumnDimension('R')->setAutoSize(true);
        // $sheet->getColumnDimension('S')->setAutoSize(true);
        // $sheet->getColumnDimension('T')->setAutoSize(true);
        // $sheet->getColumnDimension('U')->setAutoSize(true);
        // $sheet->getColumnDimension('V')->setAutoSize(true);


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
            $dataOlo = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataOlo as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'idsto'             => $value[0],
                    'layanan'           => $value[1],
                    'service_id'        => $value[2],
                    'nama'              => $value[3],
                    'alamat'            => $value[4],
                    'tag_lokasi'        => $value[5],
                    'hostname_metro'    => $value[6],
                    'port_metro'        => $value[7],
                    'hostname_olt'      => $value[8],
                    'port_onu'          => $value[9],
                    'vlan'              => $value[10],
                    'hostname_ont'      => $value[11],
                    'ip_ont'            => $value[12],
                    'ont_type'          => $value[13],
                    'serial_number'     => $value[14],
                    'odc'               => $value[15],
                    'odp'               => $value[16],
                    'desc'              => $value[17],
                ];

                $this->dataOlo->insert($data);
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
        // $dataOlo = $this->dataOlo->getTrash($keyword);
        // $getPaginatedTrash = $this->dataOlo->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'OLO',
            // 'dataOlo'      => $dataOlo,
            // 'dataOlo'      => $getPaginatedTrash['dataOlo'],
            // 'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('wan/olo/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('dataolo')->set('deleted_at', null, true)->where(['idolo' => $id])->update();
        } else {
            $this->db->table('dataolo')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/wan/olo')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wan/olo');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataOlo->delete($id, true);
            return redirect()->to('/wan/olo/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataOlo->purgeDeleted();
            return redirect()->to('/wan/olo/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }

    function detailData($id = null)
    {
        //get spesific data
        // $id = $this->request->getGet('idasr');

        $dataOlo = $this->dataOlo->GetById(array($id));
        $dataAsrwan = $this->dataAsrwan->GetServiceNo($dataOlo[0]['service_id']);
        $dataFfwan = $this->dataFfwan->GetServiceNo($dataOlo[0]['service_id']);

        $data = [
            'title'          => 'OLO',
            'id'             => $id,
            'dataOlo'      => $dataOlo,
            'dataAsrwan'     => $dataAsrwan,
            'dataFfwan'     => $dataFfwan,
        ];
        return view('wan/olo/detail', $data);
    }

    function ukurNodeb()
    {

        if ($this->request->isAJAX()) {
            $id = $this->request->getGet('idolo');
            $dataOlo = $this->dataOlo->GetById(array($id));

            $ssh = new SSH2('10.60.190.16', 22);
            $ssh->login('sp19950104', 'G4rud401');

            $ip = $dataOlo[0]['ip_ont'];

            //cek ONT up / down
            $ssh->enablePTY();
            $ssh->setTimeout(1);
            $ssh->exec('ping ' . $ip);
            $hasil = $ssh->read();
            $ssh->write("\x03");
            if (strpos($hasil, 'icmp_seq=1') !== $hasil = false) {
                $hasil = true;
            }


            //cek ONT
            if ($hasil == 1) {

                $ssh = new SSH2('10.60.190.16', 22);
                $ssh->login('sp19950104', 'G4rud401');

                $ssh->read('[sp19950104@SSH02-ACCESS-STL ~]$');
                $ssh->write("telnet " . $ip . "\n");
                $ssh->write("\n");
                $ssh->write("admin\nadmin_123\nen\nadmin_123\nsho temp\n");

                $ssh->write("sho int gei_0/2/1\n\n");
                $ssh->write("sho mac sl 1\n\nquit\ny\n");
                $str = $ssh->read();

                $pattern = "/(Current temperature\s+:(.*))(?=\s+degree)/mi";
                preg_match_all($pattern, $str, $match);
                $hasilSuhu = $match[2][0] . " degree";

                $pattern = "/(Optical RX power\s+:\s+(.*))(?=\s+Optical TX bias)/mi";
                preg_match_all($pattern, $str, $match);
                $hasilRedaman = $match[2][0];

                $pattern = "/(Optical RX power\s+:\s+(.*))(?=\s+Optical TX bias)/mi";
                preg_match_all($pattern, $str, $match);
                $hasilMac = $match[2][0];
            } else {
                $hasilSuhu = "-";
                $hasilRedaman = "-";
                $str = "-";
            }


?>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Current temperature</th>
                                    <th>:</th>
                                    <td><?= $hasilSuhu ?></td>
                                </tr>
                                <tr>
                                    <th>Optical RX power</th>
                                    <th>:</th>
                                    <td><?= $hasilRedaman ?></td>
                                </tr>
                                <tr>
                                    <th>Mac Downlink</th>
                                    <th>:</th>
                                    <td>Still stuck</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<?php
        }
    }
}
