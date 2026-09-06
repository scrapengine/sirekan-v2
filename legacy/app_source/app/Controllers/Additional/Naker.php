<?php

namespace App\Controllers\Additional;

use App\Controllers\BaseController;
use App\Models\StoModel;
use App\Models\NakerModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\RESTful\ResourcePresenter;
use Config\Services;
use \Hermawan\DataTables\DataTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Naker extends ResourceController
{
    public function __construct()
    {
        $this->dataSto = new StoModel();
        $this->dataNaker = new NakerModel();
    }

    public function index()
    {

        $keyword = $this->request->getGet('keyword');
        $dataNaker = $this->dataNaker->getAll($keyword);
        $data = [
            'title' => 'TEAM',
            'dataNaker' => $dataNaker,
            'keyword' => $keyword,
        ];
        helper('url');
        return view('/additional/naker/index', $data);
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
            'title' => 'TEAM',
            'validation' => \config\Services::validation(),
            'dataNaker' => $this->dataNaker->getAll(),
            'dataSto' => $this->dataSto->getAll(),
        ];
        return view('additional/naker/new', $data);
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
                'nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong',
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
            return redirect()->to('/additional/naker/new')->withInput();
        }
        $idsto = $this->request->getVar('idsto');
        $separator = implode(', ', $idsto);

        $data = [
            'nik'             => $this->request->getVar('nik'),
            'nama'            => $this->request->getVar('nama'),
            'divisi'          => $this->request->getVar('divisi'),
            'jobdesk'         => $this->request->getVar('jobdesk'),
            'no_hp'           => $this->request->getVar('no_hp'),
            'idsto'           => $separator,
            'labor'           => $this->request->getVar('labor'),
        ];
        $this->dataNaker->insert($data);

        return redirect()->to('/additional/naker')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $dataNaker = $this->dataNaker->find($id);
        if (is_object($dataNaker)) {
            $data = [
                'title' => 'TEAM',
                'validation' => \config\Services::validation(),
                'dataNaker' => $dataNaker,
                'dataSto' => $this->dataSto->getAll(),
            ];
            return view('additional/naker/edit', $data);
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
                'nama' => [
                    'rules' => "required",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong',
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
            return redirect()->to('/additional/naker/' . $id . '/edit')->withInput();
        }

        $idsto = $this->request->getVar('idsto');
        $separator = implode(', ', $idsto);

        $data = [
            'nik'             => $this->request->getVar('nik'),
            'nama'            => $this->request->getVar('nama'),
            'divisi'          => $this->request->getVar('divisi'),
            'jobdesk'         => $this->request->getVar('jobdesk'),
            'no_hp'           => $this->request->getVar('no_hp'),
            'idsto'           => $separator,
            'labor'           => $this->request->getVar('labor'),
        ];

        $this->dataNaker->update($id, $data);

        return redirect()->to('/additional/naker')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */

    public function delete($id = null)
    {
        //

        $this->dataNaker->delete($id);

        return redirect()->to('/additional/naker')->with('success', 'Data berhasil dihapus.');
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
            $array = array('naker.deleted_at' => null);
            $builder = $db->table('naker')->select('idnaker, nik, nama, divisi, jobdesk, no_hp, idsto, labor')
                ->where($array);


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

            // if ($keyword != '') {
            //     $builder->like('nik', $keyword)->where($array);
            //     $builder->orLike('nama', $keyword)->where($array);
            //     $builder->orLike('divisi', $keyword)->where($array);
            //     $builder->orLike('jobdesk', $keyword)->where($array);
            //     $builder->orLike('no_hp', $keyword)->where($array);
            //     $builder->orLike('idsto', $keyword)->where($array);
            //     $builder->orLike('labor', $keyword)->where($array);
            // };


            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataNaker) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <a href=\"/additional/naker/$dataNaker->idnaker/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idnaker=\"$dataNaker->idnaker\" data-nama=\"$dataNaker->nama\">
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
            $builder = $db->table('naker')
                ->select('idnaker, nik, nama, divisi, jobdesk, no_hp, idsto, labor')
                ->where('naker.deleted_at !=', null);

            if ($keyword != '') {
                $builder->like('nik', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
                $builder->orLike('nama', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
                $builder->orLike('divisi', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
                $builder->orLike('jobdesk', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
                $builder->orLike('no_hp', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
                $builder->orLike('idsto', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
                $builder->orLike('labor', $keyword)->where('naker.deleted_at IS NOT NULL', null, false);
            };


            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataNaker) {
                    return "<div class=\"btn-group\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-info\" id=\"btnRestore\" data-idnaker=\"$dataNaker->idnaker\" data-nama=\"$dataNaker->nama\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </button>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idnaker=\"$dataNaker->idnaker\" data-nama=\"$dataNaker->nama\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'NAKER-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $fromdate = $this->request->getGet('fromdate');
        $untildate = $this->request->getGet('untildate');
        $choice = $this->request->getGet('choice');
        $values = $this->request->getGet('values');

        $db = \Config\Database::connect();
        $builder = $db->table('naker');
        $array = array('naker.deleted_at' => null);
        $builder->select('*')->where($array);

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
        $dataNaker = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NIK');
        $sheet->setCellValue('C1', 'NAMA');
        $sheet->setCellValue('D1', 'DIVISI');
        $sheet->setCellValue('E1', 'JOBDESK');
        $sheet->setCellValue('F1', 'NO_HP');
        $sheet->setCellValue('G1', 'STO');
        $sheet->setCellValue('H1', 'LABOR');

        //start coloum
        $coloumn = 2;
        foreach ($dataNaker as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->nik);
            $sheet->setCellValue('C' . $coloumn, $d->nama);
            $sheet->setCellValue('D' . $coloumn, $d->divisi);
            $sheet->setCellValue('E' . $coloumn, $d->jobdesk);
            $sheet->setCellValue('F' . $coloumn, $d->no_hp);
            $sheet->setCellValue('G' . $coloumn, $d->idsto);
            $sheet->setCellValue('H' . $coloumn, $d->labor);
            $coloumn++;
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:H1')->getFill()
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
            $dataNaker = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataNaker as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'nik'           => $value[0],
                    'nama'          => $value[1],
                    'divisi'        => $value[2],
                    'jobdesk'       => $value[3],
                    'no_hp'         => $value[4],
                    'idsto'         => $value[5],
                    'labor'         => $value[6],
                ];
                $exist = $this->dataNaker->where('nik', $value[0])->first();
                if ($exist) {
                    $id = $exist->nik;
                    $this->dataNaker->update($id, $data);
                } else {
                    $this->dataNaker->insert($data);
                }
                // $this->dataNaker->insert($data);
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
        $data = [
            'title'        => 'TEAM',
            'dataNaker'      => $this->dataNaker->findAll(),
        ];

        return view('additional/naker/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('naker')->set('deleted_at', null, true)->where(['idnaker' => $id])->update();
        } else {
            $this->db->table('naker')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/additional/naker')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/additional/naker');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataNaker->delete($id, true);
            return redirect()->to('/additional/naker/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataNaker->purgeDeleted();
            return redirect()->to('/additional/naker/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }
}
