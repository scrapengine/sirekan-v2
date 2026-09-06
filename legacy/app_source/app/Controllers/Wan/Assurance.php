<?php

namespace App\Controllers\Wan;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OltModel;
use App\Models\OntModel;
use App\Models\MetroModel;
use App\Models\StoModel;
use App\Models\AsrwanModel;
use App\Models\NakerModel;
use App\Models\LayananModel;
use App\Models\JobasrModel;
use App\Models\WorklogsAsrModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use \Hermawan\DataTables\DataTable;

use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Files\File;
use CodeIgniter\I18n\Time;
use phpDocumentor\Reflection\Types\Null_;

class Assurance extends ResourceController
{
    protected $helpers = ['custom'];
    public function __construct()
    {
        $this->dataAsrwan = new AsrwanModel();
        $this->dataOlt = new OltModel();
        $this->dataOnt = new OntModel();
        $this->dataMetro = new MetroModel();
        $this->dataSto = new StoModel();
        $this->dataLayanan = new LayananModel();
        $this->dataNaker = new NakerModel();
        $this->dataJobasr = new JobasrModel();
        $this->dataWorklogsAsr = new WorklogsAsrModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */


    public function searchTicket()
    {

        $this->db      = \Config\Database::connect();
        $tiket = $this->request->getGet('s');
        $builder = $this->db->table('dataont');
        $data = [];
        $dataJob = [];
        $dataWorklog = [];
        if ($tiket != null) {
            $builder = $this->db->table('assurance_wan')
                ->select('*')
                ->where('incident', $tiket);

            $query   = $builder->get();
            $dataAsrwan = $query->getResult();

            foreach ($dataAsrwan as $d) {
                $id = $d->idasr;
            }
            if (count($dataAsrwan) < 1) {
                $hasil = [
                    'data' => "data tidak ditemukan",
                    //  'count' => count($data),
                ];

                return $this->respond($hasil, 200);
            }
            $dataAsr = $this->dataAsrwan->GetById(array($id));
            $dataJobasr    = $this->dataJobasr->getJob($id);
            $dataWorklogsAsr    = $this->dataWorklogsAsr->getWorklogs($id);

            if ($dataAsr[0]['status'] == 'BACKEND') {
                $day1 = $dataAsr[0]['reported_date'];
                $day1 = strtotime($day1);
                $day2 = date("Y-m-d H:i:s");
                $day2 = strtotime($day2);

                $diffHours = ($day2 - $day1) / 3600;

                $dataAsr[0]['ttr_customer'] = strval(round($diffHours, 2));
            }
            foreach ($dataJobasr as $d) {

                $dataJob[] = array(
                    "nik" => $d->nik,
                    "nama" => $d->nama,
                    "no_hp" => $d->no_hp,
                    "jobdesk" => $d->jobdesk,
                    "idsto" => $d->idsto,
                    "labor" => $d->labor,
                );
            }

            foreach ($dataWorklogsAsr as $d) {

                $dataWorklog[] = array(
                    "record" => $d->record,
                    "created_by" => $d->created_by,
                    "og" => $d->og,
                    "date" => $d->date,
                    "summ" => $d->summ,
                );
            }

            array_push($data, $dataAsr);
            array_push($data, $dataJob);
            array_push($data, $dataWorklog);
        }


        $hasil = [
            'data' => $data,
            //  'count' => count($data),
        ];


        return $this->respond($hasil, 200);
    }


    public function index()
    {
        $data = [
            'title'        => 'Assurance',
            'dataAsrwan'    => $this->dataAsrwan->findAll(),
        ];
        return view('wan/assurance/index', $data);
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
            'title' => 'Assurance',
            'validation' => \config\Services::validation(),
            'dataMetro' => $this->dataMetro->getAll(),
            'dataOlt' => $this->dataOlt->getAll(),
            'dataSto' => $this->dataSto->getAll(),
            'dataLayanan' => $this->dataLayanan->findAll(),
            'dataNaker' => $this->dataNaker->findAll(),
        ];
        return view('wan/assurance/new', $data);
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
                'incident' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nomor tiket tidak boleh kosong',
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
                // 'worklogs' => [
                //     'rules' => 'mime_in[worklogs,xlxs,xls]',
                //     'errors' => [
                //         'mime_in' => 'Format tidak sesuai',
                //     ]
                // ]
            ]
        )) {
            return redirect()->to('/wan/assurance/new')->withInput();
        }

        $file = $this->request->getFile('worklogs');

        // if ($file) {
        //     if (!$this->validate(
        //         [
        //             'worklogs' => [
        //                 'rules' => 'mime_in[worklogs,xlxs,xls]',
        //                 'errors' => [
        //                     'mime_in' => 'Format tidak sesuai',
        //                 ]
        //             ]
        //         ]
        //     )) {
        //         return redirect()->to('/wan/assurance/new')->withInput();
        //     }
        // }

        $evidence = $this->request->getFile('evidence');

        if ($evidence->getError()) {
            $name_evidence = "";
        } else {

            // generate nama file random
            $name_evidence = $evidence->getRandomName();

            // pindahkan gambar
            $evidence->move('img/assurance', $name_evidence);
        }


        $data = $this->request->getPost();
        $addData = [
            'divisi' => 'WAN',
            'evidence' => $name_evidence,
        ];

        $array_merge = array_merge($data, $addData);
        $this->dataAsrwan->insert($array_merge);

        $petugas = $this->request->getVar('petugas');
        // $separator = explode(',', $petugas);
        if ($petugas) {
            $data = [];
            $no = 0;
            foreach ($petugas as $s) {
                $datas[$s] = array(
                    'idasr'       => $this->dataAsrwan->getInsertID(),
                    'idnaker'     => $s,
                );

                $this->dataJobasr->insert($datas[$s]);
                $no++;
            }
        }


        if (!$file->getError()) {
            $extension = $file->getClientExtension();

            if ($extension == 'xlsx' || $extension == 'xls') {

                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
                $reader->setReadDataOnly(TRUE);

                // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

                $spreadsheet = $reader->load($file);
                $dataAsrwan = $spreadsheet->getActiveSheet()->toArray();
                foreach ($dataAsrwan as $d => $value) {
                    if ($d == 0) {
                        continue;
                    }

                    $data = [
                        'record'                 => $value[0],
                        'created_by'             => $value[2],
                        'owner_group'            => $value[3],
                        'date'                   => $value[4] != null ? date('Y-m-d H:i:s', strtotime($value[4])) : $value[4],
                        'summary'                => $value[6],
                        'idasr'                  => $this->dataAsrwan->getInsertID(),
                    ];

                    $this->dataWorklogs->insert($data);
                }
            } else {
                return redirect()->to('/wan/assurance/new')->with('error', 'Format File Worklogs Tidak Sesuai')->withInput();
            }
        }
        // change null
        $this->db      = \Config\Database::connect();
        $this->db->table('assurance_wan')->set('kategori_site_tsel', null, true)->where(['kategori_site_tsel' => '', 'divisi' => 'WAN'])->update();

        return redirect()->to('/wan/assurance')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $dataAsrwan = $this->dataAsrwan->find($id);
        // $Olt = $dataAsrwan->hostname_olt;
        if (is_object($dataAsrwan)) {
            $data = [
                'title'      => 'Assurance',
                'validation' => \config\Services::validation(),
                'dataAsrwan'  => $dataAsrwan,
                'dataMetro'  => $this->dataMetro->getAll(),
                'dataOlt'    => $this->dataOlt->getAll(),
                // 'Olt'        => $this->dataOlt->find($Olt),
                'dataSto'    => $this->dataSto->getAll(),
                'dataLayanan'    => $this->dataLayanan->findAll(),
                'dataJobasr'    => $this->dataJobasr->getJob($id),
                'dataNaker'    => $this->dataNaker->findAll(),
            ];
            return view('wan/assurance/edit', $data);
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
                'incident' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nomor tiket tidak boleh kosong',
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
                // 'worklogs' => [
                //     'rules' => 'mime_in[worklogs,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel, application/vnd.ms-excel [official], application/msexcel, application/x-msexcel, application/x-ms-excel, application/x-excel, application/x-dos_ms_excel, application/xls, application/x-xls, application/excel, application/download]',
                //     'errors' => [
                //         'mime_in' => 'Format tidak sesuai',
                //     ],
                // ],
            ]
        )) {
            return redirect()->to('/wan/assurance/' . $id . '/edit')->withInput();
        }

        $file = $this->request->getFile('worklogs');

        // if ($file) {
        //     if (!$this->validate(
        //         [
        //             'worklogs' => [
        //                 'rules' => 'mime_in[worklogs,xlxs,xls]',
        //                 'errors' => [
        //                     'mime_in' => 'Format tidak sesuai',
        //                 ],
        //             ],
        //         ]
        //     )) {
        //         return redirect()->to('/wan/assurance/' . $id . '/edit')->withInput();
        //     }
        // }
        // return print_r($file->getClientExtension());

        $evidence = $this->request->getFile('evidence');

        // cek gambar, apakah tetap gambar lama
        if ($evidence->getError() == 4) {
            $name_evidence = $this->request->getVar('evidence_old');
        } else {

            // generate nama file random
            $name_evidence = $evidence->getRandomName();

            // pindahkan gambar
            $evidence->move('img/assurance', $name_evidence);

            //hapus gambar lama
            if ($this->request->getVar('evidence_old') != "") {
                unlink('img/assurance/' . $this->request->getVar('evidence_old'));
            }
        }

        $data = $this->request->getPost();
        $addData = [
            'divisi' => 'WAN',
            'evidence' => $name_evidence,
        ];
        $array_merge = array_merge($data, $addData);
        $this->dataAsrwan->update($id, $array_merge);

        $petugas = $this->request->getVar('petugas');
        // $separator = array(implode(',', $petugas));
        // return print_r($separator);
        $this->db      = \Config\Database::connect();
        $this->db->table('job_assurance')->where(['idasr' => $id])->delete(); //delete if exist

        if ($petugas) {
            $data = [];
            $no = 0;
            foreach ($petugas as $s) {
                $datas[$s] = array(
                    'idasr'       => $id,
                    'idnaker'     => $s,
                );

                $this->dataJobasr->insert($datas[$s]);
                $no++;
            }
        }

        if (!$file->getError()) {
            $extension = $file->getClientExtension();

            if ($extension == 'xlsx' || $extension == 'xls') {

                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
                $reader->setReadDataOnly(TRUE);

                // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

                $spreadsheet = $reader->load($file);
                $dataAsrwan = $spreadsheet->getActiveSheet()->toArray();
                $this->db->table('worklogs_asr')->where(['idasr' => $id])->delete(); //delete if exist
                foreach ($dataAsrwan as $d => $value) {
                    if ($d == 0) {
                        continue;
                    }

                    $data = [
                        'record'                 => $value[0],
                        'created_by'             => $value[2],
                        'owner_group'            => $value[3],
                        'date'                   => $value[4] != null ? date('Y-m-d H:i:s', strtotime($value[4])) : $value[4],
                        'summary'                => $value[6],
                        'idasr'                  => $id,
                    ];

                    $this->dataWorklogsAsr->insert($data);
                }
            } else {
                return redirect()->to('/wan/assurance/' . $id . '/edit')->with('error', 'Format File Worklogs Tidak Sesuai')->withInput();
            }
        }
        // change null
        $this->db      = \Config\Database::connect();
        $this->db->table('assurance_wan')->set('kategori_site_tsel', null, true)->where(['kategori_site_tsel' => '', 'divisi' => 'WAN'])->update();

        return redirect()->to('/wan/assurance')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->dataAsrwan->delete($id);

        return redirect()->to('/wan/assurance')->with('success', 'Data berhasil dihapus.');
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
            $addtime = ' 23:59:59';

            $db = db_connect();
            $array = ['assurance_wan.deleted_at' => null, 'divisi' => 'WAN'];
            $builder = $db->table('assurance_wan')
                ->select('idasr, incident, customer_name, summary, owner_group, owner, external_ticketid, customer_segment, service_no, service_type, reported_date, lapul, gaul, ttr_end_to_end, ttr_customer, ttr_nasional, ttr_regional, ttr_witel, ttr_mitra, ttr_agent, ttr_pending, pending_reason, status, status_date, resolved_by, workzone, witel, regional, actual_solution, incident_domain, resolved_date, jumlah_site_tsel_nossa, kategori_site_tsel, impacted_site_tsel,divisi')
                ->where($array);

            if ($fromdate != '' && $untildate != '') {
                $builder->where('reported_date BETWEEN "' . $fromdate . '" and "' . $untildate . $addtime . '"');
            }

            if ($values != '' && $choice != '') {
                foreach ($choice as $key => $c) {
                    if ($key == 0) {
                        $builder->like($c, $values[$key])->where($array)->where('reported_date BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                    } else {
                        $builder->orlike($c, $values[$key])->where($array)->where('reported_date BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                    }
                }
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column                
                ->add('action', function ($dataAsrwan) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <button type=\"button\" class=\"btn btn-sm btn-outline-dark\" onclick=\"showDetail($dataAsrwan->idasr)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">
                    <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </button>
                    <a href=\"/wan/assurance/$dataAsrwan->idasr/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idasr=\"$dataAsrwan->idasr\" data-incident=\"$dataAsrwan->incident\" data-customer_name=\"$dataAsrwan->customer_name\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                    </div>";
                })
                ->edit('incident', function ($dataAsrwan) {
                    if (!in_groups(['superadmin', 'admin'])) {
                        return "<a type=\"button\" class=\"btn-link\" onclick=\"showDetail($dataAsrwan->idasr)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">$dataAsrwan->incident</a>";
                    }
                    return $dataAsrwan->incident;
                    // <div class=\"table-links\">
                    //           <a href=\"#\">View</a>
                    //           <div class=\"bullet\"></div>
                    //           <a href=\"#\">Edit</a>
                    //           <div class=\"bullet\"></div>
                    //           <a href=\"#\" class=\"text-danger\">Trash</a>
                    //         </div>";
                })
                ->edit('status', function ($dataAsrwan) {
                    if (in_array($dataAsrwan->status, ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'])) {
                        return '<h6><div class="badge badge-danger font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif (in_array($dataAsrwan->status, ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'])) {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif (in_array($dataAsrwan->status, ['PENDING', 'PENDINGS', 'SLAHOLD'])) {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                })
                ->edit('ttr_customer', function ($dataAsrwan) {
                    if ($dataAsrwan->status == 'BACKEND') {
                        $day1 = $dataAsrwan->reported_date;
                        $day1 = strtotime($day1);
                        $day2 = date("Y-m-d H:i:s");
                        $day2 = strtotime($day2);

                        $diffHours = ($day2 - $day1) / 3600;

                        return round($diffHours, 2);
                    }
                    return $dataAsrwan->ttr_customer;
                })
                ->toJson(true);
        }
    }


    public function listDataTrash()
    {

        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = ['assurance_wan.deleted_at !=' => null, 'divisi' => 'WAN'];
            $builder = $db->table('assurance_wan')
                ->select('idasr, incident, customer_name, summary, owner_group, owner, external_ticketid, customer_segment, service_no, service_type, reported_date, lapul, gaul, ttr_end_to_end, ttr_customer, ttr_nasional, ttr_regional, ttr_witel, ttr_mitra, ttr_agent, ttr_pending, pending_reason, status, status_date, resolved_by, workzone, witel, regional, actual_solution, incident_domain, resolved_date, jumlah_site_tsel_nossa, kategori_site_tsel, impacted_site_tsel')
                ->where($array);


            if ($keyword != '') {
                $builder->like('incident', $keyword)->where($array);
                $builder->orLike('customer_name', $keyword)->where($array);
                $builder->orLike('customer_segment', $keyword)->where($array);
                $builder->orLike('service_no', $keyword)->where($array);
                $builder->orLike('reported_date', $keyword)->where($array);
                $builder->orLike('status', $keyword)->where($array);
                $builder->orLike('workzone', $keyword)->where($array);
            };

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataAsrwan) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-outline-dark\" onclick=\"showDetail($dataAsrwan->idasr)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">
                    <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </button>
                    <a href=\"/wan/assurance/restore/$dataAsrwan->idasr\" type=\"button\" class=\"btn btn-sm btn-info\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idasr=\"$dataAsrwan->idasr\" data-incident=\"$dataAsrwan->incident\" data-customer_name=\"$dataAsrwan->customer_name\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->edit('status', function ($dataAsrwan) {
                    if ($dataAsrwan->status == 'BACKEND') {
                        return '<h6><div class="badge badge-danger font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif ($dataAsrwan->status == 'CLOSED') {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif ($dataAsrwan->status == 'PENDING') {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'ASSURANCE-WAN-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $fromdate = $this->request->getGet('fromdate');
        $untildate = $this->request->getGet('untildate');
        $choice = $this->request->getGet('choice');
        $values = $this->request->getGet('values');

        $db = \Config\Database::connect();
        $array = ['assurance_wan.deleted_at' => null, 'divisi' => 'WAN'];
        $builder = $db->table('assurance_wan');
        $builder->select('*')
            ->where($array)
            ->orderBy('reported_date', 'ASC');

        if ($fromdate != '' && $untildate != '') {
            $builder->where('reported_date BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
        }

        if ($values != '' && $choice != '') {
            foreach ($choice as $key => $c) {
                if ($key == 0) {
                    $builder->like($c, $values[$key])->where($array)->where('reported_date BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                } else {
                    $builder->orlike($c, $values[$key])->where($array)->where('reported_date BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                }
            }
        }

        $query = $builder->get();
        $dataAsrwan = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Incident');
        $sheet->setCellValue('C1', 'TTR Customer');
        $sheet->setCellValue('D1', 'Summary');
        $sheet->setCellValue('E1', 'Reported Date');
        $sheet->setCellValue('F1', 'Owner Group');
        $sheet->setCellValue('G1', 'Owner');
        $sheet->setCellValue('H1', 'External Ticketid');
        $sheet->setCellValue('I1', 'customer Segment');
        $sheet->setCellValue('J1', 'Customer Name');
        $sheet->setCellValue('K1', 'Service No');
        $sheet->setCellValue('L1', 'service type');
        $sheet->setCellValue('M1', 'Lapul');
        $sheet->setCellValue('N1', 'Gaul');
        $sheet->setCellValue('O1', 'TTR Nasional');
        $sheet->setCellValue('P1', 'TTR Regional');
        $sheet->setCellValue('Q1', 'TTR Witel');
        $sheet->setCellValue('R1', 'TTR Mitra');
        $sheet->setCellValue('S1', 'TTR Agent');
        $sheet->setCellValue('T1', 'TTR Pending');
        $sheet->setCellValue('U1', 'TTR End to End');
        $sheet->setCellValue('V1', 'Pending Reason');
        $sheet->setCellValue('W1', 'Status');
        $sheet->setCellValue('X1', 'Status Date');
        $sheet->setCellValue('Y1', 'Resolved By');
        $sheet->setCellValue('Z1', 'Workzone');
        $sheet->setCellValue('AA1', 'Witel');
        $sheet->setCellValue('AB1', 'Regional');
        $sheet->setCellValue('AC1', 'Actual Solution');
        $sheet->setCellValue('AD1', 'Incident Domain');
        $sheet->setCellValue('AE1', 'Resolved Date');
        $sheet->setCellValue('AF1', 'Jumlah Site Tsel');
        $sheet->setCellValue('AG1', 'kategori Site Tsel');
        $sheet->setCellValue('AH1', 'Impacted Site Tsel');
        $sheet->setCellValue('AI1', 'RCA');
        $sheet->setCellValue('AJ1', 'Kategori');
        $sheet->setCellValue('AK1', 'Petugas');
        $sheet->setCellValue('AL1', 'Severity');


        //start coloum
        $coloumn = 2;
        foreach ($dataAsrwan as $d) {
            $dataJobasr    = $this->dataJobasr->getJob($d->idasr);
            $petugas = "";
            if ($dataJobasr != null) {
                $number = 0;
                foreach ($dataJobasr as $j) {
                    $number += 1;
                    if ($number > 1){
                        $petugas .= " - ";
                    }
                    $petugas .= $j->nama;
                }
                // return print_r($petugas);
            }

            if ($d->customer_name == "TELEKOMUNIKASI SELULAR" || stripos($d->summary, 'TSEL') !== false) {
                $kategori = "TELKOMSEL";
            } else {
                $kategori = "OLO";
            }
            $sev_nodeb = "";

            if ($d->customer_segment == "DWS") {
                foreach (["LOW", "MINOR", "MAJOR", "CRITICAL", "PREMIUM"] as $level) {
                    if (strpos($d->summary, $level) !== false) {
                        $sev_nodeb = $level;
                        break;
                    }
                }
            }
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->incident);
            $sheet->setCellValue('C' . $coloumn, $d->ttr_customer);
            $sheet->setCellValue('D' . $coloumn, $d->summary);
            $sheet->setCellValue('E' . $coloumn, $d->reported_date);
            $sheet->setCellValue('F' . $coloumn, $d->owner_group);
            $sheet->setCellValue('G' . $coloumn, $d->owner);
            $sheet->setCellValue('H' . $coloumn, $d->external_ticketid);
            $sheet->setCellValue('I' . $coloumn, $d->customer_segment);
            $sheet->setCellValue('J' . $coloumn, $d->customer_name);
            $sheet->setCellValue('K' . $coloumn, $d->service_no);
            $sheet->setCellValue('L' . $coloumn, $d->service_type);
            $sheet->setCellValue('M' . $coloumn, $d->lapul);
            $sheet->setCellValue('N' . $coloumn, $d->gaul);
            $sheet->setCellValue('O' . $coloumn, $d->ttr_nasional);
            $sheet->setCellValue('P' . $coloumn, $d->ttr_regional);
            $sheet->setCellValue('Q' . $coloumn, $d->ttr_witel);
            $sheet->setCellValue('R' . $coloumn, $d->ttr_mitra);
            $sheet->setCellValue('S' . $coloumn, $d->ttr_agent);
            $sheet->setCellValue('T' . $coloumn, $d->ttr_pending);
            $sheet->setCellValue('U' . $coloumn, $d->ttr_end_to_end);
            $sheet->setCellValue('V' . $coloumn, $d->pending_reason);
            $sheet->setCellValue('W' . $coloumn, $d->status);
            $sheet->setCellValue('X' . $coloumn, $d->status_date);
            $sheet->setCellValue('Y' . $coloumn, $d->resolved_by);
            $sheet->setCellValue('Z' . $coloumn, $d->workzone);
            $sheet->setCellValue('AA' . $coloumn, $d->witel);
            $sheet->setCellValue('AB' . $coloumn, $d->regional);
            $sheet->setCellValue('AC' . $coloumn, $d->actual_solution);
            $sheet->setCellValue('AD' . $coloumn, $d->incident_domain);
            $sheet->setCellValue('AE' . $coloumn, $d->resolved_date);
            $sheet->setCellValue('AF' . $coloumn, $d->jumlah_site_tsel_nossa);
            $sheet->setCellValue('AG' . $coloumn, $d->kategori_site_tsel);
            $sheet->setCellValue('AH' . $coloumn, $d->impacted_site_tsel);
            $sheet->setCellValue('AI' . $coloumn, $d->rca);
            $sheet->setCellValue('AJ' . $coloumn, $kategori);
            $sheet->setCellValue('AK' . $coloumn, $petugas);
            $sheet->setCellValue('AL' . $coloumn, $sev_nodeb);
            $coloumn++;
        }

        $sheet->getStyle('A1:AL1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:AL1')->getFill()
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
        $sheet->getStyle('A1:AL1' . ($coloumn - 1))->applyFromArray($styleArray);

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
        $sheet->getColumnDimension('AE')->setAutoSize(true);
        $sheet->getColumnDimension('AF')->setAutoSize(true);
        $sheet->getColumnDimension('AG')->setAutoSize(true);
        $sheet->getColumnDimension('AH')->setAutoSize(true);
        $sheet->getColumnDimension('AI')->setAutoSize(true);
        $sheet->getColumnDimension('AJ')->setAutoSize(true);
        $sheet->getColumnDimension('AK')->setAutoSize(true);
        $sheet->getColumnDimension('AL')->setAutoSize(true);


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
        // $dataAsrwan = $this->dataAsrwan->getAll();

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('dataAsrwan');
        $builder->select('*')->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
        $builder->join('datametro',  'datametro.hostname_metro = dataAsrwan.hostname_metro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_olt', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_olt', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
            $builder->orLike('platform', $keyword)->where('dataAsrwan.deleted_at IS NOT NULL', null, false);
        }
        $query = $builder->get();
        $dataAsrwan = $query->getResult();

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
        foreach ($dataAsrwan as $d) {
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
        $from_nossa = $this->request->getVar('save_as');

        if ($extension == 'xlsx' || $extension == 'xls') {

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(TRUE);

            // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            $spreadsheet = $reader->load($file);
            $dataAsrwan = $spreadsheet->getActiveSheet()->toArray();

            if ($from_nossa == "on") {
                foreach ($dataAsrwan as $d => $value) {
                    if ($d == 0) {
                        continue;
                    }

                    $data = [
                        'incident'                  => $value[0],
                        'customer_name'             => $value[1],
                        'summary'                   => $value[5],
                        'owner_group'               => $value[6],
                        'owner'                     => $value[7],
                        'external_ticketid'         => $value[18],
                        'customer_segment'          => $value[22],
                        // 'service_no'                => $value[26],
                        'service_type'              => $value[28],
                        'reported_date'             => $value[38] != null ? date('Y-m-d H:i:s', strtotime($value[38])) : $value[38],
                        'lapul'                     => $value[39],
                        'gaul'                      => $value[40],
                        'ttr_customer'              => $value[41],
                        'ttr_nasional'              => $value[42],
                        'ttr_regional'              => $value[43],
                        'ttr_witel'                 => $value[44],
                        'ttr_mitra'                 => $value[45],
                        'ttr_agent'                 => $value[46],
                        'ttr_pending'               => $value[47],
                        'pending_reason'            => $value[48],
                        'status'                    => $value[49],
                        'status_date'               => $value[53] != null ? date('Y-m-d H:i:s', strtotime($value[53])) : $value[53],
                        'resolved_by'               => $value[54],
                        'workzone'                  => $value[55],
                        'witel'                     => $value[56],
                        'regional'                  => $value[57],
                        'actual_solution'           => $value[60],
                        'incident_domain'           => $value[61],
                        'resolved_date'             => $value[68] != null ? date('Y-m-d H:i:s', strtotime($value[68])) : null,
                        'jumlah_site_tsel_nossa'    => $value[69],
                        'kategori_site_tsel'        => $value[70] == "" ? null : $value[70],
                        'impacted_site_tsel'        => $value[71],
                        'divisi'                    => 'WAN',
                    ];
                    
                    $exist = $this->dataAsrwan->where('incident', $value[0])->first();
                    if ($exist) {
                        $id = $exist->idasr;
                    }
                    
                    if ($value[26] != null) {
                        $array_merge = array_merge($data, array('service_no' => $value[26]));
                        if ($exist) {
                            $this->dataAsrwan->update($id, $array_merge);
                        } else {
                            $this->dataAsrwan->insert($array_merge);
                        }
                    } else {
                        if ($exist) {
                            $this->dataAsrwan->update($id, $data);
                        } else {
                            $this->dataAsrwan->insert($data);
                        }
                    }
                }
            } else {

                foreach ($dataAsrwan as $d => $value) {
                    if ($d == 0) {
                        continue;
                    }
                    
                    // cek kategori tiket 
                    $kategori_site = "DOWN";
                    $text = $value[2];
                    if (str_contains($text, 'QUALITY')) {
                        $kategori_site = "QUALITY";
                    } 
                    elseif (str_contains($text, 'PARTIAL')) {
                        $kategori_site = "PARTIAL";
                    }
                    elseif (str_contains($text, 'CANCEL')) {
                        $kategori_site = "CANCEL";
                    }
                    elseif (str_contains($text, 'DOUBLE')) {
                        $kategori_site = "DOUBLE";
                    }
                    elseif (str_contains($text, 'PREVENTIVE')) {
                        $kategori_site = "PREVENTIVE";
                    }
                    
                    // exclude selain NODEB metro-e dan OLO 
                    if (str_contains($text, 'RADIOIP') || str_contains($text, 'Multiple Site Down') || str_contains($text, 'Multi-Site Transport') || str_contains($text, 'HIGHCAP_DOWN')) {
                        continue;
                    } 
                    // exclude yg buka site bengkulu 
                    $service_no = $value[29];
                    if (str_contains($service_no, 'BKG') || str_contains($service_no, 'PPN')) {
                        continue;
                    } 

                    if ($value[8] == "SUMSEL" && !in_array($value[9], ["LLG","SPP","TMO","TSS","PDP","PGA","LHT"])) {
                        continue;
                    }

                    $pattern_service_no = "/TSEL_METRO_(.+?)(?=_)/i";

                    if (preg_match($pattern_service_no, $text, $matches)) {
                        if (!str_contains($matches[1], 'NODEB') || !str_contains($matches[1], 'BTS_')) {
                            $service_no = $matches[1];
                        }
                    }


                    //get total site down
                    $jumlah_site = null;
                    // $text = $value[2];
                    if ($value[28] == "TELEKOMUNIKASI SELULAR") {
                        if (str_contains($text, 'NODEB')) {
                                $pattern = "/_(\d+)NODEB/i";
                                $hasil = preg_match($pattern, $text, $matches);
                                if ($hasil){
                                    $jumlah_site = $matches[1];
                                }
                                else {
                                    $jumlah_site = 1;
                                }
                        } 
                        else {
                            $jumlah_site = 1;
                        }
                    }

                    $rca = $value[69];

                    if ($value[69] !== $value[70]){
                        $rca = $value[69] ." ". $value[70];
                    }
                    
                    $data = [
                        'incident'                  => $value[0],
                        'customer_name'             => $value[28],
                        'summary'                   => $value[2],
                        'owner_group'               => $value[4],
                        'owner'                     => $value[5],
                        'external_ticketid'         => $value[22],
                        'customer_segment'          => $value[6],
                        // 'service_no'                => $value[29],
                        'service_type'              => $value[7],
                        'reported_date'             => $value[3] != null ? date('Y-m-d H:i:s', strtotime($value[3])) : $value[3],
                        'lapul'                     => $value[33],
                        'gaul'                      => $value[34],
                        'ttr_end_to_end'            => $value[61],
                        'ttr_customer'              => $value[1],
                        'ttr_nasional'              => $value[57],
                        'ttr_regional'              => $value[59],
                        'ttr_witel'                 => $value[60],
                        'ttr_mitra'                 => $value[56],
                        'ttr_agent'                 => $value[55],
                        'ttr_pending'               => $value[58],
                        'pending_reason'            => $value[36],
                        'status'                    => $value[10],
                        'status_date'               => $value[11] != null ? date('Y-m-d H:i:s', strtotime($value[11])) : $value[11],
                        // 'resolved_by'               => $value[54],
                        'workzone'                  => $value[9],
                        'witel'                     => $value[8],
                        'regional'                  => $value[39],
                        'actual_solution'           => $value[43],
                        'incident_domain'           => $value[38],
                        'resolved_date'             => $value[64] != null ? date('Y-m-d H:i:s', strtotime($value[64])) : null,
                        'jumlah_site_tsel_nossa'    => $jumlah_site,
                        // 'kategori_site_tsel'        => $value[70] == "" ? null : $value[70],
                        'kategori_site_tsel'        => $kategori_site,
                        'impacted_site_tsel' => $value[68] !== null ? str_replace("_x000D_", " ", $value[68]) : null,
                        'cause'                     => $value[69],
                        'resolution'                => $value[70],
                        'rca'                       => $rca,
                        'divisi'                    => 'WAN',
                    ];

                    $exist = $this->dataAsrwan->where('incident', $value[0])->first();
                    if ($exist) {
                        $id = $exist->idasr;
                    }

                    if ($value[29] != null) {
                        $array_merge = array_merge($data, array('service_no' => $service_no));
                        if ($exist) {
                            $this->dataAsrwan->update($id, $array_merge);
                        } else {
                            $this->dataAsrwan->insert($array_merge);
                        }
                    } else {
                        if ($exist) {
                            $this->dataAsrwan->update($id, $data);
                        } else {
                            $this->dataAsrwan->insert($data);
                        }
                    }


                    // add petugas====================================================
                    $db = \Config\Database::connect();
                    $array = ['idsto' => $value[9]];
                    $builder = $db->table('naker')
                    ->select('idnaker, nik, nama, divisi, jobdesk, no_hp, idsto, labor, deleted_at')
                    ->like($array)
                    ->where('deleted_at IS NULL', null, false); // Kondisi deleted_at IS NULL
                    $query = $builder->get();
                    $petugas = $query->getResult();

                    $idasr = $this->dataAsrwan->getInsertID();
                    if ($exist){
                        $idasr = $id;
                    }
                    if ($petugas) {
                        foreach ($petugas as $s) {

                            // hanya WAN + jobdesk tertentu
                            if (
                                $s->divisi == 'WAN' &&
                                ($s->jobdesk == 'TEKNISI TSEL' || $s->jobdesk == 'TEKNISI OLO')
                            ) {

                                // === VALIDASI DUPLIKAT ===
                                $existPetugas = $this->dataJobasr
                                    ->where('idasr', $idasr)
                                    ->where('idnaker', $s->idnaker)
                                    ->first();

                                // kalau sudah ada → SKIP
                                if ($existPetugas) {
                                    continue;
                                }

                                // kalau belum ada → INSERT
                                $data = [
                                    'idasr'   => $idasr,
                                    'idnaker' => $s->idnaker,
                                ];

                                $this->dataJobasr->insert($data);
                            }
                        }
                    }
                }
            }

            // change null
            $this->db      = \Config\Database::connect();
            $this->db->table('assurance_wan')->set('kategori_site_tsel', null, true)->where(['kategori_site_tsel' => '', 'divisi' => 'WAN'])->update();

            return redirect()->back()->with('success', 'Data Excel Berhasil Diimport');
        } else {
            return redirect()->back()->with('error', 'Format File Tidak Sesuai');
        }
    }

    public function trash()
    {

        $data = [
            'title'        => 'Assurance',
            'dataAsrwan'      => $this->dataAsrwan->findAll(),
        ];

        return view('wan/assurance/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('assurance_wan')->set('deleted_at', null, true)->where(['idasr' => $id])->update();
        } else {
            $this->db->table('assurance_wan')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/wan/assurance')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wan/assurance');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataAsrwan->delete($id, true);
            return redirect()->to('/wan/assurance/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataAsrwan->purgeDeleted();
            return redirect()->to('/wan/assurance/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }

    function detailData()
    {
        if ($this->request->isAJAX()) {

            //get spesific data
            $id = $this->request->getGet('idasr');
            $builder = $this->dataAsrwan->GetById(array($id));
            $dataJobasr    = $this->dataJobasr->getJob($id);
            $dataWorklogsAsr    = $this->dataWorklogsAsr->getWorklogs($id);
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
                            <div class="row">
                                <div class="col-sm-12 col-lg-2">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%"><?= $builder[0]['incident'] ?></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12 col-lg-10">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <tr>
                                                <th><?= $builder[0]['summary'] ?></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-sm-12 col-lg-6">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <tr>
                                                <th class="media-change">Owner</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['owner'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Owner Group</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['owner_group'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Reported Date</th>
                                                <th>:</th>
                                                <td><?= date('Y-m-d H:i:s', strtotime($builder[0]['reported_date'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <th>:</th>
                                                <td>
                                                    <?php if ($builder[0]['status'] == 'BACKEND') : ?>
                                                        <h6>
                                                            <div class="badge badge-danger"><?= $builder[0]['status'] ?></div>
                                                        </h6>
                                                    <?php elseif ($builder[0]['status'] == 'CLOSED') : ?>
                                                        <h6>
                                                            <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                        </h6>
                                                    <?php elseif ($builder[0]['status'] == 'PENDING') : ?>
                                                        <h6>
                                                            <div class="badge badge-warning"><?= $builder[0]['status'] ?></div>
                                                        </h6>
                                                    <?php else : ?>
                                                        <h6>
                                                            <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                        </h6>
                                                    <?php endif ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12 col-lg-3">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <tr>
                                                <th class="media-change">TTR Customer</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['ttr_customer'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>TTR Nasional</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['ttr_nasional'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>TTR Regional</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['ttr_regional'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>TTR Pending</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['ttr_pending'] ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12 col-lg-3 pl-0 text-center">
                                    <table class="table table-sm table-borderless mb-1">
                                        <tbody>
                                            <tr>
                                                <th class="media-change">TTR Witel</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['ttr_witel'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>TTR Mitra</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['ttr_mitra'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>TTR Agent</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['ttr_agent'] ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php if ($builder[0]['pending_reason'] != '') : ?>
                                        <h6>
                                            <div class="bg-info rounded-pill text-white text-center" style="font-size: 0.7rem;"><?= $builder[0]['pending_reason'] ?></div>
                                            <div class="badge badge-info"><?= $builder[0]['pending_reason'] ?></div>
                                        </h6>
                                    <?php endif ?>
                                </div>
                            </div> -->
                            <hr class="mt-0">
                            <?php if ($builder[0]['rca'] != '') : ?>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-sm table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th>RCA</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th>
                                                        <textarea disabled="" class="" style="border: none; height: 50px !important; width: 100% !important"><?= $builder[0]['rca'] ?></textarea>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif ?>
                            <?php if ($builder[0]['incident'] == 'NO TICKET') : ?>
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%">Reported Date</th>
                                            <th>:</th>
                                            <td><?= date('Y-m-d H:i:s', strtotime($builder[0]['reported_date'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <th>:</th>
                                            <td>
                                                <?php if ($builder[0]['status'] == 'BACKEND') : ?>
                                                    <h6>
                                                        <div class="badge badge-danger"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php elseif ($builder[0]['status'] == 'CLOSED') : ?>
                                                    <h6>
                                                        <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php elseif ($builder[0]['status'] == 'PENDING') : ?>
                                                    <h6>
                                                        <div class="badge badge-warning"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php else : ?>
                                                    <h6>
                                                        <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php endif ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <hr class="m-0">
                                            </th>
                                        </tr>
                                        <?php if ($builder[0]['rca'] == '') : ?>
                                            <tr>
                                                <th>RCA</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['rca'] ?></td>
                                            </tr>
                                        <?php endif ?>
                                        <tr>
                                            <th>Service No</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['service_no'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Customer Segment</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['customer_segment'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Resolved Date</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['resolved_date'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Workzone</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['workzone'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Kategori Site Tsel</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['kategori_site_tsel'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                            <?php endif ?>

                            <?php if ($builder[0]['incident'] != 'NO TICKET') : ?>
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%">Owner</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['owner'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Owner Group</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['owner_group'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Reported Date</th>
                                            <th>:</th>
                                            <td><?= date('Y-m-d H:i:s', strtotime($builder[0]['reported_date'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <th>:</th>
                                            <td>
                                                <?php if ($builder[0]['status'] == 'BACKEND') : ?>
                                                    <h6>
                                                        <div class="badge badge-danger"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php elseif ($builder[0]['status'] == 'CLOSED') : ?>
                                                    <h6>
                                                        <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php elseif ($builder[0]['status'] == 'PENDING') : ?>
                                                    <h6>
                                                        <div class="badge badge-warning"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php else : ?>
                                                    <h6>
                                                        <div class="badge badge-success"><?= $builder[0]['status'] ?></div>
                                                    </h6>
                                                <?php endif ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <hr class="m-0">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>TTR Customer</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['ttr_customer'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>TTR Nasional</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['ttr_nasional'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>TTR Regional</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['ttr_regional'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>TTR Pending</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['ttr_pending'] ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Pending Reason</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['pending_reason'] ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>TTR Witel</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['ttr_witel'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>TTR Mitra</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['ttr_mitra'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>TTR Agent</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['ttr_agent'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <hr class="m-0">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>External TicketID</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['external_ticketid'] ?></td>
                                        </tr>
                                        <?php if ($builder[0]['rca'] == '') : ?>
                                            <tr>
                                                <th>RCA</th>
                                                <th>:</th>
                                                <td><?= $builder[0]['rca'] ?></td>
                                            </tr>
                                        <?php endif ?>
                                        <tr>
                                            <th>Service No</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['service_no'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Service Type</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['service_type'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Customer Segment</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['customer_segment'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Lapul</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['lapul'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Gaul</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['gaul'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Actual Solution</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['actual_solution'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Incident Domain</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['incident_domain'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status Date</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['status_date'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Resolved By</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['resolved_by'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Resolved Date</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['resolved_date'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Workzone</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['workzone'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Witel</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['witel'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Regional</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['regional'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Site Tsel Nossa</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['jumlah_site_tsel_nossa'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Kategori Site Tsel</th>
                                            <th>:</th>
                                            <td><?= $builder[0]['kategori_site_tsel'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Impacted Site Tsel</th>
                                            <th>:</th>
                                            <td>
                                                <?php if ($builder[0]['impacted_site_tsel'] != '') : ?>
                                                    <textarea disabled="" class="" style="border: none; height: 200px !important; width: 100% !important"><?= $builder[0]['impacted_site_tsel'] ?></textarea>
                                                <?php endif ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="card card-plain">
                    <div class="card-header" role="tab" id="headingTwo">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            #PROGRESS_BY

                            <i class="fas fa-chevron-down text-right"></i>
                        </a>
                    </div>
                    <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                        <div class="card-body">
                            <?php if ($dataJobasr != null) : ?>
                                <table class="table table-sm table-borderedless">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Divisi</th>
                                    </tr>
                                    <tbody>
                                        <?php foreach ($dataJobasr as $d) : ?>
                                            <tr>
                                                <td><?= $d->nama ?></td>
                                                <td><?= $d->divisi ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="card card-plain">
                    <div class="card-header" role="tab" id="headingThree">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            #WORK_LOGS

                            <i class="fas fa-chevron-down text-right"></i>
                        </a>
                    </div>
                    <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                        <div class="card-body">
                            <?php if ($dataWorklogsAsr != null) : ?>
                                <table class="table table-sm table-borderedless">
                                    <tr>
                                        <th>Record</th>
                                        <th>Created By</th>
                                        <th>Owner Group</th>
                                        <th>Date</th>
                                        <th>Summary</th>
                                    </tr>
                                    <tbody>
                                        <?php foreach ($dataWorklogsAsr as $d) : ?>
                                            <tr>
                                                <td><?= $d->record ?></td>
                                                <td><?= $d->created_by ?></td>
                                                <td><?= $d->og ?></td>
                                                <td><?= $d->date ?></td>
                                                <td><?= $d->summ ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif ?>
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
                                    <!-- <tr>
                                        <th>Material</th>
                                        <th>:</th>
                                        <td></td>
                                    </tr> -->
                                    <tr>
                                        <th style="width: 30%">Evidence</th>
                                        <th>:</th>
                                        <td>
                                            <img class="rounded img-fluid" src="<?= ($builder[0]['evidence'] != '') ? base_url('/img/assurance/' . $builder[0]['evidence']) : ''; ?>" alt="<?= $builder[0]['evidence'] ?>">
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
