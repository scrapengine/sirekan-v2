<?php

namespace App\Controllers;

class Dummy extends BaseController
{
    public function index()
    {
        //cara 1
        $builder = $this->db->table('gawe');
        $query   = $builder->get();  // Produces: SELECT * FROM mytable

        //cara 2
        // $query = $this->db->query("SELECT * FROM gawe");

        $data = [
            'title' => 'Dummy',
            'gawe' => $query->getResult(),
        ];
        return view('wan/dummy', $data);
    }

    public function add()
    {

        $data = [
            'title' => 'Add Dummy',
        ];
        return view('wan/add', $data);
    }
    public function store()
    {
        //cara 1
        $data = $this->request->getPost();

        //cara 2 deklasrasikan masing" input
        // $data = [
        //     'gawe_name' => $this->request->getVar('gawe_name'),
        // ];

        $this->db->table('gawe')->insert($data);

        if ($this->db->affectedRows() > 0) {
            return redirect()->to('wan/dummy')->with('success', 'Data berhasil disimpan.');
        }
    }

    public function edit($id = null)
    {
        if ($id != null) {
            $query = $this->db->table('gawe')->getWhere(['gawe_id' => $id]);
            if ($query->resultID->num_rows > 0) {
                $data = [
                    'title' => 'Update Dummy',
                    'gawe' => $query->getRow()
                ];
                return view('wan/edit', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        //cara 1
        $data = $this->request->getPost();
        unset($data['_method']);

        //cara 2 deklarasikan input
        // $data = [
        //     'name_gawe' => $this->request->getVar('name_gawe'),
        // ];

        $this->db->table('gawe')->where(['gawe_id' => $id])->update($data);
        return redirect()->to('wan/dummy')->with('success', 'Data berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->db->table('gawe')->where(['gawe_id' => $id])->delete();
        return redirect()->to('wan/dummy')->with('success', 'Data berhasil dihapus.');
    }
}
