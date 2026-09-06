<?php

namespace App\Controllers;

use App\Models\AsrwanModel;
use App\Models\FfwanModel;
use App\Models\OntModel;
use App\Models\StoModel;
use App\Models\OntTypeModel;
use \Hermawan\DataTables\DataTable;

class Home extends BaseController
{
    protected $helpers = ['custom'];
    public function __construct()
    {
        $this->dataAsrwan = new AsrwanModel();
        $this->dataFfwan = new FfwanModel();
        $this->dataOnt = new OntModel();
        $this->dataSto = new StoModel();
        $this->dataOntType = new OntTypeModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Login',
        ];
        return view('auth/login', $data);
    }

    public function register()
    {
        $data = [
            'title' => 'Register',
        ];
        return view('auth/register', $data);
    }

    public function user()
    {
        return view('user/index');
    }

    public function home()
    {

        // $countWeekWan = $this->dataAsrwan->countWeekWan();
        // $countMonthWan = $this->dataAsrwan->countMonthWan();
        // $countMonthCcan = $this->dataAsrwan->countMonthCcan();
        // $countMonthWifi = $this->dataAsrwan->countMonthWifi();
        // // $date = date('F', mktime(0, 0, 0, 5, 1));
        // return print_r($countMonthWan);

        // foreach ($countMonthWan as $result) {
        //     $data[] = array(
        //         'month'   => $result['month'],
        //         'wan' => $result['total']
        //     );
        // }
        // foreach ($countMonthCcan as $result) {
        //     $dataCcan[] = $result['total'];
        // }
        // foreach ($countMonthWifi as $result) {
        //     $dataWifi[] = $result['total'];
        // }
        // $array1 = $data;
        // $array2 = $dataCcan;
        // $array3 = $dataWifi;

        // foreach ($array1 as $i => &$row) {
        //     $row['ccan'] = $array2[$i];
        // }

        // foreach ($array1 as $i => &$row) {
        //     $row['wifi'] = $array3[$i];
        // }
        // return json_encode($array1);

        $temp = [];
        $byType = [];
        $i = 0;
        $getTypeOnt = $this->dataOnt->getGroup('dataont.type')->getResultArray();

        foreach ($this->dataSto->getAll() as $idsto) :
            $temp[$i]['idsto'] = $idsto->idsto;
            // $temp[$i] = '<strong>Total</strong>';
            // foreach ($this->dataSto->getAll() as $idsto) :
            //     $temp[$i]['idsto'] = '<strong>' . $this->dataOnt->totalWhereHorizontal(['idsto' => $idsto->idsto])->total . '</strong>';

            // endforeach;

            foreach ($getTypeOnt as $typeOnt) :
                $tempType = $this->dataOnt->jumlahAsr(['dataont.idsto' => $idsto->idsto], ['type' => $typeOnt['type']], ['allocation' => 'ASSURANCE'])->getRowArray();

                $temp[$i][$typeOnt['type']] = $tempType['jumlah'];
                if ($temp[$i][$typeOnt['type']] != 0) {
                    $temp[$i][$typeOnt['type']] = '<span class="text-info font-weight-bold">' . $tempType['jumlah'] . '</span>';
                }

            endforeach;

            array_push($byType, $temp[$i]);
            $i++;

        endforeach;
        // ==========================================
        $temp[$i]['idsto'] = 'FULFILLMENT';
        foreach ($getTypeOnt as $typeOnt) :
            $tempType = $this->dataOnt->jumlahFf(['type' => $typeOnt['type']], ['allocation' => 'FULFILLMENT'])->getRowArray();

            $temp[$i][$typeOnt['type']] = $tempType['jumlah'];
            if ($temp[$i][$typeOnt['type']] != 0) {
                $temp[$i][$typeOnt['type']] = '<span class="text-info font-weight-bold">' . $tempType['jumlah'] . '</span>';
            }
        endforeach;
        array_push($byType, $temp[$i]);

        // ==========================================
        $temp[$i]['idsto'] = 'OLO';
        foreach ($getTypeOnt as $typeOnt) :
            $tempType = $this->dataOnt->jumlahFf(['type' => $typeOnt['type']], ['allocation' => 'OLO'])->getRowArray();

            $temp[$i][$typeOnt['type']] = $tempType['jumlah'];
            if ($temp[$i][$typeOnt['type']] != 0) {
                $temp[$i][$typeOnt['type']] = '<span class="text-info font-weight-bold">' . $tempType['jumlah'] . '</span>';
            }
        endforeach;
        array_push($byType, $temp[$i]);

        // ==========================================
        $temp[$i]['idsto'] = 'RUSAK';
        foreach ($getTypeOnt as $typeOnt) :
            $tempType = $this->dataOnt->jumlahRusak(['type' => $typeOnt['type']])->getRowArray();

            $temp[$i][$typeOnt['type']] = $tempType['jumlah'];
            if ($temp[$i][$typeOnt['type']] != 0) {
                $temp[$i][$typeOnt['type']] = '<span class="text-danger font-weight-bold">' . $tempType['jumlah'] . '</span>';
            }
        endforeach;
        array_push($byType, $temp[$i]);

        // ==========================================
        $temp[$i]['idsto'] = '<strong>Total</strong>';
        foreach ($getTypeOnt as $typeOnt) :
            $temp[$i][$typeOnt['type']] = '<strong>' . $this->dataOnt->totalWhere(['type' => $typeOnt['type']])->total . '</strong>';

        endforeach;
        array_push($byType, $temp[$i]);

        //total samping

        $temp_t = [];
        $byType_t = [];
        $i = 0;
        $getTypeOnt = $this->dataOnt->getGroup('dataont.type')->getResultArray();
        foreach ($this->dataSto->getAll() as $idsto) :
            $temp_t = '<strong>' . $this->dataOnt->totalWhereHorizontal(['idsto' => $idsto->idsto], ['allocation' => 'ASSURANCE'])->total . '</strong>';

            array_push($byType_t, $temp_t);

        endforeach;

        $temp_t = '<strong>' . $this->dataOnt->totalWhereFfOlo(['allocation' => 'FULFILLMENT'])->total . '</strong>';
        array_push($byType_t, $temp_t);

        $temp_t = '<strong>' . $this->dataOnt->totalWhereFfOlo(['allocation' => 'OLO'])->total . '</strong>';
        array_push($byType_t, $temp_t);

        $temp_t = '<strong>' . $this->dataOnt->totalWhereRusak()->total . '</strong>';
        array_push($byType_t, $temp_t);

        $temp_t = '<strong>' . $this->dataOnt->totalWhereAll()->total . '</strong>';
        array_push($byType_t, $temp_t);

        //combine 2 array
        $zipped = array_map(null, $byType, $byType_t);

        $data = [
            'title' => 'Home',
            'pivot' => $zipped,
            'total' => $byType_t,
            'type' => $getTypeOnt,
        ];
        return view('home', $data);
    }

    public function countOrderNow()
    {

        if ($this->request->isAJAX()) {
            $divisi = $this->request->getGet('divisi');

            $ticketNow = $this->dataAsrwan->countTicketNow($divisi);
            $ffNow = $this->dataFfwan->countFfNow($divisi);
            $total = $ticketNow + $ffNow

?>
            <div class="card-stats-items justify-content-center">
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="CLOSED,FINALCHECK,MEDIACARE,RESOLVED,SALAMSIM" data-toggle="modal" data-target="#modalTicketNow">
                        <?= $ticketNow; ?>
                    </div>
                    <div class="card-stats-item-label">Assurance</div>
                </div>
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="CLOSE,REJECT" data-toggle="modal" data-target="#modalFfNow">
                        <?= $ffNow; ?>
                    </div>
                    <div class="card-stats-item-label">Fulfillment</div>
                </div>
            </div>
            <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Orders</h4>
                </div>
                <div class="card-body">
                    <div type="button" class="card-stats-item-count" data-status-asr="CLOSED,FINALCHECK,MEDIACARE,RESOLVED,SALAMSIM" data-status-ff="CLOSE,REJECT" data-toggle="modal" data-target="#modalOrderNow">
                        <?= $total; ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }
    public function countTicket()
    {

        if ($this->request->isAJAX()) {
            $divisi = $this->request->getGet('divisi');
            $bulan = $this->request->getGet('bulan');

            $ticketOpen = $this->dataAsrwan->countTicketOpen($divisi, $bulan);
            $ticketClose = $this->dataAsrwan->countTicketClose($divisi, $bulan);
            $ticketPending = $this->dataAsrwan->countTicketPending($divisi, $bulan);
            $ticket = $this->dataAsrwan->countTicket($divisi, $bulan);
        ?>
            <div class="card-stats-items">
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="BACKEND,NEW,DRAFT,INPROG,QUEUED,WAIT,HISTEDIT" data-toggle="modal" data-target="#modaldetailTicket">
                        <?= $ticketOpen; ?>
                    </div>
                    <div class="card-stats-item-label">Open</div>
                </div>
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="PENDING,PENDINGS,SLAHOLD" data-toggle="modal" data-target="#modaldetailTicket">
                        <?= $ticketPending; ?>
                    </div>
                    <div class="card-stats-item-label">Pending</div>
                </div>
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="CLOSED,FINALCHECK,MEDIACARE,RESOLVED,SALAMSIM" data-toggle="modal" data-target="#modaldetailTicket">
                        <?= $ticketClose; ?>
                    </div>
                    <div class="card-stats-item-label">Closed</div>
                </div>
            </div>
            <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Tickets</h4>
                </div>
                <div class="card-body">
                    <div type="button" class="card-stats-item-count" data-status="" data-toggle="modal" data-target="#modaldetailTicket">
                        <?= $ticket; ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }

    public function countFf()
    {

        if ($this->request->isAJAX()) {
            $divisi = $this->request->getGet('divisi');
            $bulan = $this->request->getGet('bulan');

            $dataOpen = $this->dataFfwan->countFfOpen($divisi, $bulan);
            $dataClose = $this->dataFfwan->countFfClose($divisi, $bulan);
            $dataReject = $this->dataFfwan->countFfReject($divisi, $bulan);
            $data_all = $this->dataFfwan->countFf($divisi, $bulan);
        ?>
            <div class="card-stats-items">
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="OPEN" data-toggle="modal" data-target="#modaldetailFf">
                        <?= $dataOpen; ?>
                    </div>
                    <div class="card-stats-item-label">Open</div>
                </div>
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="REJECT" data-toggle="modal" data-target="#modaldetailFf">
                        <?= $dataReject; ?>
                    </div>
                    <div class="card-stats-item-label">Reject</div>
                </div>
                <div class="card-stats-item">
                    <div type="button" class="card-stats-item-count" data-status="CLOSE" data-toggle="modal" data-target="#modaldetailFf">
                        <?= $dataClose; ?>
                    </div>
                    <div class="card-stats-item-label">Closed</div>
                </div>
            </div>
            <div class="card-icon shadow-primary bg-primary">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Fulfillments</h4>
                </div>
                <div class="card-body">
                    <div type="button" class="card-stats-item-count" data-status="" data-toggle="modal" data-target="#modaldetailFf">
                        <?= $data_all; ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }

    public function chart_data()
    {
        // $results = $this->dataAsrwan->countWeek();

        // foreach ($results as $result) {
        //     $data[] = array(
        //         'day'   => $result['day'],
        //         'total' => $result['total']
        //     );
        // }


        $countWeekWan = $this->dataAsrwan->countWeekWan();
        $countWeekCcan = $this->dataAsrwan->countWeekCcan();
        $countWeekWifi = $this->dataAsrwan->countWeekWifi();

        foreach ($countWeekWan as $result) {
            $data[] = array(
                'day'   => $result['day'],
                'wan' => $result['total']
            );
        }
        foreach ($countWeekCcan as $result) {
            $dataCcan[] = $result['total'];
        }
        foreach ($countWeekWifi as $result) {
            $dataWifi[] = $result['total'];
        }

        $array1 = $data;
        $array2 = $dataCcan;
        $array3 = $dataWifi;

        foreach ($array1 as $i => &$row) {
            $row['ccan'] = $array2[$i];
        }

        foreach ($array1 as $i => &$row) {
            $row['wifi'] = $array3[$i];
        }

        return json_encode($array1);
    }
    public function chart_data_month()
    {

        $countMonthWan = $this->dataAsrwan->countMonthWan();
        $countMonthCcan = $this->dataAsrwan->countMonthCcan();
        $countMonthWifi = $this->dataAsrwan->countMonthWifi();

        foreach ($countMonthWan as $result) {
            $data[] = array(
                'month'   => $result['month'],
                'wan' => $result['total']
            );
        }
        foreach ($countMonthCcan as $result) {
            $dataCcan[] = $result['total'];
        }
        foreach ($countMonthWifi as $result) {
            $dataWifi[] = $result['total'];
        }
        $array1 = $data;
        $array2 = $dataCcan;
        $array3 = $dataWifi;

        foreach ($array1 as $i => &$row) {
            $row['ccan'] = $array2[$i];
        }

        foreach ($array1 as $i => &$row) {
            $row['wifi'] = $array3[$i];
        }

        return json_encode($array1);
    }

    //----------------------DATATABLES----------------------------------------------------


    public function monthTicket()
    {

        if ($this->request->isAJAX()) {
            $divisi = $this->request->getGet('divisi');
            $bulan = $this->request->getGet('bulan');
            $cek_status = $this->request->getGet('status');
            $status = explode(',', $this->request->getGet('status'));

            $db = db_connect();
            $array = array(
                'deleted_at' => null,
                'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => $bulan
            );
            // $status = ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'];
            $builder = $db->table('assurance_wan')
                ->select('idasr, incident, customer_name, summary, owner_group, owner, external_ticketid, customer_segment, service_no, service_type, reported_date, lapul, gaul, ttr_customer, ttr_nasional, ttr_regional, ttr_witel, ttr_mitra, ttr_agent, ttr_pending, pending_reason, status, status_date, resolved_by, workzone, witel, regional, actual_solution, incident_domain, resolved_date, jumlah_site_tsel_nossa, kategori_site_tsel, impacted_site_tsel,divisi')
                ->where($array);
            if ($cek_status != "")
                $builder->whereIn('status', $status);

            if ($divisi != "All") {
                $builder->where('assurance_wan.divisi', $divisi);
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column                
                ->edit('status', function ($dataAsrwan) {
                    if ($dataAsrwan->status == 'BACKEND') {
                        return '<h6><div class="badge badge-danger font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif ($dataAsrwan->status == 'CLOSED') {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif ($dataAsrwan->status == 'PENDING') {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                })
                ->edit('ttr_customer', function ($dataAsrwan) {
                    if ($dataAsrwan->status == 'BACKEND') {
                        $day1 = $dataAsrwan->reported_date;
                        $day1 = strtotime($day1);
                        $day2 = date("Y-m-d H:i:s");
                        $day2 = strtotime($day2);

                        $diffHours = ($day2 - $day1) / 3600;

                        return round($diffHours, 2);
                    }
                    return $dataAsrwan->ttr_customer;
                })
                ->toJson(true);
        }
    }

    public function monthFf()
    {

        if ($this->request->isAJAX()) {
            $divisi = $this->request->getGet('divisi');
            $bulan = $this->request->getGet('bulan');
            $cek_status = $this->request->getGet('status');
            $status = explode(',', $this->request->getGet('status'));

            $db = db_connect();
            $array = array(
                'ffwan.deleted_at' => null,
                'DATE_FORMAT(ffwan.tanggal, "%Y-%m")' => $bulan
            );
            // $status = ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'];
            $builder = $db->table('ffwan')
                ->select('idff, datasto.idsto, ffwan.idsto, tanggal, layanan, no_order, nama, ffwan.alamat, tag_lokasi, order_type, datametro.hostname_metro ,datametro.ip_metro , dataolt.port_metro, ffwan.port_metro, dataolt.hostname_olt, dataolt.ip_olt, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, bandwidth, odc, odp, p_tarikan, status, desc, divisi, evidence')
                ->where($array)
                ->join('datasto',  'datasto.idsto = ffwan.idsto')
                ->join('datametro',  'datametro.hostname_metro = ffwan.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = ffwan.hostname_olt');
            if ($cek_status != "")
                $builder->whereIn('status', $status);

            if ($divisi != "All") {
                $builder->where('ffwan.divisi', $divisi);
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column                
                ->edit('status', function ($dataFfwan) {
                    if ($dataFfwan->status == 'OPEN') {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'CLOSE') {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'REJECT') {
                        return '<h6><div class="badge badge-info font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                })
                ->toJson(true);
        }
    }

    public function ticketNow()
    {

        if ($this->request->isAJAX()) {
            $divisi = $this->request->getGet('divisi');
            $cek_status = $this->request->getGet('status');
            $status = explode(',', $this->request->getGet('status'));

            $db = db_connect();
            $array = array(
                'deleted_at' => null,
            );
            // $status = ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'];
            $builder = $db->table('assurance_wan')
                ->select('idasr, incident, customer_name, summary, owner_group, owner, external_ticketid, customer_segment, service_no, service_type, reported_date, lapul, gaul, ttr_customer, ttr_nasional, ttr_regional, ttr_witel, ttr_mitra, ttr_agent, ttr_pending, pending_reason, status, status_date, resolved_by, workzone, witel, regional, actual_solution, incident_domain, resolved_date, jumlah_site_tsel_nossa, kategori_site_tsel, impacted_site_tsel,divisi')
                ->where($array);
            if ($cek_status != "")
                $builder->whereNotIn('status', $status);

            if ($divisi != "All") {
                $builder->where('assurance_wan.divisi', $divisi);
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column                
                ->edit('status', function ($dataAsrwan) {
                    if ($dataAsrwan->status == 'BACKEND') {
                        return '<h6><div class="badge badge-danger font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif ($dataAsrwan->status == 'CLOSED') {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    } elseif ($dataAsrwan->status == 'PENDING') {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success font-weight-bold">' . $dataAsrwan->status . '</div></h6>';
                })
                ->edit('ttr_customer', function ($dataAsrwan) {
                    if ($dataAsrwan->status == 'BACKEND') {
                        $day1 = $dataAsrwan->reported_date;
                        $day1 = strtotime($day1);
                        $day2 = date("Y-m-d H:i:s");
                        $day2 = strtotime($day2);

                        $diffHours = ($day2 - $day1) / 3600;

                        return round($diffHours, 2);
                    }
                    return $dataAsrwan->ttr_customer;
                })
                ->toJson(true);
        }
    }

    public function ffNow()
    {

        if ($this->request->isAJAX()) {
            $divisi = $this->request->getGet('divisi');
            $cek_status = $this->request->getGet('status');
            $status = explode(',', $this->request->getGet('status'));

            $db = db_connect();
            $array = array(
                'ffwan.deleted_at' => null,
            );
            // $status = ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'];
            $builder = $db->table('ffwan')
                ->select('idff, datasto.idsto, ffwan.idsto, tanggal, layanan, no_order, nama, ffwan.alamat, tag_lokasi, order_type, datametro.hostname_metro ,datametro.ip_metro , dataolt.port_metro, ffwan.port_metro, dataolt.hostname_olt, dataolt.ip_olt, port_onu, hostname_ont, ip_ont, ont_type, serial_number, vlan, bandwidth, odc, odp, p_tarikan, status, desc, divisi, evidence')
                ->where($array)
                ->join('datasto',  'datasto.idsto = ffwan.idsto')
                ->join('datametro',  'datametro.hostname_metro = ffwan.hostname_metro')
                ->join('dataolt',  'dataolt.hostname_olt = ffwan.hostname_olt');
            if ($cek_status != "")
                $builder->whereNotIn('status', $status);

            if ($divisi != "All") {
                $builder->where('ffwan.divisi', $divisi);
            }

            return DataTable::of($builder)
                ->addNumbering('number') //it will return data output with numbering on first column                
                ->edit('status', function ($dataFfwan) {
                    if ($dataFfwan->status == 'OPEN') {
                        return '<h6><div class="badge badge-warning font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'CLOSE') {
                        return '<h6><div class="badge badge-success font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    } elseif ($dataFfwan->status == 'REJECT') {
                        return '<h6><div class="badge badge-info font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                    }
                    return '<h6><div class="badge badge-success font-weight-bold">' . $dataFfwan->status . '</div></h6>';
                })
                ->toJson(true);
        }
    }

    public function OntNodeb()
    {

        if ($this->request->isAJAX()) {
            $sto = $this->request->getGet('sto');
            $allocation = $this->request->getGet('allocation');
            // $divisi = $this->request->getGet('divisi');
            // $cek_status = $this->request->getGet('status');
            // $status = explode(',', $this->request->getGet('status'));

            $db = db_connect();
            if ($sto != "OLO" && $sto != "FULFILLMENT" && $sto != "Total" &&  $sto != "RUSAK") {
                $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');

                $builder = $db->table('dataont')
                    ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return, created_at')
                    ->where($array)
                    ->where('dataont.idsto', $sto)
                    ->whereNotIn('dataont.allocation', ['OLO', 'FULFILLMENT']);

                return DataTable::of($builder)
                    ->addNumbering('number') //it will return data output with numbering on first column              
                    ->toJson(true);
            } elseif ($sto == 'OLO') {
                $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');

                $builder = $db->table('dataont')
                    ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return, created_at')
                    ->where($array)
                    // ->where('dataont.idsto', $sto)
                    ->where('dataont.allocation', 'OLO');

                return DataTable::of($builder)
                    ->addNumbering('number') //it will return data output with numbering on first column              
                    ->toJson(true);
            } elseif ($sto == 'FULFILLMENT') {
                $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'BAIK');

                $builder = $db->table('dataont')
                    ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return, created_at')
                    ->where($array)
                    // ->where('dataont.idsto', $sto)
                    ->where('dataont.allocation', 'FULFILLMENT');

                return DataTable::of($builder)
                    ->addNumbering('number') //it will return data output with numbering on first column              
                    ->toJson(true);
            } elseif ($sto == 'RUSAK') {
                $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.status' => 'RUSAK');

                $builder = $db->table('dataont')
                    ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return, created_at')
                    ->where($array);

                return DataTable::of($builder)
                    ->addNumbering('number') //it will return data output with numbering on first column              
                    ->toJson(true);
            } elseif ($sto == 'Total') {
                $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null);

                $builder = $db->table('dataont')
                    ->select('idont, idsto, merk, type, serial_number, status, desc, installed, received, return, created_at')
                    ->where($array)
                    ->orderBy('status', 'DESC')
                    ->orderBy('idsto', 'ASC');

                return DataTable::of($builder)
                    ->edit('status', function ($dataOnt) {
                        if ($dataOnt->status == 'RUSAK') {
                            return '<span class="text-danger font-weight-bold">' . $dataOnt->status . '</span>';
                        }
                        return $dataOnt->status;
                    })
                    ->addNumbering('number') //it will return data output with numbering on first column              
                    ->toJson(true);
            }
        }
    }
    public function reportAll()
    {

        if ($this->request->isAJAX()) {
        ?>
            <div class="row">
                <div class="table-responsive col-lg-4 col-md-12 col-sm-12">
                    <table class="table table-striped table-hover table-sm table-bordered" style="width: 100%">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="font-size: 0.9rem;" colspan="6">LAPORAN GANGGUAN CCAN WAN &amp; WIFI ID <br><?= time_now(); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>NO</td>
                                <td>JENIS LAYANAN</td>
                                <td>OPEN</td>
                                <td>PENDING</td>
                                <td>CLOSED</td>
                                <td>TOTAL</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>TELKOMSEL</td>
                                <td><?= asrTableToday()['nodeb'][0]; ?></td>
                                <td><?= asrTableToday()['nodeb'][1]; ?></td>
                                <td><?= asrTableToday()['nodeb'][2]; ?></td>
                                <td><?= asrTableToday()['nodeb'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>OLO</td>
                                <td><?= asrTableToday()['olo'][0]; ?></td>
                                <td><?= asrTableToday()['olo'][1]; ?></td>
                                <td><?= asrTableToday()['olo'][2]; ?></td>
                                <td><?= asrTableToday()['olo'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>VPN</td>
                                <td><?= asrTableToday()['vpn'][0]; ?></td>
                                <td><?= asrTableToday()['vpn'][1]; ?></td>
                                <td><?= asrTableToday()['vpn'][2]; ?></td>
                                <td><?= asrTableToday()['vpn'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>ASTINET</td>
                                <td><?= asrTableToday()['astinet'][0]; ?></td>
                                <td><?= asrTableToday()['astinet'][1]; ?></td>
                                <td><?= asrTableToday()['astinet'][2]; ?></td>
                                <td><?= asrTableToday()['astinet'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>METRO-E</td>
                                <td><?= asrTableToday()['metroe'][0]; ?></td>
                                <td><?= asrTableToday()['metroe'][1]; ?></td>
                                <td><?= asrTableToday()['metroe'][2]; ?></td>
                                <td><?= asrTableToday()['metroe'][3]; ?></td>
                            <tr>
                                <td>6</td>
                                <td>INTERNET</td>
                                <td><?= asrTableToday()['internet'][0]; ?></td>
                                <td><?= asrTableToday()['internet'][1]; ?></td>
                                <td><?= asrTableToday()['internet'][2]; ?></td>
                                <td><?= asrTableToday()['internet'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>VOICE</td>
                                <td><?= asrTableToday()['voice'][0]; ?></td>
                                <td><?= asrTableToday()['voice'][1]; ?></td>
                                <td><?= asrTableToday()['voice'][2]; ?></td>
                                <td><?= asrTableToday()['voice'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>IPTV</td>
                                <td><?= asrTableToday()['iptv'][0]; ?></td>
                                <td><?= asrTableToday()['iptv'][1]; ?></td>
                                <td><?= asrTableToday()['iptv'][2]; ?></td>
                                <td><?= asrTableToday()['iptv'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>WIFI</td>
                                <td><?= asrTableToday()['wifi'][0]; ?></td>
                                <td><?= asrTableToday()['wifi'][1]; ?></td>
                                <td><?= asrTableToday()['wifi'][2]; ?></td>
                                <td><?= asrTableToday()['wifi'][3]; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">TOTAL</td>
                                <td><?= asrTableToday()['all'][0]; ?></td>
                                <td><?= asrTableToday()['all'][1]; ?></td>
                                <td><?= asrTableToday()['all'][2]; ?></td>
                                <td><?= asrTableToday()['all'][3]; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive col-lg-4 col-md-12 col-sm-12">
                    <table class="table table-striped table-hover table-sm table-bordered" style="width: 100%">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="font-size: 0.9rem;" colspan="6">LAPORAN GANGGUAN CCAN WAN &amp; WIFI ID <br><?= date("F Y"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>NO</td>
                                <td>JENIS LAYANAN</td>
                                <td>OPEN</td>
                                <td>PENDING</td>
                                <td>CLOSED</td>
                                <td>TOTAL</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>TELKOMSEL</td>
                                <td><?= asrTableMonth()['nodeb'][0]; ?></td>
                                <td><?= asrTableMonth()['nodeb'][1]; ?></td>
                                <td><?= asrTableMonth()['nodeb'][2]; ?></td>
                                <td><?= asrTableMonth()['nodeb'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>OLO</td>
                                <td><?= asrTableMonth()['olo'][0]; ?></td>
                                <td><?= asrTableMonth()['olo'][1]; ?></td>
                                <td><?= asrTableMonth()['olo'][2]; ?></td>
                                <td><?= asrTableMonth()['olo'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>VPN</td>
                                <td><?= asrTableMonth()['vpn'][0]; ?></td>
                                <td><?= asrTableMonth()['vpn'][1]; ?></td>
                                <td><?= asrTableMonth()['vpn'][2]; ?></td>
                                <td><?= asrTableMonth()['vpn'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>ASTINET</td>
                                <td><?= asrTableMonth()['astinet'][0]; ?></td>
                                <td><?= asrTableMonth()['astinet'][1]; ?></td>
                                <td><?= asrTableMonth()['astinet'][2]; ?></td>
                                <td><?= asrTableMonth()['astinet'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>METRO-E</td>
                                <td><?= asrTableMonth()['metroe'][0]; ?></td>
                                <td><?= asrTableMonth()['metroe'][1]; ?></td>
                                <td><?= asrTableMonth()['metroe'][2]; ?></td>
                                <td><?= asrTableMonth()['metroe'][3]; ?></td>
                            <tr>
                                <td>6</td>
                                <td>INTERNET</td>
                                <td><?= asrTableMonth()['internet'][0]; ?></td>
                                <td><?= asrTableMonth()['internet'][1]; ?></td>
                                <td><?= asrTableMonth()['internet'][2]; ?></td>
                                <td><?= asrTableMonth()['internet'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>VOICE</td>
                                <td><?= asrTableMonth()['voice'][0]; ?></td>
                                <td><?= asrTableMonth()['voice'][1]; ?></td>
                                <td><?= asrTableMonth()['voice'][2]; ?></td>
                                <td><?= asrTableMonth()['voice'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>IPTV</td>
                                <td><?= asrTableMonth()['iptv'][0]; ?></td>
                                <td><?= asrTableMonth()['iptv'][1]; ?></td>
                                <td><?= asrTableMonth()['iptv'][2]; ?></td>
                                <td><?= asrTableMonth()['iptv'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>WIFI</td>
                                <td><?= asrTableMonth()['wifi'][0]; ?></td>
                                <td><?= asrTableMonth()['wifi'][1]; ?></td>
                                <td><?= asrTableMonth()['wifi'][2]; ?></td>
                                <td><?= asrTableMonth()['wifi'][3]; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">TOTAL</td>
                                <td><?= asrTableMonth()['all'][0]; ?></td>
                                <td><?= asrTableMonth()['all'][1]; ?></td>
                                <td><?= asrTableMonth()['all'][2]; ?></td>
                                <td><?= asrTableMonth()['all'][3]; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive col-lg-4 col-md-12 col-sm-12">
                    <table class="table table-striped table-hover table-sm table-bordered" style="width: 100%">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="font-size: 0.9rem;" colspan="6">LAPORAN GANGGUAN CCAN WAN &amp; WIFI ID <br>TAHUN <?= date("Y"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>NO</td>
                                <td>JENIS LAYANAN</td>
                                <td>OPEN</td>
                                <td>PENDING</td>
                                <td>CLOSED</td>
                                <td>TOTAL</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>TELKOMSEL</td>
                                <td><?= asrTableYear()['nodeb'][0]; ?></td>
                                <td><?= asrTableYear()['nodeb'][1]; ?></td>
                                <td><?= asrTableYear()['nodeb'][2]; ?></td>
                                <td><?= asrTableYear()['nodeb'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>OLO</td>
                                <td><?= asrTableYear()['olo'][0]; ?></td>
                                <td><?= asrTableYear()['olo'][1]; ?></td>
                                <td><?= asrTableYear()['olo'][2]; ?></td>
                                <td><?= asrTableYear()['olo'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>VPN</td>
                                <td><?= asrTableYear()['vpn'][0]; ?></td>
                                <td><?= asrTableYear()['vpn'][1]; ?></td>
                                <td><?= asrTableYear()['vpn'][2]; ?></td>
                                <td><?= asrTableYear()['vpn'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>ASTINET</td>
                                <td><?= asrTableYear()['astinet'][0]; ?></td>
                                <td><?= asrTableYear()['astinet'][1]; ?></td>
                                <td><?= asrTableYear()['astinet'][2]; ?></td>
                                <td><?= asrTableYear()['astinet'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>METRO-E</td>
                                <td><?= asrTableYear()['metroe'][0]; ?></td>
                                <td><?= asrTableYear()['metroe'][1]; ?></td>
                                <td><?= asrTableYear()['metroe'][2]; ?></td>
                                <td><?= asrTableYear()['metroe'][3]; ?></td>
                            <tr>
                                <td>6</td>
                                <td>INTERNET</td>
                                <td><?= asrTableYear()['internet'][0]; ?></td>
                                <td><?= asrTableYear()['internet'][1]; ?></td>
                                <td><?= asrTableYear()['internet'][2]; ?></td>
                                <td><?= asrTableYear()['internet'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>VOICE</td>
                                <td><?= asrTableYear()['voice'][0]; ?></td>
                                <td><?= asrTableYear()['voice'][1]; ?></td>
                                <td><?= asrTableYear()['voice'][2]; ?></td>
                                <td><?= asrTableYear()['voice'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>IPTV</td>
                                <td><?= asrTableYear()['iptv'][0]; ?></td>
                                <td><?= asrTableYear()['iptv'][1]; ?></td>
                                <td><?= asrTableYear()['iptv'][2]; ?></td>
                                <td><?= asrTableYear()['iptv'][3]; ?></td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>WIFI</td>
                                <td><?= asrTableYear()['wifi'][0]; ?></td>
                                <td><?= asrTableYear()['wifi'][1]; ?></td>
                                <td><?= asrTableYear()['wifi'][2]; ?></td>
                                <td><?= asrTableYear()['wifi'][3]; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">TOTAL</td>
                                <td><?= asrTableYear()['all'][0]; ?></td>
                                <td><?= asrTableYear()['all'][1]; ?></td>
                                <td><?= asrTableYear()['all'][2]; ?></td>
                                <td><?= asrTableYear()['all'][3]; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
<?php
        }
    }
}
