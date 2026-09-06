<?php

namespace App\Controllers\Wan;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OltModel;
use App\Models\OntModel;
use App\Models\MetroModel;
use App\Models\StoModel;
use App\Models\NodebModel;
use App\Models\NodebAllModel;
use App\Models\AsrwanModel;
use App\Models\CactiModel;
use App\Models\OntTypeModel;
use CodeIgniter\CLI\Console;
use CodeIgniter\HTTP\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Hermawan\DataTables\DataTable;
use phpseclib3\Net\SSH2;
use Dompdf\Dompdf;
use Dompdf\Options;
use SoapClient;

// require_once 'dompdf/autoload.inc.php';
class Nodeb extends ResourceController
{
    protected $helpers = ['slug'];
    public function __construct()
    {
        $this->dataNodeb = new NodebModel();
        $this->datanodeb_all = new NodebAllModel();
        $this->dataOlt = new OltModel();
        $this->dataOnt = new OntModel();
        $this->dataMetro = new MetroModel();
        $this->dataSto = new StoModel();
        $this->dataAsrwan = new AsrwanModel();
        $this->dataCacti = new CactiModel();
        $this->ontType = new OntTypeModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */


    public function read()
    {

        $db = \Config\Database::connect();
        $array = ['datanodeb.deleted_at' => null]; //AGR023
        $builder = $db->table('datanodeb')
            // ->select('idnodeb, datanodeb.idsto as idsto_nodeb, site_id, site_name, datanodeb.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, dataolt.type_olt as type_olt, datanodeb.port_metro as port_metro_nodeb, datanodeb.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, datanodeb.serial_number as serial_number, odc, odp, tikor_site, on_air')
            ->select('datanodeb.idnodeb as idnodeb, datanodeb.idsto as idsto_nodeb, site_id, site_name, datanodeb.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, dataolt.type_olt as type_olt, datanodeb.port_metro as port_metro_nodeb, datanodeb.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, dataont.merk as merk, CONCAT(dataont.merk ,"-", dataont.type) as ont_type_gabungan, datanodeb.serial_number as serial_number, odc, odp, tikor_site, on_air, graph_id, cacti.idnodeb as cacti_idnodeb')
            ->where($array)
            ->groupBy('idnodeb')
            ->join('cacti',  'cacti.idnodeb = datanodeb.idnodeb', 'left')
            ->join('dataont',  'dataont.serial_number = datanodeb.serial_number','left')
            ->join('datasto',  'datasto.idsto = datanodeb.idsto')
            ->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

        $builder->groupBy('idnodeb');
        // $builder->having('COUNT(idnodeb > 1)');


        $query = $builder->get();
        $dataNodeb = $query->getResult();
        // print_r($dataNodeb);

        $data = array();

        foreach ($dataNodeb as $d) {

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
                // "id" => $d->idnodeb,
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
                "graph_id" => $d->graph_id,
            );
        }
        return $this->respond($data, 200);
    }
    public function index()
    {


        // $jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
        //     [
        //         'PHPSESSID' => '5681D30AEBCC703E7C12255610EC1274',
        //         'other_cookie' => 'barbaz1234'
        //     ],
        //     'http://10.62.165.58/ibooster/home.php?page=PG298'
        // );
        // $cookie = $jar->getCookieByName('PHPSESSID');

        // $kuki = $cookie->getValue();
        // $client = new \GuzzleHttp\Client(['base_uri' => 'http://10.62.165.58/ibooster/home.php?page=PG298']);
        // // $jar = new \GuzzleHttp\Cookie\CookieJar;
        // $r = $client->request('POST', 'http://10.62.165.58/ibooster/home.php?page=PG298nospeedy=111407116313&analis=ANALISA');

        // return var_dump($r);

        // $data = array('nospeedy' => '111407116313', 'analis' => 'ANALISA');
        // $response = \WpOrg\Requests\Requests::post('http://10.62.165.58/ibooster/home.php?page=PG298', array(), $data);
        // // $response = \WpOrg\Requests::post('https://httpbin.org/post', array(), $data);
        // return var_dump($response->body);

        //return phpinfo();

        // try {
        //     $soapclient = new SoapClient('http://10.62.165.36/air/index.php?wsdl');
        //     $param = "message: { ukurRequest: {nd: 111134102895, realm: telkom.net} }";
        //     $response = $soapclient->ukur($param);
        //     // $soapclient = new SoapClient('http://10.62.165.36/air/index.php?wsdl');
        //     // $param = "message: { datekRequest: {nd: 111134102895, realm: telkom.net} }";
        //     // $response = $soapclient->datek($param);
        //     return var_dump($response);
        //     echo '<br><br><br>';
        //     $array = json_decode(json_encode($response), true);
        //     print_r($array);
        //     echo '<br><br><br>';
        //     echo '<br><br><br>';
        //     foreach ($array as $item) {
        //         echo '<pre>';
        //         var_dump($item);
        //     }
        // } catch (\Exception $e) {
        //     return print_r("Gagal");
        // }

        // $ssh = new SSH2('10.60.190.16', 22);
        // $result = $ssh->login('sp19950104', 'G4rud402');

        // $ip = "10.199.11.170";

        // //test ping
        // // $ssh->read('[sp19950104@SSH02-ACCESS-STL ~]$');
        // // $ssh->write("ping -c 5 " . $ip . "\n");
        // // $hasil = $ssh->read();

        // //cek ONT
        // $ssh->read('[sp19950104@SSH02-ACCESS-STL ~]$');
        // $ssh->write("telnet " . $ip . "\n");
        // $ssh->write("\n");
        // $ssh->write("admin\nadmin_123\nen\nadmin_123\nsho temp\n");
        // $suhu = "suhu";
        // // $pattern = "/(Current temperature(.*))(?=\s+degree)/mi";
        // // preg_match_all($pattern, $suhu, $match);
        // // $hasilSuhu = $match[0][0] . " degree";

        // $ssh->write("sho int gei_0/2/1\n\n");
        // $redaman = "redaman";
        // // $pattern = "/(Optical RX power\s+:\s+(.*))(?=\s+Optical TX bias)/mi";
        // // preg_match_all($pattern, $redaman, $match);
        // // $hasilRedaman = $match[2][0];

        // $ssh->write("sho mac sl 1\n\nquit\ny\n");
        // $str = $ssh->read();

        // $pattern = "/(Current temperature(.*))(?=\s+degree)/mi";
        // preg_match_all($pattern, $str, $match);
        // $hasilSuhu = $match[0][0] . " degree";

        // $pattern = "/(Optical RX power\s+:\s+(.*))(?=\s+Optical TX bias)/mi";
        // preg_match_all($pattern, $str, $match);
        // $hasilRedaman = $match[2][0];

        // $pattern = "/(PORT MAC VID TYPE\s+(.*))(?=\s+dynamic)/mi";
        // preg_match_all($pattern, $str, $match);
        // $hasilMacBawah = $match[2][0];
        //test with putty
        // $ssh->enablePTY();
        // $ssh->setTimeout(1);
        // $ssh->exec('telnet ' . $ip);
        // $hasil = $ssh->read();
        // $ssh->write("\x03");

        // return print_r($hasilSuhu . "==========" . $hasilRedaman . "===========");

        // $ping = exec("ping -n 1 $ip", $output, $status);
        // echo $status;
        // $ssh->setTimeout(3);
        // $ssh->exec('ping ' . $ip, function ($str) {
        //     print_r($str);
        //     if (strpos($str, 'icmp_seq=5') !== false) {
        //         return print_r(true);
        //     }
        // });

        // if (!$result) {
        //     return print_r('Gagal');
        // }
        // return print_r('Oke');

        $keyword = $this->request->getGet('keyword');
        $dataNodeb = $this->dataNodeb->getAll($keyword);
        // $getPaginated = $this->dataNodeb->getPaginated(10, $keyword);
        $data = [
            'title'        => 'NODE-B',
            'dataNodeb'      => $dataNodeb,
            // 'dataNodeb'      => $getPaginated['dataNodeb'],
            // 'pager'        => $getPaginated['pager'],
            // 'dataNodeb2'     => $this->dataNodeb->findAll(),
            // 'ssh' => $ssh,
        ];
        return view('wan/nodeb/index', $data);
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

    public function surat()
    {
        //
        $nama = $this->request->getGet('nama');
        $nik = $this->request->getGet('nik');
        $unit = $this->request->getGet('unit');
        $telp = $this->request->getGet('telp');
        $nama_pelanggan = $this->request->getGet('nama_pelanggan');
        $alamat = $this->request->getGet('alamat');
        $no_order = $this->request->getGet('no_order');
        $jenis = $this->request->getGet('jenis');
        $tanggal = $this->request->getGet('tanggal');

        // $nama = "nama1,nama2,nama3";
        // $nik ="nik1,nik2,nik3";
        // $unit ="unit1,unit2,unit3";
        // $telp = "telp1,telp2,telp3";
        
        $petugas = [];
        
        $array_nama = explode(",",$nama);
        $array_nik = explode(",",$nik);
        $array_unit = explode(",",$unit);
        $array_telp = explode(",",$telp);
        
        foreach ($array_nama as $key => $value) {
            $petugas[] = [$value,  $array_nik[$key], $array_unit[$key], $array_telp[$key]];
        }
        
        // print_r($petugas);
        
        // return $this->respond($petugas, 200);
        
        $cust = [];

        // $nama_pelanggan = "nama1,nama2,nama3";
        // $alamat ="alamat1,alamat2,alamat3";
        // $no_order ="no_order1,no_order2,no_order3";

        $array_nama_pelanggan = explode(",",$nama_pelanggan);
        $array_alamat = explode(",",$alamat);
        $array_no_order = explode(",",$no_order);
        
        foreach ($array_nama_pelanggan as $key => $value) {
            $cust[] = [$value,  $array_alamat[$key], $array_no_order[$key]];
        }

        // $jenis = "Maintenance";
        // $tanggal = "01 Januari 2024";

        $logo = base64_encode(@file_get_contents('img/surat/Logo_TA.png'));
        $ttd = base64_encode(@file_get_contents('img/surat/ttd.jpg'));

        $data = [
            'title' => 'SURAT TUGAS',
            'logo'=>$logo,
            'ttd'=>$ttd,
            'ttd'=>$ttd,
            'petugas'=> $petugas,
            'cust' =>$cust,
            'jenis' =>$jenis,
            'tanggal' =>$tanggal,
        ];
        // return view('wan/surat', $data);
        $options = new Options();
        $options->set('defaultFont', "Poppins, Segoe UI, sans-serif");
        // $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        // $html = '<link type="text/css" href="https://10.27.110.100/template/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />';
        $html = view('wan/surat', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->render();
        // $dompdf->setBasePath(APPPATH . "/");
        // $dompdf->stream();
        $dompdf->stream('doccc.pdf', array("Attachment" => false));
    }
    function htmlToPDF()
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('pdf_view'));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
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
            'dataMetro' => $this->dataMetro->getAll(),
            'dataOlt' => $this->dataOlt->getAll(),
            'dataSto' => $this->dataSto->getAll(),
        ];
        return view('wan/nodeb/new', $data);
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
                'hostname_metro' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Hostname Metro tidak boleh kosong',
                    ],
                ],
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'site_id' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Site id tidak boleh kosong',
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
            ]
        )) {
            return redirect()->to('/wan/nodeb/new')->withInput();
        }

        $data = $this->request->getPost();

        $evidence = $this->request->getFile('evidence');

        if ($evidence->getError()) {
            $name_evidence = "";
        } else {

            // generate nama file random
            $name_evidence = $evidence->getRandomName();

            // pindahkan gambar
            $evidence->move('img/nodeb', $name_evidence);
        }

        $addData = [
            'evidence' => $name_evidence,
        ];

        $array_merge = array_merge($data, $addData);
        $this->dataNodeb->insert($array_merge);

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $site_id = $this->request->getVar('site_id');
        $serial_number = $this->request->getVar('serial_number');
        $ont_type = $this->request->getVar('ont_type');
        // Cek apakah serial_number tidak kosong
        if (!empty($serial_number) && strpos(strtoupper($ont_type), 'CASCADE') === false) {
            $dataOnt = $this->dataOnt->where('serial_number', $serial_number)->findAll();
            
            // Pastikan data ont tidak kosong dan status bukan "RUSAK"
            if (!empty($dataOnt) && end($dataOnt)->status != "RUSAK") {
                // Update status menjadi "BAIK", set installed, dan update deskripsi
                $this->db->table('dataont')
                    ->set(
                        'installed',
                        "IF(`installed` IS NULL OR `installed` = '', '$installed', `installed`)",
                        false
                    )
                    ->set(
                        'desc',
                        "IF(`desc` = '' OR `desc` IS NULL, 
                            '$installed Installed to $site_id', 
                            IF(LOCATE(' to $site_id', `desc`) > 0, 
                                `desc`, 
                                CONCAT(`desc`, '\n$installed Installed to $site_id')
                            )
                        )",
                        false
                    )
                    ->where(['status' => 'BAIK'])
                    ->where(['serial_number' => $serial_number])
                    ->update();
            }
        }

        //set nodeb to metro-e
        $sto = $this->request->getVar('idsto');
        $datanodeb_all = $this->datanodeb_all->where('site_id_all', $site_id)->first();
        if ($datanodeb_all) {
            $this->db->table('datanodeb_all')->set('transport', 'Metro-E Telkom')->where(['site_id_all' => $site_id])->update();
            $this->db->table('datanodeb_all')->set('sto', $sto)->where(['site_id_all' => $site_id])->update();
        }


        $graph_id = $this->request->getVar('graph_id');

        if ($graph_id) {
            $data = array(
                'graph_id'       => $graph_id,
                'idnodeb'     => $this->dataNodeb->getInsertID(),
            );
            $this->dataCacti->insert($data);
        }
        //change date not null
        // $this->db      = \Config\Database::connect();
        // $this->db->table('datanodeb')->set('on_air', '0000-00-00', true)->where(['on_air' => null])->update();

        return redirect()->to('/wan/nodeb')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {

        $dataNodeb = $this->dataNodeb->find($id);
        $Olt = $dataNodeb->hostname_olt;
        if (is_object($dataNodeb)) {
            $data = [
                'title'      => 'NODE-B',
                'validation' => \config\Services::validation(),
                'dataNodeb'  => $dataNodeb,
                'dataMetro'  => $this->dataMetro->getAll(),
                'dataOlt'    => $this->dataOlt->getAll(),
                'Olt'        => $this->dataOlt->find($Olt),
                'dataSto'    => $this->dataSto->getAll(),
                'dataCacti'    => $this->dataCacti->getId($id),
            ];
            return view('wan/nodeb/edit', $data);
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
                'hostname_metro' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Hostname Metro tidak boleh kosong',
                    ],
                ],
                'idsto' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'STO tidak boleh kosong',
                    ],
                ],
                'site_id' => [
                    'rules' => "required",
                    'errors' => [
                        'required' => 'Site id tidak boleh kosong',
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
            ]
        )) {
            return redirect()->to('/wan/nodeb/' . $id . '/edit')->withInput();
        }

        $data = $this->request->getPost();

        $evidence = $this->request->getFile('evidence');

        // cek gambar, apakah tetap gambar lama
        if ($evidence->getError() == 4) {
            $name_evidence = $this->request->getVar('evidence_old');
        } else {

            // generate nama file random
            $name_evidence = $evidence->getRandomName();

            // pindahkan gambar
            $evidence->move('img/nodeb', $name_evidence);

            //hapus gambar lama
            if ($this->request->getVar('evidence_old') != "") {
                unlink('img/nodeb' . $this->request->getVar('evidence_old'));
            }
        }
        $addData = [
            'evidence' => $name_evidence,
        ];
        $array_merge = array_merge($data, $addData);
        $this->dataNodeb->update($id, $array_merge);

        //set ont to installed
        $this->db      = \Config\Database::connect();
        $installed = date("Y-m-d");
        $site_id = $this->request->getVar('site_id');
        $serial_number = $this->request->getVar('serial_number');
        $ont_type = $this->request->getVar('ont_type');
        // Cek apakah serial_number tidak kosong
        if (!empty($serial_number) && strpos(strtoupper($ont_type), 'CASCADE') === false) {
            $dataOnt = $this->dataOnt->where('serial_number', $serial_number)->findAll();
            
            // Pastikan data ont tidak kosong dan status bukan "RUSAK"
            if (!empty($dataOnt) && end($dataOnt)->status != "RUSAK") {
                // Update status menjadi "BAIK", set installed, dan update deskripsi
                $this->db->table('dataont')
                    ->set(
                        'installed',
                        "IF(`installed` IS NULL OR `installed` = '', '$installed', `installed`)",
                        false
                    )
                    ->set(
                        'desc',
                        "IF(`desc` = '' OR `desc` IS NULL, 
                            '$installed Installed to $site_id', 
                            IF(LOCATE(' to $site_id', `desc`) > 0, 
                                `desc`, 
                                CONCAT(`desc`, '\n$installed Installed to $site_id')
                            )
                        )",
                        false
                    )
                    ->where(['status' => 'BAIK'])
                    ->where(['serial_number' => $serial_number])
                    ->update();
            }
        }

        $graph_id = $this->request->getVar('graph_id');
        $this->db      = \Config\Database::connect();
        $this->db->table('cacti')->where(['idnodeb' => $id])->delete(); //delete if exist

        if ($graph_id) {
            $data = array(
                'graph_id'       => $graph_id,
                'idnodeb'     => $id,
            );

            $this->dataCacti->insert($data);
        }
        //change date not null
        // $this->db      = \Config\Database::connect();
        // $this->db->table('datanodeb')->set('on_air', '0000-00-00', true)->where(['on_air' => null])->update();

        return redirect()->to('/wan/nodeb')->with('success', 'Data berhasil diupdate.');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
        $this->dataNodeb->delete($id);

        return redirect()->to('/wan/nodeb')->with('success', 'Data berhasil dihapus.');
    }

    public function listData()
    {
        if ($this->request->isAJAX()) {

            //get spesific data
            $keyword = $this->request->getGet('keyword');
            $dateParam = $this->request->getGet('dateParam');
            $fromdate = $this->request->getGet('fromdate');
            $untildate = $this->request->getGet('untildate');
            $choice = $this->request->getGet('choice');
            $values = $this->request->getGet('values');
            $addtime = ' 23:59:59';

            $db = db_connect();
            $array = ['datanodeb.deleted_at' => null];
            $builder = $db->table('datanodeb')
                ->select('idnodeb, datanodeb.idsto as idsto_nodeb, site_id, site_name, datanodeb.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, datanodeb.port_metro as port_metro_nodeb, datanodeb.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, serial_number, odc, odp, tikor_site, on_air')
                ->where($array)
                ->join('datasto',  'datasto.idsto = datanodeb.idsto')
                ->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

            if ($fromdate != '' && $untildate != '') {
                $builder->where($dateParam . ' BETWEEN "' . $fromdate . '" and "' . $untildate . $addtime . '"');
            }

            if ($values != '' && $choice != '') {
                foreach ($choice as $key => $c) {
                    if ($key == 0) {
                        $builder->like($c, $values[$key])->where($array)->where('on_air BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                    } else {
                        $builder->orlike($c, $values[$key])->where($array)->where('on_air BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                    }
                }
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column                
                ->add('action', function ($dataNodeb) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">
                    <a href=\"/wan/nodeb/detail/$dataNodeb->idnodeb/" . slug($dataNodeb->site_id . '-' . $dataNodeb->site_name) . "\"
                    type=\"button\" class=\"btn btn-sm btn-outline-dark\">
                    <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </a>
                    <a href=\"/wan/nodeb/$dataNodeb->idnodeb/edit\" type=\"button\" class=\"btn btn-sm btn-warning\">
                    <i class='fas fa-edit' data-toggle='tooltip' data-placement='bottom' title='Edit Data'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idnodeb=\"$dataNodeb->idnodeb\" data-site_id=\"$dataNodeb->site_id\" data-site_name=\"$dataNodeb->site_name\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                    </div>";
                })
                ->add('port_metro', function ($dataNodeb) {
                    if ($dataNodeb->port_metro_olt != null) {
                        return $dataNodeb->port_metro_olt;
                    }
                    return $dataNodeb->port_metro_nodeb;
                })
                ->toJson(true);
        }
    }

    public function listDataTrash()
    {
        if ($this->request->isAJAX()) {
            $db = db_connect();
            $array = ['datanodeb.deleted_at !=' => null];
            $builder = $db->table('datanodeb')
                ->select('idnodeb, datanodeb.idsto as idsto_nodeb, site_id, site_name, datanodeb.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, datanodeb.port_metro as port_metro_nodeb, datanodeb.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, serial_number, odc, odp, tikor_site, on_air')
                ->where($array)
                ->join('datasto',  'datasto.idsto = datanodeb.idsto')
                ->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column
                ->add('action', function ($dataNodeb) {
                    return "<div class=\"btn-group btn-group-sm\" role=\"group\">

                    <button type=\"button\" class=\"btn btn-sm btn-outline-dark\" onclick=\"showDetail($dataNodeb->idnodeb)\" data-toggle=\"modal\" data-target=\"#modaldetailData\"  data-backdrop=\"static\" data-keyboard=\"false\">
                    <i class='far fa-eye' data-toggle='tooltip' data-placement='bottom' title='View Detail'></i>
                    </button>
                    <a href=\"/wan/nodeb/restore/$dataNodeb->idnodeb\" type=\"button\" class=\"btn btn-sm btn-info\">
                    <i class='fas fa-recycle' data-toggle='tooltip' data-placement='bottom' title='Restore'></i>
                    </a>
                    <button type=\"button\" class=\"btn btn-sm btn-danger\" id=\"btnDelete\" data-idnodeb=\"$dataNodeb->idnodeb\" data-site_id=\"$dataNodeb->site_id\" data-site_name=\"$dataNodeb->site_name\">
                    <i class='far fa-trash-alt' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
                    </button>
                </div>";
                })
                ->add('port_metro', function ($dataNodeb) {
                    if ($dataNodeb->port_metro_olt != null) {
                        return $dataNodeb->port_metro_olt;
                    }
                    return $dataNodeb->port_metro_nodeb;
                })
                ->toJson(true);
        }
    }

    public function export()
    {

        $filename = 'NODEB-' . date('ymd-his') . '.xlsx';

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $dateParam = $this->request->getGet('dateParam');
        $fromdate = $this->request->getGet('fromdate');
        $untildate = $this->request->getGet('untildate');
        $choice = $this->request->getGet('choice');
        $values = $this->request->getGet('values');
        $addtime = ' 23:59:59';

        $db = \Config\Database::connect();
        $array = ['datanodeb.deleted_at' => null];
        $builder = $db->table('datanodeb')
            ->select('idnodeb, datanodeb.idsto as idsto_nodeb, site_id, site_name, datanodeb.hostname_metro as hostname_metro_nodeb, ip_metro, dataolt.port_metro as port_metro_olt, datanodeb.port_metro as port_metro_nodeb, datanodeb.hostname_olt as hostname_olt_nodeb, ip_olt, hostname_ont, ip_ont, port_onu, ont_type, serial_number, odc, odp, tikor_site, on_air')
            ->where($array)
            ->join('datasto',  'datasto.idsto = datanodeb.idsto')
            ->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro')
            ->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

        if ($fromdate != '' && $untildate != '') {
            $builder->where($dateParam . ' BETWEEN "' . $fromdate . '" and "' . $untildate . $addtime . '"');
        }

        if ($values != '' && $choice != '') {
            foreach ($choice as $key => $c) {
                if ($key == 0) {
                    $builder->like($c, $values[$key])->where($array)->where('on_air BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                } else {
                    $builder->orlike($c, $values[$key])->where($array)->where('on_air BETWEEN "' . $fromdate . '" and "' . $untildate . '"');
                }
            }
        }

        $query = $builder->get();
        $dataNodeb = $query->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'STO');
        $sheet->setCellValue('C1', 'SITE_ID');
        $sheet->setCellValue('D1', 'SITE_NAME');
        $sheet->setCellValue('E1', 'HOSTNAME_METRO');
        $sheet->setCellValue('F1', 'IP_METRO');
        $sheet->setCellValue('G1', 'PORT_METRO');
        $sheet->setCellValue('H1', 'HOSTNAME_OLT');
        $sheet->setCellValue('I1', 'IP_OLT');
        $sheet->setCellValue('J1', 'PORT_ONU');
        $sheet->setCellValue('K1', 'HOSTNAME_ONT');
        $sheet->setCellValue('L1', 'IP_ONT');
        $sheet->setCellValue('M1', 'ONT_TYPE');
        $sheet->setCellValue('N1', 'SERIAL_NUMBER');
        $sheet->setCellValue('O1', 'ODC');
        $sheet->setCellValue('P1', 'ODP');
        $sheet->setCellValue('Q1', 'COORDINATE');
        $sheet->setCellValue('R1', 'ON AIR');

        //start coloum
        $coloumn = 2;
        foreach ($dataNodeb as $d) {
            $port_metro = "";
            if ($d->port_metro_olt != null) {
                $port_metro = $d->port_metro_olt;
            }
            $port_metro = $d->port_metro_nodeb;

            $sheet->setCellValue('A' . $coloumn, ($coloumn - 1));
            $sheet->setCellValue('B' . $coloumn, $d->idsto_nodeb);
            $sheet->setCellValue('C' . $coloumn, $d->site_id);
            $sheet->setCellValue('D' . $coloumn, $d->site_name);
            $sheet->setCellValue('E' . $coloumn, $d->hostname_metro_nodeb);
            $sheet->setCellValue('F' . $coloumn, $d->ip_metro);
            $sheet->setCellValue('G' . $coloumn, $port_metro);
            $sheet->setCellValue('H' . $coloumn, $d->hostname_olt_nodeb);
            $sheet->setCellValue('I' . $coloumn, $d->ip_olt);
            $sheet->setCellValue('J' . $coloumn, $d->port_onu);
            $sheet->setCellValue('K' . $coloumn, $d->hostname_ont);
            $sheet->setCellValue('L' . $coloumn, $d->ip_ont);
            $sheet->setCellValue('M' . $coloumn, $d->ont_type);
            $sheet->setCellValue('N' . $coloumn, $d->serial_number);
            $sheet->setCellValue('O' . $coloumn, $d->odc);
            $sheet->setCellValue('P' . $coloumn, $d->odp);
            $sheet->setCellValue('Q' . $coloumn, $d->tikor_site);
            $sheet->setCellValue('R' . $coloumn, $d->on_air);
            $coloumn++;
        }

        $sheet->getStyle('A1:R1')->getFont()->setBold(true)
            ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:R1')->getFill()
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
        $sheet->getColumnDimension('R')->setAutoSize(true);


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
        // $dataNodeb = $this->dataNodeb->getAll();

        //get spesific data
        $keyword = $this->request->getGet('keyword');
        $db = \Config\Database::connect();
        $builder = $db->table('dataNodeb');
        $builder->select('*')->where('dataNodeb.deleted_at IS NOT NULL', null, false);
        $builder->join('datametro',  'datametro.hostname_metro = dataNodeb.hostname_metro');
        if ($keyword != '') {
            $builder->like('witel', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('idsto', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_metro', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_olt', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_olt', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('platform', $keyword)->where('dataNodeb.deleted_at IS NOT NULL', null, false);
        }
        $query = $builder->get();
        $dataNodeb = $query->getResult();

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
        foreach ($dataNodeb as $d) {
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $file = $this->request->getFile('file_excel');
        $extension = $file->getClientExtension();

        if (!in_array($extension, ['xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format File Tidak Sesuai');
        }

        $reader = $extension == 'xls'
            ? new \PhpOffice\PhpSpreadsheet\Reader\Xls()
            : new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadsheet = $reader->load($file);
        $dataNodeb = $spreadsheet->getActiveSheet()->toArray();

        $db = \Config\Database::connect();
        $db->transStart(); // 🔥 transaction

        foreach ($dataNodeb as $i => $value) {
            if ($i == 0) continue;

            // =====================
            // INSERT datanodeb
            // =====================

            $dataNodebInsert = [
                'idsto'          => $value[0],
                'site_id'        => $value[1],
                'site_name'      => $value[2],
                'hostname_metro' => $value[3],
                'port_metro'     => $value[4],
                'hostname_olt'   => $value[5],
                'port_onu'       => $value[6],
                'hostname_ont'   => $value[7],
                'ip_ont'         => $value[8],
                'ont_type'       => $value[9],
                'serial_number'  => $value[10],
                'odc'            => $value[11],
                'odp'            => $value[12],
                'tikor_site'     => $value[13],
                'on_air'         => !empty($value[14]) ? date('Y-m-d', strtotime($value[14])) : null,
            ];

            $this->dataNodeb->insert($dataNodebInsert);

            $idnodeb = $this->dataNodeb->getInsertID();

            // =====================
            // INSERT cacti
            // =====================
            if (!empty($value[15])) {
                $this->dataCacti->insert([
                    'graph_id' => $value[15],
                    'idnodeb'  => $idnodeb
                ]);
            }

            // =====================
            // DATA ONT & ONT TYPE
            // =====================
            $siteId = trim($value[1] ?? '');
            $serial = trim($value[10] ?? '');
            $ontRaw = trim($value[9] ?? '');

            if ($serial !== '' && strtoupper($serial) !== 'TO IPASO' && $ontRaw !== '') {

                // parsing merk - type
                $merk = $ontRaw;
                $type = null;

                if (strpos($ontRaw, '-') !== false) {
                    [$merk, $type] = explode('-', $ontRaw, 2);
                    $merk = trim($merk);
                    $type = trim($type);
                }

                // ---------- ont_type (anti double)
                $existType = $this->ontType
                    ->where('merk', $merk)
                    ->where('type', $type)
                    ->first();

                if (!$existType) {
                    $this->ontType->insert([
                        'merk' => $merk,
                        'type' => $type
                    ]);
                }
                
                $tglRaw   = $value[14];
                $tanggal  = (!empty($tglRaw) && $tglRaw != '0000-00-00')
                            ? date('Y-m-d', strtotime($tglRaw))
                            : null;

                // ---------- ont_type (anti double)
                $ont = $this->dataOnt
                    ->where('serial_number', $serial)
                    ->first();

                // ---------- dataont
                if (!$ont){
                    $desc = $tanggal
                            ? "$tanggal Installed to $siteId"
                            : "Installed to $siteId";
                    $this->dataOnt->insert([
                        'merk'          => $merk,
                        'type'          => $type,
                        'serial_number' => $serial,
                        'status'        => 'BAIK',
                        'idsto'         => $value[0],
                        'installed'     => !empty($value[14]) ? date('Y-m-d', strtotime($value[14])) : null,
                        'desc'          => $desc,
                    ]);                    
                }
                else {
                    // cek apakah siteid sudah pernah tercatat
                    if (!str_contains($ont->desc ?? '', "to $siteId")) {

                        if (!empty($ont->installed)){
                            $line = $tanggal
                                ? "$tanggal Cascade to $siteId"
                                : "Cascade to $siteId";
                        } else {
                            $line = $tanggal
                                ? "$tanggal Installed to $siteId"
                                : "Installed to $siteId";
                        }

                        $descBaru = trim(($ont->desc ?? '') . "\n" . $line);

                        $this->dataOnt->update(
                            $ont->idont, // primary key
                            [
                                'desc' => $descBaru
                            ]
                        );
                    }                
                }
            }
            // =====================
            // UPSERT datanodeb_all
            // =====================

            $siteIdAll   = trim($value[1] ?? '');
            $siteNameAll = trim($value[2] ?? '');
            $latLong     = trim($value[13] ?? '');
            $sto         = trim($value[0] ?? '');
            $siteNameAll = preg_replace('/\s-\s(ONT|LINK)\s\d+$/i', '', $siteNameAll);

            if ($siteIdAll !== '') {

                $existAll = $this->datanodeb_all
                    ->where('site_id_all', $siteIdAll)
                    ->first();

                if ($existAll) {

                    // karena primary key = site_id_all
                    $this->datanodeb_all->update(
                        $siteIdAll,
                        [
                            'transport' => 'Metro-E Telkom'
                        ]
                    );

                } else {

                    $this->datanodeb_all->insert([
                        'site_id_all'   => $siteIdAll,
                        'site_name_all' => $siteNameAll,
                        'lat_long'      => $latLong,
                        'transport'     => 'Metro-E Telkom',
                        'sto'           => $sto,
                    ]);
                }
            }

        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Import gagal, data di-rollback');
        }

        return redirect()->back()->with('success', 'Data Excel berhasil diimport');
    }

    // public function import()
    // {
    //     set_time_limit(0);
    //     ini_set('memory_limit', '-1');

    //     $file = $this->request->getFile('file_excel');
    //     $extension = $file->getClientExtension();

    //     if (!in_array($extension, ['xlsx', 'xls'])) {
    //         return redirect()->back()->with('error', 'Format File Tidak Sesuai');
    //     }

    //     $reader = $extension == 'xls'
    //         ? new \PhpOffice\PhpSpreadsheet\Reader\Xls()
    //         : new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

    //     $spreadsheet = $reader->load($file);
    //     $rows = $spreadsheet->getActiveSheet()->toArray();

    //     $db = \Config\Database::connect();
    //     $db->transStart();

    //     foreach ($rows as $i => $value) {
    //         if ($i == 0) continue;

    //         $graphId = trim($value[15] ?? '');

    //         // skip kalau graph kosong
    //         if ($graphId === '') continue;

    //         // ambil key kombinasi
    //         $siteId        = trim($value[1] ?? '');
    //         $hostnameMetro = trim($value[3] ?? '');
    //         $portMetro     = trim($value[4] ?? '');
    //         $hostnameOlt   = trim($value[5] ?? '');
    //         $portOnu       = trim($value[6] ?? '');

    //         // cari datanodeb
    //         $nodeb = $this->dataNodeb
    //             ->where('site_id', $siteId)
    //             ->where('hostname_metro', $hostnameMetro)
    //             ->where('port_metro', $portMetro)
    //             ->where('hostname_olt', $hostnameOlt)
    //             ->where('port_onu', $portOnu)
    //             ->first();

    //         // kalau tidak ketemu → skip
    //         if (!$nodeb) continue;

    //         $idnodeb = $nodeb->idnodeb;

    //         // cek cacti existing
    //         $cacti = $this->dataCacti
    //             ->where('idnodeb', $idnodeb)
    //             ->first();

    //         if ($cacti) {
    //             // update
    //             $this->dataCacti->update($cacti->id, [
    //                 'graph_id' => $graphId
    //             ]);
    //         } else {
    //             // insert
    //             $this->dataCacti->insert([
    //                 'idnodeb'  => $idnodeb,
    //                 'graph_id' => $graphId
    //             ]);
    //         }
    //     }

    //     $db->transComplete();

    //     if ($db->transStatus() === false) {
    //         return redirect()->back()->with('error', 'Update cacti gagal, rollback');
    //     }

    //     return redirect()->back()->with('success', 'Cacti berhasil direvisi tanpa mengubah data lain');
    // }


    public function trash()
    {

        $keyword = $this->request->getGet('keyword');
        $dataNodeb = $this->dataNodeb->getTrash($keyword);
        // $getPaginatedTrash = $this->dataNodeb->getPaginatedTrash(10, $keyword);
        $data = [
            'title'        => 'NODE-B',
            'dataNodeb'      => $dataNodeb,
            // 'dataNodeb'      => $getPaginatedTrash['dataNodeb'],
            // 'pager'        => $getPaginatedTrash['pager'],
        ];

        return view('wan/nodeb/trash', $data);
    }

    public function restore($id = null)
    {
        $this->db      = \Config\Database::connect();

        if ($id != null) {
            $this->db->table('datanodeb')->set('deleted_at', null, true)->where(['idnodeb' => $id])->update();
        } else {
            $this->db->table('datanodeb')->set('deleted_at', null, true)->where('deleted_at is NOT NULL', NULL, FALSE)->update();
        }
        if ($this->db->affectedRows() > 0) {
            return redirect()->to('/wan/nodeb')->with('success', 'Data berhasil direstore.');
        }

        return redirect()->to('/wan/nodeb');
    }

    public function deletetrash($id = null)
    {
        //
        if ($id != null) {
            $this->dataNodeb->delete($id, true);
            return redirect()->to('/wan/nodeb/trash')->with('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->dataNodeb->purgeDeleted();
            return redirect()->to('/wan/nodeb/trash')->with('success', 'Data berhasil dihapus permanen.');
        }
    }

    function detailData($id = null)
    {
        //get spesific data
        // $id = $this->request->getGet('idasr');

        $dataNodeb = $this->dataNodeb->GetById(array($id));
        $dataAsrwan = $this->dataAsrwan->GetServiceNo($dataNodeb[0]['site_id']);
        $site_id = base64_encode(@file_get_contents('https://siborder.telkom.co.id/img/datek/rollout/site-photo-' . $dataNodeb[0]['site_id'] . '.jpg'));
        $site_image = $site_id;
        if ($site_id === false) {
            $site_image = "";
        }

        // if (!str_contains($dataNodeb[0]['ont_type'], 'direct')) {
        //     try {
        //         $ssh = new SSH2('10.62.165.21', 22);
        //         $ssh->login('sp19950104', 'G4rud401');
        //         // $result = $ssh->login('sp19950104', 'G4rud402');

        //         $ip = $dataNodeb[0]['ip_ont'];

        //         $ssh->enablePTY();
        //         $ssh->setTimeout(3);
        //         $ssh->exec('ping ' . $ip);
        //         $hasil = $ssh->read();
        //         $ssh->write("\x03");
        //         if (strpos($hasil, 'icmp_seq=1') !== $hasil = false) {
        //             $hasil = true;
        //         }

        //         $data = [
        //             'title'          => 'NODE-B',
        //             'id'             => $id,
        //             'dataNodeb'      => $dataNodeb,
        //             'statusOnt'      => $hasil,
        //             'dataAsrwan'     => $dataAsrwan,
        //             'site_id'        => $site_image,
        //         ];


        //         return view('wan/nodeb/detail', $data);
        //     } catch (\Exception $e) {

        //         $hasil = 500;
        //         $data = [
        //             'title'          => 'NODE-B',
        //             'id'             => $id,
        //             'dataNodeb'      => $dataNodeb,
        //             'statusOnt'      => $hasil,
        //             'dataAsrwan'     => $dataAsrwan,
        //             'site_id'        => $site_image,
        //         ];
        //         return view('wan/nodeb/detail', $data);
        //     }
        // } else {
        //     $hasil = 'direct';
        //     $data = [
        //         'title'          => 'NODE-B',
        //         'id'             => $id,
        //         'dataNodeb'      => $dataNodeb,
        //         'statusOnt'      => $hasil,
        //         'dataAsrwan'     => $dataAsrwan,
        //         'site_id'        => $site_image,
        //     ];
        //     return view('wan/nodeb/detail', $data);
        // }
        $data = [
            'title'          => 'NODE-B',
            'id'             => $id,
            'dataNodeb'      => $dataNodeb,
            // 'statusOnt'      => $hasil,
            'dataAsrwan'     => $dataAsrwan,
            'site_id'        => $site_image,
        ];
        return view('wan/nodeb/detail', $data);
    }

    function mapNodeb()
    {

        $dataNodeb = $this->dataNodeb->findAll();
        $datanodeb_all = $this->datanodeb_all->getRadio();
        $data = [
            'title'          => 'NODE-B',
            'datanodeb_all'      => $datanodeb_all,
            'dataNodeb'      => $dataNodeb,

        ];
        return view('wan/nodeb/map', $data);
    }

    function ukurNodeb()
    {

        if ($this->request->isAJAX()) {
            $id = $this->request->getGet('idnodeb');
            $dataNodeb = $this->dataNodeb->GetById(array($id));

            $ssh = new SSH2('10.60.190.16', 22);
            $ssh->login('sp19950104', 'G4rud401');

            $ip = $dataNodeb[0]['ip_ont'];

            //cek ONT up / down
            $ssh->enablePTY();
            $ssh->setTimeout(1);
            $ssh->exec('ping ' . $ip);
            $hasil = $ssh->read();
            $ssh->write("\x03");
            if (strpos($hasil, 'icmp_seq=1') !== $hasil = false) {
                $hasil = true;
            }


            //cek ONT
            if ($hasil == 1) {

                $ssh = new SSH2('10.60.190.16', 22);
                $ssh->login('sp19950104', 'G4rud401');

                $ssh->read('[sp19950104@SSH02-ACCESS-STL ~]$');
                $ssh->write("telnet " . $ip . "\n");
                $ssh->write("\n");
                $ssh->write("admin\nadmin_123\nen\nadmin_123\nsho temp\n");

                $ssh->write("sho int gei_0/2/1\n\n");
                $ssh->write("sho mac sl 1\n\nquit\ny\n");
                $str = $ssh->read();

                $pattern = "/(Current temperature\s+:(.*))(?=\s+degree)/mi";
                preg_match_all($pattern, $str, $match);
                $hasilSuhu = $match[2][0] . " degree";

                $pattern = "/(Optical RX power\s+:\s+(.*))(?=\s+Optical TX bias)/mi";
                preg_match_all($pattern, $str, $match);
                $hasilRedaman = $match[2][0];

                $pattern = "/(Optical RX power\s+:\s+(.*))(?=\s+Optical TX bias)/mi";
                preg_match_all($pattern, $str, $match);
                $hasilMac = $match[2][0];
            } else {
                $hasilSuhu = "-";
                $hasilRedaman = "-";
                $str = "-";
            }


?>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Current temperature</th>
                                    <th>:</th>
                                    <td><?= $hasilSuhu ?></td>
                                </tr>
                                <tr>
                                    <th>Optical RX power</th>
                                    <th>:</th>
                                    <td><?= $hasilRedaman ?></td>
                                </tr>
                                <tr>
                                    <th>Mac Downlink</th>
                                    <th>:</th>
                                    <td>Still stuck</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<?php
        }
    }
}
