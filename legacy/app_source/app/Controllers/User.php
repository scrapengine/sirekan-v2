<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \Myth\Auth\Authorization\GroupModel;
use \Myth\Auth\Models\UserModel;
use \Myth\Auth\Password;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Hermawan\DataTables\DataTable;

class User extends ResourceController
{
    protected $db, $builder;

    public function __construct()
    {
        $this->db       = \Config\Database::connect();
        $this->builder  = $this->db->table('users');
        $this->GroupModel = new GroupModel();
        $this->UserModel = new UserModel();
    }

    public function index()
    {

        $data = [
            'title' => 'My Profile',
            'validation' => \config\Services::validation(),
        ];
        $users = new \Myth\Auth\Models\UserModel();
        $data['users'] = $users->findAll();

        return view('user/index', $data);
    }

    public function update($id = null)
    {

        if (!$this->validate([
            'user_image' => [
                'rules' => 'max_size[user_image,1024]|is_image[user_image]|mime_in[user_image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Format tidak sesuai',
                    'mime_in' => 'Format tidak sesuai',
                ]
            ]
        ])) {
            return redirect()->to('user')->withInput();
        }

        $user_image = $this->request->getFile('user_image');

        // cek gambar, apakah tetap gambar lama
        if ($user_image->getError() == 4) {
            $name_user_image = $this->request->getVar('user_image_old');
        } else {

            $name_user_image = $user_image->getRandomName();

            //crop image 
            $image = \Config\Services::image()
                ->withFile($user_image)
                ->fit(200, 200, 'center')
                ->save(FCPATH . '/img/' . $name_user_image);

            // // generate nama file random
            // $name_user_image = $user_image->getRandomName();
            // // pindahkan gambar
            // $user_image->move('img', $name_user_image);
            // cek jika file gambarnya default.svg
            $default = $this->db->table('users')->where(['user_image' => 'default.svg']);
            if (!$default) {
                // hapus file yang lama
                unlink('img/' . $this->request->getVar('user_image_old'));
            }
        }


        //cara 1
        // $data = $this->request->getPost();

        //cara 2 deklarasikan input
        $data = [
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
            'fullname' => $this->request->getVar('fullname'),
            'user_image' => $name_user_image,

        ];

