<?php

namespace App\Controllers\Additional;

use App\Controllers\BaseController;
use App\Models\MetroModel;
use App\Models\StoModel;
use App\Models\OltModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\RESTful\ResourcePresenter;
use Config\Services;
use DateTimeZone;
use \Hermawan\DataTables\DataTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Metro extends ResourceController
{
    /**
     * Present a view of resource objects
     *
     * @return mixed
     */

    public function __construct()
    {
        $this->dataMetro = new MetroModel();
        $this->dataSto = new StoModel();
        $this->dataOlt = new OltModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $dataMetro = $this->dataMetro->getAll($keyword);
        $data = [
            'title' => 'Metro',
            'dataMetro' => $dataMetro,
        ];
        helper('url');
        return view('/additional/metro/index', $data);
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
            'title' => 'METRO',
            'validation' => \config\Services::validation(),
            'dataSto' => $this->dataSto->getAll(),
        ];
        return view('additional/metro/new', $data);
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
                'hostname_metro' => [
                    'rules' => 'required|is_unique[datametro.hostname_metro]',
                    'errors' => [
                        'required' => 'Hostname Metro tidak boleh kosong',
                        'is_unique' => 'Hostname Metro sudah terdaftar',
                    ],
                ],
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/additional/metro/new')->withInput();
        }
        $data = $this->request->getPost();
        $this->dataMetro->insert($data);

        return redirect()->to('/additional/metro')->with('success', 'Data berhasil disimpan.');
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
        $dataMetro = $this->dataMetro->find($id);
        if (is_object($dataMetro)) {
            $data = [
                'title' => 'METRO',
                'validation' => \config\Services::validation(),
                'dataMetro' => $dataMetro,
                'dataSto' => $this->dataSto->getAll(),
            ];
            return view('additional/metro/edit', $data);
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
        //validasi form input
        if (!$this->validate(
            [
                'hostname_metro' => [
                    'rules' => "required|is_unique[datametro.hostname_metro,hostname_metro,{$id}]",
                    'errors' => [
                        'required' => 'Hostname Metro tidak boleh kosong',
                        'is_unique' => 'Hostname Metro sudah terdaftar',
                    ],
                ],
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/additional/metro/' . $id . '/edit')->withInput();
        }

        $data = $this->request->getPost();

        $this->dataMetro->update($id, $data);

        return redirect()->to('/additional/metro')->with('success', 'Data berhasil diupdate.');
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
        $dataOlt = $this->dataOlt->first();
        if ($dataOlt->hostname_metro == $id) {
            return redirect()->to('/additional/metro')->with('error', 'Tidak dapat dihapus! Data berelasi sudah digunakan di tabel lain.');
        }

        $this->dataMetro->delete($id);
        return redirect()->to('/additional/metro')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = array('datametro.deleted_at' => null, 'hostname_metro !=' => '-');
            $builder = $db->table('datametro')
                ->select('datasto.idsto, hostname_metro, ip_metro')
                ->where($array)
                ->join('datasto',  'datasto.idsto = datametro.idsto');

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataMetro) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <a href=\"/additional/metro/$dataMetro->hostname_metro/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-hostname_metro=\"$dataMetro->hostname_metro\">
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
            $builder = $db->table('datametro')
                ->select('datasto.idsto, hostname_metro, ip_metro')
                ->where('datametro.deleted_at !=', null)
                ->join('datasto',  'datasto.idsto = datametro.idsto');

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataMetro) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-info\" id=\"btnRestore\" data-hostname_metro=\"$dataMetro->hostname_metro\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </button>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-hostname_metro=\"$dataMetro->hostname_metro\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'METRO-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $array = array('datametro.deleted_at' => null, 'hostname_metro !=' => '-');
        $builder = $db->table('datametro');
        $builder->select('*')->where($array);

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('hostname_metro', $keyword)->where($array);
            $builder->orLike('ip_metro', $keyword)->where($array);
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

        $sheet->getStyle('A1:D1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:D1')->getFill()
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
            $dataMetro = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataMetro as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'idsto'          => $value[0],
                    'hostname_metro' => $value[1],
                    'ip_metro'       => $value[2],
                ];

                $this->dataMetro->insert($data);
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
        $dataMetro = $this->dataMetro->getTrash($keyword);
        // $getPaginatedTrash = $this->dataMetro->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'METRO',
            'dataMetro'      => $dataMetro,
            // 'dataMetro'      => $getPaginatedTrash['dataMetro'],
            // 'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('additional/metro/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('datametro')->set('deleted_at', null, true)->where(['hostname_metro' => $id])->update();
        } else {
            $this->db->table('datametro')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/additional/metro')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/additional/metro');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataMetro->delete($id, true);
            return redirect()->to('/additional/metro/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataMetro->purgeDeleted();
            return redirect()->to('/additional/metro/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }
}
