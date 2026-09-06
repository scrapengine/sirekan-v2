<?php

namespace App\Models;

use CodeIgniter\Commands\Utilities\Publish;
use CodeIgniter\Database\MySQLi\Builder;
use CodeIgniter\Database\Query;
use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

use function GuzzleHttp\Promise\queue;

class AsrwanModel extends Model
{
    protected $table          = 'assurance_wan';
    protected $primaryKey     = 'idasr';
    protected $returnType     = 'object';
    protected $allowedFields  = ['incident', 'customer_name', 'summary', 'owner_group', 'owner', 'external_ticketid', 'customer_segment', 'service_no', 'service_type', 'reported_date', 'lapul', 'gaul', 'ttr_end_to_end','ttr_customer', 'ttr_nasional', 'ttr_regional', 'ttr_witel', 'ttr_mitra', 'ttr_agent', 'ttr_pending', 'pending_reason', 'status', 'status_date', 'resolved_by', 'workzone', 'witel', 'regional', 'actual_solution', 'incident_domain', 'resolved_date', 'jumlah_site_tsel_nossa', 'kategori_site_tsel', 'impacted_site_tsel','cause','resolution', 'divisi', 'rca', 'evidence'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    // ...

    function getAll($keyword = null)
    {
        $builder = $this->db->table('datanodeb');
        $builder->select('*')->where('datanodeb.deleted_at', null);
        $builder->select('datanodeb.port_metro as port_metro_nodeb');
        $builder->join('datasto',  'datasto.idsto = datanodeb.idsto');
        $builder->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro');
        $builder->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

        if ($keyword != '') {
            $builder->like('datasto.idsto', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('site_id', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('site_name', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('datametro.hostname_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('datametro.ip_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('datanodeb.port_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('dataolt.port_metro', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('dataolt.hostname_olt', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('dataolt.ip_olt', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('port_onu', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('hostname_ont', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('ip_ont', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('ont_type', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('serial_number', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('odc', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('odp', $keyword)->where('datanodeb.deleted_at', null);
            $builder->orLike('tikor_site', $keyword)->where('datanodeb.deleted_at', null);
        };

        $query   = $builder->get();
        return $query->getResult();
    }
    function getTrash($keyword = null)
    {
        $builder = $this->db->table('datanodeb');
        $builder->select('*')->where('datanodeb.deleted_at !=', null);
        $builder->join('datasto',  'datasto.idsto = datanodeb.idsto');
        $builder->join('datametro',  'datametro.hostname_metro = datanodeb.hostname_metro');
        $builder->join('dataolt',  'dataolt.hostname_olt = datanodeb.hostname_olt');

        if ($keyword != '') {
            $builder->like('idsto', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('site_id', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('site_name', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_metro', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('datametro.ip_metro', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_metro', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_olt', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('dataolt.ip_olt', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('port_onu', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('hostname_ont', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ip_ont', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('ont_type', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('serial_number', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('odc', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('odp', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
            $builder->orLike('tikor_site', $keyword)->where('datanodeb.deleted_at IS NOT NULL', null, false);
        };

        $query   = $builder->get();
        return $query->getResult();
    }

    function GetById($id)
    {
        $builder = $this->db->table('assurance_wan')
            ->select('*')
            ->where('idasr', $id);

        $query   = $builder->get();
        return $query->getResultArray();
    }
    function countTicketNow($divisi)
    {
        $db = \Config\Database::connect();
        $status = ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'];
        $array = array(
            'deleted_at' => null,
        );
        $builder = $db->table('assurance_wan')->select('*')->where($array)->whereNotIn('status', $status);
        if ($divisi != "All") {
            $builder->where('assurance_wan.divisi', $divisi);
        }
        return $builder->countAllResults();
    }

    function GetServiceNo($id)
    {
        $builder = $this->db->table('assurance_wan')
            ->select('*')
            ->like('service_no', $id);

        $query   = $builder->get();
        return $query->getResult();
    }

    function countTicket($divisi, $bulan)
    {

        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => $bulan
        );
        $builder = $db->table('assurance_wan')->select('*')->where($array);
        if ($divisi != "All") {
            $builder->where('assurance_wan.divisi', $divisi);
        }

        return $builder->countAllResults();
    }

    function countAllTicket()
    {
        $db = \Config\Database::connect();
        $array = array('deleted_at' => null, 'assurance_wan.divisi' => 'WAN', 'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => '2022-10');
        $builder = $db->table('assurance_wan')->select('*')->where($array);
        return $builder->countAllResults();
    }

    function countTicketOpen($divisi, $bulan)
    {
        $db = \Config\Database::connect();
        $status = ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'];
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => $bulan,
        );
        $builder = $db->table('assurance_wan')->select('*')->where($array)->whereIn('status', $status);
        if ($divisi != "All") {
            $builder->where('assurance_wan.divisi', $divisi);
        }
        return $builder->countAllResults();
    }

    function countTicketClose($divisi, $bulan)
    {
        $db = \Config\Database::connect();
        $status = ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'];
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => $bulan,
        );
        $builder = $db->table('assurance_wan')->select('*')->where($array)->whereIn('status', $status);
        if ($divisi != "All") {
            $builder->where('assurance_wan.divisi', $divisi);
        }
        return $builder->countAllResults();
    }

    function countTicketPending($divisi, $bulan)
    {
        $db = \Config\Database::connect();
        $status = ['PENDING', 'PENDINGS', 'SLAHOLD'];
        $array = array(
            'deleted_at' => null,
            'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => $bulan,
        );
        $builder = $db->table('assurance_wan')->select('*')->where($array)->whereIn('status', $status);
        if ($divisi != "All") {
            $builder->where('assurance_wan.divisi', $divisi);
        }
        return $builder->countAllResults();
    }

    function countWeek()
    {

        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
        );

        $date_start = strtotime('-6 day');
        $week_start = date('Y-m-d', $date_start);
        $date_end = strtotime('+1 day');
        $week_end = date('Y-m-d', $date_end);

        $data = array();

        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total')
            ->where('reported_date >=', $week_start)
            ->where('reported_date <=', $week_end)
            ->where($array)
            ->groupBy('DATE(reported_date)');
        $query = $builder->get();

        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', $date_start + ($i * 86400));
            $data[date('w', strtotime($date))] = array(
                'day'   => date('D', strtotime($date)),
                'total' => 0,
            );
        }
        foreach ($query->getResultArray() as $result) {
            $data[date('w', strtotime($result['reported_date']))] = array(
                'day'   => date('D', strtotime($result['reported_date'])),
                'total' => $result['total'],
            );
        }

        return $data;
    }
    function countWeekWan()
    {

        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'divisi' => 'WAN',
        );

        $date_start = strtotime('-6 day');
        $week_start = date('Y-m-d', $date_start);
        $date_end = strtotime('+1 day');
        $week_end = date('Y-m-d', $date_end);

        $data = array();

        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total')
            ->where('reported_date >=', $week_start)
            ->where('reported_date <=', $week_end)
            ->where($array)
            ->groupBy('DATE(reported_date)');
        $query = $builder->get();

        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', $date_start + ($i * 86400));
            $data[date('w', strtotime($date))] = array(
                'day'   => date('l', strtotime($date)),
                'total' => 0,
            );
        }
        foreach ($query->getResultArray() as $result) {
            $data[date('w', strtotime($result['reported_date']))] = array(
                'day'   => date('l', strtotime($result['reported_date'])),
                'total' => $result['total'],
            );
        }

        return $data;
    }
    function countWeekCcan()
    {

        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'divisi' => 'CCAN',
        );

        $date_start = strtotime('-6 day');
        $week_start = date('Y-m-d', $date_start);
        $date_end = strtotime('+1 day');
        $week_end = date('Y-m-d', $date_end);

        $data = array();

        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total')
            ->where('reported_date >=', $week_start)
            ->where('reported_date <=', $week_end)
            ->where($array)
            ->groupBy('DATE(reported_date)');
        $query = $builder->get();

        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', $date_start + ($i * 86400));
            $data[date('w', strtotime($date))] = array(
                'day'   => date('D', strtotime($date)),
                'total' => 0,
            );
        }
        foreach ($query->getResultArray() as $result) {
            $data[date('w', strtotime($result['reported_date']))] = array(
                'day'   => date('D', strtotime($result['reported_date'])),
                'total' => $result['total'],
            );
        }

        return $data;
    }
    function countWeekWifi()
    {

        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'divisi' => 'WIFI',
        );

        $date_start = strtotime('-6 day');
        $week_start = date('Y-m-d', $date_start);
        $date_end = strtotime('+1 day');
        $week_end = date('Y-m-d', $date_end);

        $data = array();

        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total')
            ->where('reported_date >=', $week_start)
            ->where('reported_date <=', $week_end)
            ->where($array)
            ->groupBy('DATE(reported_date)');
        $query = $builder->get();

        // for ($i = 0; $i < 7; $i++) {
        //     $date = date('Y-m-d', $date_start + ($i * 86400));
        //     $data[date('w', strtotime($date))] = array(
        //         'day'   => date('D', strtotime($date)),
        //         'total' => 0
        //     );
        // }

        // foreach ($query->getResultArray() as $result) {
        //     $data[date('w', strtotime($result['reported_date']))] = array(
        //         'day'   => date('D', strtotime($result['reported_date'])),
        //         'total' => $result['total']
        //     );
        // }
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', $date_start + ($i * 86400));
            $data[date('w', strtotime($date))] = array(
                'day'   => date('D', strtotime($date)),
                'total' => 0,
            );
        }
        foreach ($query->getResultArray() as $result) {
            $data[date('w', strtotime($result['reported_date']))] = array(
                'day'   => date('D', strtotime($result['reported_date'])),
                'total' => $result['total'],
            );
        }

        return $data;
    }

    public function getUserTotalByWeek()
    {
        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
        );
        $date_start = strtotime('-6 days');
        $user_data = array();

        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total')
            ->where('DATE(reported_date) >=', $date_start)
            ->where($array)
            ->groupBy('DAYNAME(reported_date)');
        $query = $builder->get();
    }

    public function countMonthWan()
    {
        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'divisi' => 'WAN',
        );

        // $data = array();
        $date = date('n');
        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total, MONTH(reported_date) as month')
            ->where('YEAR(reported_date) =', date("Y"))
            ->where($array)
            ->groupBy('MONTH(reported_date)');
        $query = $builder->get()->getResultArray();

        for ($i = 1; $i < $date + 1; $i++) {
            $data[date('F', mktime(0, 0, 0, $i, 1))] = array(
                'month'   => date('F', mktime(0, 0, 0, $i, 1)),
                'total' => 0,
            );
        }
        foreach ($query as $result) {
            $data[date('F', mktime(0, 0, 0, $result['month'], 1))] = array(
                'month'   => date('F', mktime(0, 0, 0, $result['month'], 1)),
                'total' => $result['total'],
            );
        }

        return $data;
    }
    public function countMonthCcan()
    {
        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'divisi' => 'CCAN',
        );

        // $data = array();
        $date = date('n');
        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total, MONTH(reported_date) as month')
            ->where('YEAR(reported_date) =', date("Y"))
            ->where($array)
            ->groupBy('MONTH(reported_date)');
        $query = $builder->get()->getResultArray();

        for ($i = 1; $i < $date + 1; $i++) {
            $data[date('F', mktime(0, 0, 0, $i, 1))] = array(
                'month'   => date('F', mktime(0, 0, 0, $i, 1)),
                'total' => 0,
            );
        }
        foreach ($query as $result) {
            $data[date('F', mktime(0, 0, 0, $result['month'], 1))] = array(
                'month'   => date('F', mktime(0, 0, 0, $result['month'], 1)),
                'total' => $result['total'],
            );
        }

        return $data;
    }
    public function countMonthWifi()
    {
        $db = \Config\Database::connect();
        $array = array(
            'deleted_at' => null,
            'divisi' => 'WIFI',
        );

        // $data = array();
        $date = date('n');
        $builder = $db->table('assurance_wan')
            ->select('*, COUNT(*) AS total, MONTH(reported_date) as month')
            ->where('YEAR(reported_date) =', date("Y"))
            ->where($array)
            ->groupBy('MONTH(reported_date)');
        $query = $builder->get()->getResultArray();

        for ($i = 1; $i < $date + 1; $i++) {
            $data[date('F', mktime(0, 0, 0, $i, 1))] = array(
                'month'   => date('F', mktime(0, 0, 0, $i, 1)),
                'total' => 0,
            );
        }
        foreach ($query as $result) {
            $data[date('F', mktime(0, 0, 0, $result['month'], 1))] = array(
                'month'   => date('F', mktime(0, 0, 0, $result['month'], 1)),
                'total' => $result['total'],
            );
        }

        return $data;
    }
}
