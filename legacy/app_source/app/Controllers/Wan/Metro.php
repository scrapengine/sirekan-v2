<?php

namespace App\Controllers\Wan;

use App\Controllers\BaseController;
use App\Models\MetroModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\RESTful\ResourcePresenter;
use Config\Services;
use \Hermawan\DataTables\DataTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Metro extends BaseController
{
    public function __construct()
    {
        $this->dataMetro = new MetroModel();
    }

    public function index()
    {

        $dataMetro = $this->dataMetro->findAll();
        $data = [
            'title' => 'Data Metro',
            'dataMetro' => $dataMetro,
        ];
        helper('url');
        return view('/wan/metro/index', $data);
    }

    public function listData()
    {
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $builder = $db->table('datametro')->select('idmetro, hostname_metro, ip_metro')->where('idmetro !=', 0);

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataMetro) {
                    return "<td class=\"btn-group\" role=\"group\">

                    <a href=\"/wan/olt/$dataMetro->idmetro/edit\" type=\"button\" class=\"btn btn-sm\"><i class='fas fa-edit' style='color: orange'></i>
                    </a>
                    <input type=\"hidden\" name=\"deletesiteid\" value=\"$dataMetro->idmetro\">
                    <button type=\"button\" class=\"btn btn-sm\" data-toggle=\"modal\" data-target=\"#delete$dataMetro->idmetro\">
                        <i class='fa fa-trash' style='color: red'></i>
                    </button>
                </td>";
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'METRO-' . date('ymd') . '.xlsx';
        //get all data
        // $dataOlt = $this->dataOlt->getAll();

        //get spesific data
        $keyword = $this->request->getGet('datametro_filter');
        $db = \Config\Database::connect();
        $builder = $db->table('datametro');
        $builder->select('*')->where('datametro.idmetro !=', 0);

        if ($keyword != '') {
            $builder->like('hostname_metro', $keyword)->where('datametro.idmetro !=', 0);
            $builder->orLike('ip_metro', $keyword)->where('datametro.idmetro !=', 0);
        }

        $query = $builder->get();
        $dataOlt = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'HOSTNAME METRO');
        $sheet->setCellValue('C1', 'IP METRO');

        //start coloum
        $coloumn = 2;
        foreach ($dataOlt as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->hostname_metro);
            $sheet->setCellValue('C' . $coloumn, $d->ip_metro);
            $coloumn++;
        }

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
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
        $sheet->getStyle('A1:C' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);


        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
