<?php

namespace App\Controllers\Wan;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OltModel;
use App\Models\MetroModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Files\File;

class Olt extends ResourceController
{
    public function __construct()
    {
        $this->dataOlt = new OltModel();
        $this->dataMetro = new MetroModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $getPaginated = $this->dataOlt->getPaginated(10, $keyword);
        $data = [
            'title'        => 'OLT',
            // 'dataOlt'      => $this->dataOlt->getAll(),
            'dataOlt'      => $getPaginated['dataOlt'],
            'pager'        => $getPaginated['pager'],
            'dataOlt2'     => $this->dataOlt->findAll(),
        ];
        return view('wan/olt/index', $data);
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
            'title' => 'Add OLT',
            'validation' => \config\Services::validation(),
            'dataMetro' => $this->dataMetro->findAll(),
        ];
        return view('wan/olt/new', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        //validasi form input
        if (!$this->validate([
            'idmetro' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Hostname Metro tidak boleh kosong',
                ],
            ],
        ])) {
            return redirect()->to('/wan/olt/new')->withInput();
        }
        $data = $this->request->getPost();
        $this->dataOlt->insert($data);

        return redirect()->to('/wan/olt')->with('success', 'Data berhasil disimpan.');
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
                'title' => 'Edit OLT',
                'validation' => \config\Services::validation(),
                'dataOlt' => $dataOlt,
                'dataMetro' => $this->dataMetro->findAll(),
                'dataMetro2' => $this->dataOlt->getAll(),
            ];
            return view('wan/olt/edit', $data);
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
        if (!$this->validate([
            'idmetro' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Hostname Metro tidak boleh kosong',
                ],
            ],
        ])) {
            return redirect()->to('/wan/olt/' . $id . '/edit' . $this->request->getVar('idolt'))->withInput();
        }

        $data = $this->request->getPost();

        $this->dataOlt->update($id, $data);

        return redirect()->to('/wan/olt')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->dataOlt->delete($id);

        return redirect()->to('/wan/olt')->with('success', 'Data berhasil dihapus.');
    }

    public function export()
    {

        $filename = 'OLT-' . date('ymd') . '.xlsx';
        //get all data
        // $dataOlt = $this->dataOlt->getAll();

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('dataolt');
        $builder->select('*')->where('dataolt.deleted_at', null);
        $builder->join('datametro',  'datametro.idmetro = dataolt.idmetro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('sto', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('ip_metro', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('hostname_metro', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('port_metro', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('ip_olt', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('hostname_olt', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('port_olt', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('platform', $keyword)->where('dataolt.deleted_at', null);
            $builder->orLike('type_olt', $keyword)->where('dataolt.deleted_at', null);
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
        $sheet->setCellValue('K1', 'OLT Type');

        //start coloum
        $coloumn = 2;
        foreach ($dataOlt as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->witel);
            $sheet->setCellValue('C' . $coloumn, $d->sto);
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

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()
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
        $builder->join('datametro',  'datametro.idmetro = dataolt.idmetro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
            $builder->orLike('sto', $keyword)->where('dataolt.deleted_at IS NOT NULL', null, false);
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
            $sheet->setCellValue('C' . $coloumn, $d->sto);
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
                    'witel'          => $value[1],
                    'sto'            => $value[2],
                    'port_metro'     => $value[3],
                    'hostname_olt'   => $value[4],
                    'ip_olt'         => $value[5],
                    'port_olt'       => $value[6],
                    'platform'       => $value[7],
                    'idmetro'        => 0,
                ];

                $this->dataOlt->insert($data);
            }

            return
                redirect()->back()->with('success', 'Data Excel Berhasil Diimport');
        } else {
            return redirect()->back()->with('error', 'Format File Tidak Sesuai');
        }
    }

    public function trash()
    {

        // $keyword = $this->request->getGet('keyword');

        // $getPaginatedTrash = $this->dataOlt->getPaginatedTrash(10, $keyword);
        // $data = [
        //     'title'        => 'OLT',
        //     'dataOlt'      => $getPaginatedTrash['dataOlt'],
        //     'pager'        => $getPaginatedTrash['pager'],
        // ];
        $keyword = $this->request->getGet('keyword');
        $getPaginatedTrash = $this->dataOlt->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'OLT',
            // 'dataOlt'      => $this->dataOlt->getTrash(),
            'dataOlt'      => $getPaginatedTrash['dataOlt'],
            'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('wan/olt/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('dataolt')->set('deleted_at', null, true)->where(['idolt' => $id])->update();
        } else {
            $this->db->table('dataolt')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/wan/olt')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wan/olt');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataOlt->delete($id, true);
            return redirect()->to('/wan/olt/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataOlt->purgeDeleted();
            return redirect()->to('/wan/olt/trash')->with('success', 'Data berhasil dihapus permanen.');
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
