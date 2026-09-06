<?php

namespace App\Controllers\Additional;

use App\Controllers\BaseController;
use App\Models\StoModel;
use App\Models\OltModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\RESTful\ResourcePresenter;
use Config\Services;
use \Hermawan\DataTables\DataTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SoapClient;

class Sto extends ResourceController
{
    public function __construct()
    {
        $this->dataSto = new StoModel();
        $this->dataOlt = new OltModel();
    }

    public function index()
    {

        $keyword = $this->request->getGet('keyword');
        $dataSto = $this->dataSto->getAll($keyword);
        $data = [
            'title' => 'STO',
            'dataSto' => $dataSto,
            'keyword' => $keyword,
        ];
        helper('url');
        return view('/additional/sto/index', $data);
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
            'title' => 'STO',
            'validation' => \config\Services::validation(),
            'dataSto' => $this->dataSto->getAll(),
        ];
        return view('additional/sto/new', $data);
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
                'witel' => [
                    'rules' => 'required[datasto.witel]',
                    'errors' => [
                        'required' => 'Witel tidak boleh kosong',
                    ],
                ],
                'idsto' => [
                    'rules' => 'required|is_unique[datasto.idsto]',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                        'is_unique' => 'STO sudah terdaftar',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/additional/sto/new')->withInput();
        }
        $data = $this->request->getPost();
        $this->dataSto->insert($data);

        return redirect()->to('/additional/sto')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $dataSto = $this->dataSto->find($id);
        if (is_object($dataSto)) {
            $data = [
                'title' => 'STO',
                'validation' => \config\Services::validation(),
                'dataSto' => $dataSto,
            ];
            return view('additional/sto/edit', $data);
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
                'witel' => [
                    'rules' => "required[datasto.witel,witel,{$id}]",
                    'errors' => [
                        'required' => 'Witel tidak boleh kosong',
                    ],
                ],
                'idsto' => [
                    'rules' => "required|is_unique[datasto.idsto,idsto,{$id}]",
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                        'is_unique' => 'STO sudah terdaftar',
                    ],
                ],
            ]
        )) {
            return redirect()->to('/additional/sto/' . $id . '/edit')->withInput();
        }

        $data = $this->request->getPost();

        $this->dataSto->update($id, $data);

        return redirect()->to('/additional/sto')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */

    public function delete($id = null)
    {
        //
        $dataOlt = $this->dataOlt->first();
        if ($dataOlt->idsto == $id) {
            return redirect()->to('/additional/sto')->with('error', 'Tidak dapat dihapus! Data berelasi sudah digunakan di tabel lain.');
        }

        $this->dataSto->delete($id);

        return redirect()->to('/additional/sto')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {

        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = array('datasto.deleted_at' => null, 'idsto !=' => '-');
            $builder = $db->table('datasto')->select('idsto, nama_sto, longitude, latitude, alamat')
                ->where($array);

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where($array);
                $builder->orLike('nama_sto', $keyword)->where($array);
                $builder->orLike('longitude', $keyword)->where($array);
                $builder->orLike('latitude', $keyword)->where($array);
                $builder->orLike('alamat', $keyword)->where($array);
            };


            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataSto) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <a href=\"/additional/sto/$dataSto->idsto/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idsto=\"$dataSto->idsto\" data-nama_sto=\"$dataSto->nama_sto\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function listDataTrash()
    {

        $keyword = $this->request->getGet('keyword');
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $builder = $db->table('datasto')
                ->select('idsto, nama_sto, longitude, latitude, alamat')
                ->where('datasto.deleted_at !=', null);

            if ($keyword != '') {
                $builder->like('idsto', $keyword)->where('datasto.deleted_at !=', null);
                $builder->orLike('nama_sto', $keyword)->where('datasto.deleted_at !=', null);
                $builder->orLike('longitude', $keyword)->where('datasto.deleted_at !=', null);
                $builder->orLike('latitude', $keyword)->where('datasto.deleted_at !=', null);
                $builder->orLike('alamat', $keyword)->where('datasto.deleted_at !=', null);
            };


            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataSto) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-info\" id=\"btnRestore\" data-idsto=\"$dataSto->idsto\" data-nama_sto=\"$dataSto->nama_sto\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </button>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idsto=\"$dataSto->idsto\" data-nama_sto=\"$dataSto->nama_sto\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'STO-' . date('ymd-his') . '.xlsx';
        //get all data
        // $dataSto = $this->dataSto->getAll();

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('datasto');
        $array = array('datasto.deleted_at' => null, 'idsto !=' => '-');
        $builder->select('*')->where($array);

        if ($keyword != '') {
            $builder->like('witel', $keyword)->where($array);
            $builder->like('idsto', $keyword)->where($array);
            $builder->orLike('nama_sto', $keyword)->where($array);
            $builder->orLike('longitude', $keyword)->where($array);
            $builder->orLike('latitude', $keyword)->where($array);
            $builder->orLike('alamat', $keyword)->where($array);
        }

        $query = $builder->get();
        $dataSto = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'WITEL');
        $sheet->setCellValue('C1', 'CODE');
        $sheet->setCellValue('D1', 'NAME');
        $sheet->setCellValue('E1', 'LONGITUDE');
        $sheet->setCellValue('F1', 'LATITUDE');
        $sheet->setCellValue('G1', 'ADDRESS');

        //start coloum
        $coloumn = 2;
        foreach ($dataSto as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->witel);
            $sheet->setCellValue('C' . $coloumn, $d->idsto);
            $sheet->setCellValue('D' . $coloumn, $d->nama_sto);
            $sheet->setCellValue('E' . $coloumn, $d->longitude);
            $sheet->setCellValue('F' . $coloumn, $d->latitude);
            $sheet->setCellValue('G' . $coloumn, $d->alamat);
            $coloumn++;
        }

        $sheet->getStyle('A1:G1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:G1')->getFill()
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
        $sheet->getStyle('A1:G' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);


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
            $dataSto = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataSto as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'witel'          => $value[0],
                    'idsto'          => $value[1],
                    'nama_sto'       => $value[2],
                    'longitude'      => $value[3],
                    'latitude'       => $value[4],
                    'alamat'         => $value[5],
                ];

                $this->dataSto->insert($data);
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
        $getPaginatedTrash = $this->dataSto->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'STO',
            // 'dataSto'      => $this->dataSto->getTrash(),
            'dataSto'      => $getPaginatedTrash['dataSto'],
            'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('additional/sto/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('datasto')->set('deleted_at', null, true)->where(['idsto' => $id])->update();
        } else {
            $this->db->table('datasto')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/additional/sto')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/additional/sto');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataSto->delete($id, true);
            return redirect()->to('/additional/sto/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataSto->purgeDeleted();
            return redirect()->to('/additional/sto/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }
}
