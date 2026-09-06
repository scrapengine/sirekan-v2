<?php

namespace App\Controllers;

use App\Models\NodebModel;

class Wan extends BaseController
{
    protected $nodebModel;

    public function __construct()
    {
        $this->nodebModel   = new NodebModel();
    }

    public function index()
    {
        $dataNodeb = $this->nodebModel->findAll();

        $data = [
            'title' => 'Data Node-B',
            'dataNodeb' => $dataNodeb
        ];


        return view('wan/datanodeb', $data);
    }

    public function addnodeb()
    {

        $data = [
            'title' => 'Tambah Data Node-B',
            'validation' => \config\Services::validation()
        ];


        return view('wan/addnodeb', $data);
    }

    public function save()
    {

        //validasi form input
        if (!$this->validate([
            'site_id' => 'required'
        ])) {
            $validation = \config\Services::validation();
            return redirect()->to('/wan/addnodeb')->withInput();
        }

        $this->nodebModel->save([

            'sto' => $this->request->getVar('sto'),
            'site_id' => $this->request->getVar('site_id')

        ]);

        session()->setFlashdata('pesan', 'Data berhasil disimpan.');

        return redirect()->to('/wan');
    }


    public function deletenodeb($idnodeb)
    {
        $this->nodebModel->delete($idnodeb);

        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/wan');
    }


    public function updatenodeb($idnodeb)
    {

        $data = [
            'title' => 'Update Data Node-B',
            'validation' => \config\Services::validation(),
            'dataNodeb' => $this->nodebModel->getNodeb($idnodeb)

        ];

        return view('wan/updatenodeb', $data);
    }

    public function saveupdatenodeb($idnodeb)
    {

        //validasi form input
        if (!$this->validate([
            'site_id' => 'required'
        ])) {
            $validation = \config\Services::validation();
            return redirect()->to('/wan/updatenodeb/' . $this->request->getVar('idnodeb'))->withInput();
        }

        $this->nodebModel->save([
            'idnodeb' => $idnodeb,
            'sto' => $this->request->getVar('sto'),
            'site_id' => $this->request->getVar('site_id')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diupdate.');

        return redirect()->to('/wan');
    }

    //--------------------------------------------------------------------------

}