        $this->db->table('users')->where(['id' => $id])->update($data);
        return redirect()->to('user')->with('success', 'Data berhasil diupdate.');
    }

    public function userlist()
    {
        $data['title'] = 'USER';
        // $users = new \Myth\Auth\Models\UserModel();
        // $data['users'] = $users->findAll();

        $this->builder->select('users.id as userid, username, email, name, active, password_hash');
        $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        $query = $this->builder->get();

        $data['users'] = $query->getResult();
        $data['GroupModel'] = $this->GroupModel->findAll();

        return view('user/userlist', $data);
    }

    public function detail($id = 0)
    {

        $data = [
            'title' => 'User',
            'validation' => \config\Services::validation(),
        ];
        // $users = new \Myth\Auth\Models\UserModel();
        // $data['users'] = $users->findAll();

        $this->builder->select('users.id as userid, username, email, fullname, user_image, name');
        $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        $this->builder->where('users.id', $id);
        $query = $this->builder->get();

        $data['users'] = $query->getRow();

        if (empty($data['users'])) {
            return redirect()->to('/user/userlist');
        }

        return view('user/detail', $data);
    }



    public function activate()
    {
        $userModel = new UserModel();

        $data = [
            'activate_hash' => null,
            'active' => $this->request->getVar('active') == '0' || '' ? '1' : '0',
        ];
        $userModel->update($this->request->getVar('id'), $data);

        return redirect()->to(base_url('user/userlist'));
    }

    public function changePassword($id = null)
    {
        // if ($id == null) {
        //     return redirect()->to(base_url('user/userlist'));
        // } else {
        //     $data = [
        //         'id' => $id,
        //         'title' => 'Update Password',
        //     ];
        //     return view('user/changepassword', $data);
        // }

        $UserModel = $this->UserModel->find($id);
        if (is_object($UserModel)) {
            $data = [
                'title' => 'Password',
                'validation' => \config\Services::validation(),
                'UserModel' => $UserModel,
                'id' => $id,
            ];
            return view('user/changepassword', $data);
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function setPassword()
    {

        $id = $this->request->getVar('id');
        if (!$this->validate(
            [
                'old_password'     => 'required|old_password',
                'password'         => 'required|strong_password',
                'pass_confirm'     => 'required|matches[password]',
            ]
        )) {
            return redirect()->to('/user/changePassword/' . $id)->withInput();
        }
        // return view('user/changepassword', $data);

        $userModel = new UserModel();
        $data = [
            'password_hash' => Password::hash($this->request->getVar('password')),
            'reset_hash' => null,
            'reset_at' => null,
            'reset_expires' => null,
        ];
        $userModel->update($this->request->getVar('id'), $data);

        return redirect()->to(base_url('user/userlist'));
    }

    public function changeGroup()
    {
        $userId = $this->request->getVar('id');
        $groupId = $this->request->getVar('group');

        $groupModel = new GroupModel();
        $groupModel->removeUserFromAllGroups(intval($userId));

        $groupModel->addUserToGroup(intval($userId), intval($groupId));

        return redirect()->to(base_url('/user/userlist'));
    }


    //--------------------------------------------------------------------------

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
            'title' => 'USER',
            'validation' => \config\Services::validation(),
        ];
        return view('user/new', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->UserModel->delete($id);

        return redirect()->to('/user/userlist')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {
        if ($this->request->isAJAX()) {
            $keyword = $this->request->getGet('keyword');
            $db = db_connect();
            $builder = $db->table('dataNodeb')
                ->select('witel, datasto.idsto, datametro.hostname_metro, datametro.ip_metro, port_metro, hostname_olt, ip_olt, port_olt, platform')
                ->where('dataNodeb.deleted_at', null)
                ->join('datametro',  'datametro.hostname_metro = dataNodeb.hostname_metro')
                ->join('datasto',  'datasto.idsto = datametro.idsto');


            if ($keyword != '') {
                $builder->like('witel', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('idsto', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('ip_metro', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('hostname_metro', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('port_metro', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('ip_olt', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('hostname_olt', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('port_olt', $keyword)->where('dataNodeb.deleted_at', null);
                $builder->orLike('platform', $keyword)->where('dataNodeb.deleted_at', null);
            };

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataNodeb) {
                    return "<div class=\"btn-group\" role=\"group\">

                    <a href=\"/user/$dataNodeb->hostname_olt/edit\" type=\"button\" class=\"btn btn-sm\"><i class='fas fa-edit' style='color: orange'></i>
                    </a>
                    <input type=\"hidden\" name=\"deletesiteid\" value=\"$dataNodeb->hostname_olt\">
                    <button type=\"button\" class=\"btn btn-sm\" data-toggle=\"modal\" data-target=\"#delete$dataNodeb->hostname_olt\">
                        <i class='fa fa-trash' style='color: red'></i>
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
            $builder = $db->table('datanodeb')
                ->select('witel, datasto.idsto, datametro.hostname_metro, datametro.ip_metro, port_metro, hostname_olt, ip_olt, port_olt, platform')
                ->where('dataNodeb.deleted_at !=', null)
                ->join('datametro',  'datametro.hostname_metro = dataNodeb.hostname_metro')
                ->join('datasto',  'datasto.idsto = datametro.idsto');

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataNodeb) {
                    return "<div class=\"btn-group\" role=\"group\">

                    <a href=\"/user/restore/$dataNodeb->hostname_olt\" type=\"button\" class=\"btn btn-sm\"><i class='fas fa-recycle' style='color: DodgerBlue'></i>
                    </a>
                    <input type=\"hidden\" name=\"deleteolt\" value=\"$dataNodeb->hostname_olt\">
                    <button type=\"button\" class=\"btn btn-sm\" data-toggle=\"modal\" data-target=\"#delete$dataNodeb->hostname_olt\">
                        <i class='fa fa-trash' style='color: red'></i>
                    </button>
                </div>";
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'USERS-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $this->builder->select('users.id as userid, username, email, fullname, name, active');
        $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');

        $query = $this->builder->get();
        $dataQuery = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'USERNAME');
        $sheet->setCellValue('C1', 'EMAIL');
        $sheet->setCellValue('D1', 'FULLNAME');
        $sheet->setCellValue('E1', 'ROLE');
        $sheet->setCellValue('F1', 'ACTIVE');

        //start coloum
        $coloumn = 2;
        foreach ($dataQuery as $d) {
            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->username);
            $sheet->setCellValue('C' . $coloumn, $d->email);
            $sheet->setCellValue('D' . $coloumn, $d->fullname);
            $sheet->setCellValue('E' . $coloumn, $d->name);
            $sheet->setCellValue('F' . $coloumn, $d->active);
            $coloumn++;
        }

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()
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
        $sheet->getStyle('A1:F' . ($coloumn - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
    public function export_trash()
    {
        //=======================
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
            $dataNodeb = $spreadsheet->getActiveSheet()->toArray();
            foreach ($dataNodeb as $d => $value) {
                if ($d == 0) {
                    continue;
                }
                $data = [
                    'site_id'           => $value[0],
                    'site_name'         => $value[1],
                    'port_metro'        => $value[2],
                    'hostname_ont'      => $value[3],
                    'ip_ont'            => $value[4],
                    'port_onu'          => $value[5],
                    'ont_type'          => $value[6],
                    'serial_number'     => $value[7],
                    'odc'               => $value[8],
                    'odp'               => $value[9],
                    'tikor_site'        => $value[10],
                    'on_air'            => $value[11],
                    'idsto'             => $value[12],
                    'hostname_metro'    => $value[13],
                    'hostname_olt'      => $value[14],
                ];

                $this->dataNodeb->insert($data);
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
        $dataNodeb = $this->dataNodeb->getTrash($keyword);
        // $getPaginatedTrash = $this->dataNodeb->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'USER',
            'dataNodeb'      => $dataNodeb,
            // 'dataNodeb'      => $getPaginatedTrash['dataNodeb'],
            // 'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('user/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('dataNodeb')->set('deleted_at', null, true)->where(['idnodeb' => $id])->update();
        } else {
            $this->db->table('dataNodeb')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/user')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/user');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->UserModel->delete($id, true);
            return redirect()->to('/user/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->UserModel->purgeDeleted();
            return redirect()->to('/user/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }
}
