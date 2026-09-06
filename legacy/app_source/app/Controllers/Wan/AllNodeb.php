<?php

namespace App\Controllers\Wan;

use CodeIgniter\RESTful\ResourceController;
use App\Models\NodebAllModel;
use App\Models\StoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Hermawan\DataTables\DataTable;


class AllNodeb extends ResourceController
{
    public function __construct()
    {
        $this->datanodeb_all = new NodebAllModel();
        $this->dataSto = new StoModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */


    public function read()
    {

        $db = \Config\Database::connect();
        $array = ['datanodeb_all.deleted_at' => null]; //AGR023
        $builder = $db->table('datanodeb_all')
            // ->select('site_id_all, datanodeb_all.idsto as idsto_nodeb, site_id, site_name, datanodeb_all.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, dataolt.type_olt as type_olt, datanodeb_all.port_metro as port_metro_nodeb, datanodeb_all.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, datanodeb_all.serial_number as serial_number, odc, odp, tikor_site, on_air')
            ->select('site_id_all, datanodeb_all.idsto as idsto_nodeb, site_id, site_name, datanodeb_all.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, dataolt.type_olt as type_olt, datanodeb_all.port_metro as port_metro_nodeb, datanodeb_all.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, dataont.merk as merk, CONCAT(dataont.merk ,"-", dataont.type) as ont_type_gabungan, datanodeb_all.serial_number as serial_number, odc, odp, tikor_site, on_air')
            ->where($array)
            ->groupBy('site_id_all')
            ->join('dataont',  'dataont.serial_number = datanodeb_all.serial_number')
            ->join('datasto',  'datasto.idsto = datanodeb_all.idsto')
            ->join('datametro',  'datametro.hostname_metro = datanodeb_all.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = datanodeb_all.hostname_olt');

        $builder->groupBy('site_id_all');
        // $builder->having('COUNT(site_id_all > 1)');


        $query = $builder->get();
        $datanodeb_all = $query->getResult();
        // print_r($datanodeb_all);

        $data = array();

        foreach ($datanodeb_all as $d) {

            $ont_type = $d->ont_type;
            if (!str_contains($d->ont_type, 'direct')) {
                $ont_type = $d->ont_type_gabungan;
            }

            // $ont_merk = "";
            // if (!str_contains($d->ont_type, 'direct')) {
            //     $ont_merk = $d->merk;
            // }

            $port_metro = $d->port_metro_nodeb;
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
                // "id" => $d->site_id_all,
                "sto" => $d->idsto_nodeb,
                "site_id" => $d->site_id,
                "site_name" => $d->site_name,
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
            );
        }
        return $this->respond($data, 200);
    }
    public function index()
    {
        
        // $getPaginated = $this->datanodeb_all->getPaginated(10, $keyword);
        $data = [
            'title'        => 'NODE-B',
        ];
        return view('wan/allnodeb/index', $data);
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
            'title' => 'NODE-B',
            'validation' => \config\Services::validation(),
            'dataSto' => $this->dataSto->getAll(),
        ];
        return view('wan/allnodeb/new', $data);
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
                'sto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'site_id_all' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Site id tidak boleh kosong',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/wan/allnodeb/new')->withInput();
        }

        $data = $this->request->getPost();

        $this->datanodeb_all->insert($data);


        //change date not null
        // $this->db      = \Config\Database::connect();
        // $this->db->table('datanodeb_all')->set('on_air', '0000-00-00', true)->where(['on_air' => null])->update();

        return redirect()->to('/wan/allnodeb')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $datanodeb_all = $this->datanodeb_all->find($id);
        if (is_object($datanodeb_all)) {
            $data = [
                'title'      => 'NODE-B',
                'validation' => \config\Services::validation(),
                'datanodeb_all'  => $datanodeb_all,
                'dataSto'    => $this->dataSto->getAll(),
            ];
            return view('wan/allnodeb/edit', $data);
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
                'sto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'site_id_all' => [
                    'rules' => "required",
                    'errors' => [
                        'required' => 'Site id tidak boleh kosong',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/wan/allnodeb/' . $id . '/edit')->withInput();
        }

        $data = $this->request->getPost();
        $this->datanodeb_all->update($id, $data);


        //change date not null
        // $this->db      = \Config\Database::connect();
        // $this->db->table('datanodeb_all')->set('on_air', '0000-00-00', true)->where(['on_air' => null])->update();

        return redirect()->to('/wan/allnodeb')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->datanodeb_all->delete($id);

        return redirect()->to('/wan/allnodeb')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {
        if ($this->request->isAJAX()) {

            //get spesific data
            $keyword = $this->request->getGet('keyword');
            $choice = $this->request->getGet('choice');
            $values = $this->request->getGet('values');

            $db = db_connect();
            $array = ['datanodeb_all.deleted_at' => null];
            $builder = $db->table('datanodeb_all')
                ->select('site_id_all, site_name_all, nsa, kabupaten, kecamatan, kelurahan, lat_long, tech, band_2g, band_3g, band_4g, band_updated, tp, s_power, transport, sto')
                ->where($array);


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
                ->add('action', function ($datanodeb_all) {
                    // return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    // <a href=\"/wan/allnodeb/detail/$datanodeb_all->site_id_all\" type=\"button\" class=\"btn btn-sm btn-outline-dark\">
                    // <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    // </a>
                    // <a href=\"/wan/allnodeb/$datanodeb_all->site_id_all/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    // <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    // </a>
                    // <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_name_all=\"$datanodeb_all->site_name_all\">
                    // <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    // </button>
                    // </div>";
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/allnodeb/$datanodeb_all->site_id_all/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_name_all=\"$datanodeb_all->site_name_all\">
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
            $array = ['datanodeb_all.deleted_at !=' => null];
            $builder = $db->table('datanodeb_all')
                ->select('site_id_all, site_name_all, nsa, kabupaten, kecamatan, kelurahan, lat_long, tech, band_2g, band_3g, band_4g, band_updated, tp, s_power, transport, sto')
                ->where($array);

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($datanodeb_all) {
                    // return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    // <button type=\"button\" class=\"btn btn-sm btn-outline-dark\" onclick=\"showDetail($datanodeb_all->site_id_all)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">
                    // <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    // </button>
                    // <a href=\"/wan/allnodeb/restore/$datanodeb_all->site_id_all\" type=\"button\" class=\"btn btn-sm btn-info\">
                    // <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    // </a>
                    // <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_name_all=\"$datanodeb_all->site_name_all\">
                    // <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    // </button>
                    // </div>";
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/allnodeb/restore/$datanodeb_all->site_id_all\" type=\"button\" class=\"btn btn-sm btn-info\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_id_all=\"$datanodeb_all->site_id_all\" data-site_name_all=\"$datanodeb_all->site_name_all\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                    </div>";
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'NODEB-ALL-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $choice = $this->request->getGet('choice');
        $values = $this->request->getGet('values');

        $db = \Config\Database::connect();
        $array = ['datanodeb_all.deleted_at' => null];
        $builder = $db->table('datanodeb_all')
            ->select('site_id_all, site_name_all, nsa, kabupaten, kecamatan, kelurahan, lat_long, tech, band_2g, band_3g, band_4g, band_updated, tp, s_power, transport, sto')
            ->where($array);

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
        $datanodeb_all = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'SITE_ID');
        $sheet->setCellValue('C1', 'SITE_NAME');
        $sheet->setCellValue('D1', 'NSA');
        $sheet->setCellValue('E1', 'KABUPATEN');
        $sheet->setCellValue('F1', 'KECAMATAN');
        $sheet->setCellValue('G1', 'KELURAHAN');
        $sheet->setCellValue('H1', 'COORDINATE');
        $sheet->setCellValue('I1', 'TECH');
        $sheet->setCellValue('J1', 'BAND_2G');
        $sheet->setCellValue('K1', 'BAND_3G');
        $sheet->setCellValue('L1', 'BAND_4G');
        $sheet->setCellValue('M1', 'BAND UPDATED');
        $sheet->setCellValue('N1', 'TP');
        $sheet->setCellValue('O1', 'SOURCE POWER');
        $sheet->setCellValue('P1', 'TRANSPORT');
        $sheet->setCellValue('Q1', 'STO');

        //start coloum
        $coloumn = 2;
        foreach ($datanodeb_all as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->site_id_all);
            $sheet->setCellValue('C' . $coloumn, $d->site_name_all);
            $sheet->setCellValue('D' . $coloumn, $d->nsa);
            $sheet->setCellValue('E' . $coloumn, $d->kabupaten);
            $sheet->setCellValue('F' . $coloumn, $d->kecamatan);
            $sheet->setCellValue('G' . $coloumn, $d->kelurahan);
            $sheet->setCellValue('H' . $coloumn, $d->lat_long);
            $sheet->setCellValue('I' . $coloumn, $d->tech);
            $sheet->setCellValue('J' . $coloumn, $d->band_2g);
            $sheet->setCellValue('K' . $coloumn, $d->band_3g);
            $sheet->setCellValue('L' . $coloumn, $d->band_4g);
            $sheet->setCellValue('M' . $coloumn, $d->band_updated);
            $sheet->setCellValue('N' . $coloumn, $d->tp);
            $sheet->setCellValue('O' . $coloumn, $d->s_power);
            $sheet->setCellValue('P' . $coloumn, $d->transport);
            $sheet->setCellValue('Q' . $coloumn, $d->sto);
            $coloumn++;
        }

        $sheet->getStyle('A1:Q1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:Q1')->getFill()
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
        $sheet->getStyle('A1:R' . ($coloumn - 1))->applyFromArray($styleArray);

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
            $datanodeb_all = $spreadsheet->getActiveSheet()->toArray();
            foreach ($datanodeb_all as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'site_id_all'           => $value[0],
                    'site_name_all'         => $value[1],
                    'nsa'                   => $value[2],
                    'kabupaten'             => $value[3],
                    'kecamatan'             => $value[4],
                    'kelurahan'             => $value[5],
                    'lat_long'              => $value[6],
                    'tech'                  => $value[7],
                    'band_2g'               => $value[8],
                    'band_3g'               => $value[9],
                    'band_4g'               => $value[10],
                    'band_updated'          => $value[11],
                    'tp'                    => $value[12],
                    's_power'               => $value[13],
                    'transport'             => $value[14],
                    'sto'                   => $value[15],
                ];
                $exist = $this->datanodeb_all->where('site_id_all', $value[0])->first();
                if ($exist) {
                    $id = $exist->site_id_all;
                    $this->datanodeb_all->update($id, $data);
                } else {
                    $this->datanodeb_all->insert($data);
                }

                // $this->datanodeb_all->insert($data);
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
            'title'        => 'NODE-B',
        ];

        return view('wan/allnodeb/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('datanodeb_all')->set('deleted_at', null, true)->where(['site_id_all' => $id])->update();
        } else {
            $this->db->table('datanodeb_all')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/wan/allnodeb')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wan/allnodeb');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->datanodeb_all->delete($id, true);
            return redirect()->to('/wan/allnodeb/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->datanodeb_all->purgeDeleted();
            return redirect()->to('/wan/allnodeb/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }


}
