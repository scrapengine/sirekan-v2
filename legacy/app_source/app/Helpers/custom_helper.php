<?php

function countData($table)
{
    $db = \Config\Database::connect();
    return $db->table($table)->countAllResults();
}

function countDataa($table)
{
    $db = \Config\Database::connect();
    return $db->table($table)->where('deleted_at', null)->countAllResults();
}

function time_now()
{
    // Change the line below to your timezone!
    date_default_timezone_set('Asia/Jakarta');
    $date = date('d-m-Y H:i:s', time());
    return $date;
}


//Menghitung total ONT
function countOntAsr()
{
    $db = \Config\Database::connect();
    $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation =' => 'ASSURANCE');
    $builder = $db->table('dataont')
        ->select('idont, idsto, type, serial_number, status, desc, allocation, received, return')
        ->where($array);
    return $builder->countAllResults();
}

function countOntFf()
{
    $db = \Config\Database::connect();
    $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation =' => 'FULFILLMENT');
    $builder = $db->table('dataont')
        ->select('idont, idsto, type, serial_number, status, desc, allocation, received, return')
        ->where($array);
    return $builder->countAllResults();
}

function countOntOlo()
{
    $db = \Config\Database::connect();
    $array = array('dataont.deleted_at' => null, 'dataont.installed' => null, 'dataont.return' => null, 'dataont.allocation' => "OLO");
    $builder = $db->table('dataont')
        ->select('idont, idsto, type, serial_number, status, desc, allocation, received, return')
        ->where($array);
    return $builder->countAllResults();
}

//============================================================================
// function countTicketNodeb()
// {
//     $db = \Config\Database::connect();
//     $array = array('deleted_at' => null, 'assurance_wan.divisi' => 'WAN', 'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => '2022-10');
//     $builder = $db->table('assurance_wan')->select('*')->where($array);
//     return $builder->countAllResults();
// }

// function countTicketOpen()
// {
//     $db = \Config\Database::connect();
//     $status = ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'];
//     $array = array(
//         'deleted_at' => null,
//         'assurance_wan.divisi' => 'WAN',
//         'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => '2022-10',
//     );
//     $builder = $db->table('assurance_wan')->select('*')->where($array)->whereIn('status', $status);
//     return $builder->countAllResults();
// }

// function countTicketClose()
// {
//     $db = \Config\Database::connect();
//     $status = ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'];
//     $array = array(
//         'deleted_at' => null,
//         'assurance_wan.divisi' => 'WAN',
//         'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => '2022-10',
//     );
//     $builder = $db->table('assurance_wan')->select('*')->where($array)->whereIn('status', $status);
//     return $builder->countAllResults();
// }

// function countTicketPending()
// {
//     $db = \Config\Database::connect();
//     $status = ['PENDING', 'PENDINGS', 'SLAHOLD'];
//     $array = array(
//         'deleted_at' => null,
//         'assurance_wan.divisi' => 'WAN',
//         'DATE_FORMAT(assurance_wan.reported_date, "%Y-%m")' => '2022-10',
//     );
//     $builder = $db->table('assurance_wan')->select('*')->where($array)->whereIn('status', $status);
//     return $builder->countAllResults();
// }

//==========================================================================================

function asrWeek()
{
    $db = \Config\Database::connect();
    $array = array(
        'deleted_at' => null,
    );

    $date_start = strtotime('-6 day');
    $week_start = date('Y-m-d', $date_start);
    $date_end = strtotime('+1 day');
    $week_end = date('Y-m-d', $date_end);

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN');

    $wan = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN');

    $ccan = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI');

    $wifi = $builder->countAllResults();

    return array($wan, $ccan, $wifi);
}

function asrMonth()
{
    $db = \Config\Database::connect();
    $array = array(
        'deleted_at' => null,
    );

    $date_start = strtotime('first day of january this year');
    $week_start = date('Y-m-d', $date_start);
    $date_end = strtotime('last day of december this year');
    $week_end = date('Y-m-d', $date_end);

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN');

    $wan = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN');

    $ccan = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI');

    $wifi = $builder->countAllResults();

    return array($wan, $ccan, $wifi);
}

function tableMonth($divisi)
{
    $db = \Config\Database::connect();
    $array = array(
        'deleted_at' => null,
    );

    $date_start = strtotime('first day of this month');
    $week_start = date('Y-m-d', $date_start);
    $date_end = strtotime('last day of this month');
    $week_end = date('Y-m-d', $date_end);

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', $divisi)
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    return $builder;
}

function asrTableMonth()
{
    $db = \Config\Database::connect();
    $array = array(
        'deleted_at' => null,
    );

    $date_start = strtotime('first day of this month');
    $week_start = date('Y-m-d', $date_start);
    $date_end = strtotime('last day of this month');
    $week_end = date('Y-m-d', $date_end);

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_all = $builder->countAllResults();

    $nodeb = array($nodeb_open, $nodeb_pending, $nodeb_close, $nodeb_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_all = $builder->countAllResults();

    $olo = array($olo_open, $olo_pending, $olo_close, $olo_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $vpn_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $vpn_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $vpn_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN');

    $vpn_all = $builder->countAllResults();

    $vpn = array($vpn_open, $vpn_pending, $vpn_close, $vpn_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $astinet_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $astinet_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $astinet_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET');

    $astinet_all = $builder->countAllResults();

    $astinet = array($astinet_open, $astinet_pending, $astinet_close, $astinet_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $metroe_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $metroe_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $metroe_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET');

    $metroe_all = $builder->countAllResults();

    $metroe = array($metroe_open, $metroe_pending, $metroe_close, $metroe_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $internet_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $internet_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $internet_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET');

    $internet_all = $builder->countAllResults();

    $internet = array($internet_open, $internet_pending, $internet_close, $internet_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $voice_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $voice_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $voice_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE');

    $voice_all = $builder->countAllResults();

    $voice = array($voice_open, $voice_pending, $voice_close, $voice_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $iptv_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $iptv_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $iptv_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV');

    $iptv_all = $builder->countAllResults();

    $iptv = array($iptv_open, $iptv_pending, $iptv_close, $iptv_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $wifi_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $wifi_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $wifi_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI');

    $wifi_all = $builder->countAllResults();

    $wifi = array($wifi_open, $wifi_pending, $wifi_close, $wifi_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $all_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $all_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $all_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array);

    $all_all = $builder->countAllResults();

    $all = array($all_open, $all_pending, $all_close, $all_all);

    $data = array(
        'nodeb' => $nodeb,
        'olo' => $olo,
        'vpn' => $vpn,
        'astinet' => $astinet,
        'metroe' => $metroe,
        'internet' => $internet,
        'voice' => $voice,
        'iptv' => $iptv,
        'wifi' => $wifi,
        'all' => $all,
    );

    return $data;
}

function asrTableYear()
{
    $db = \Config\Database::connect();
    $array = array(
        'deleted_at' => null,
    );

    $date_start = strtotime('first day of january this year');
    $week_start = date('Y-m-d', $date_start);
    $date_end = strtotime('first day of december this year');
    $week_end = date('Y-m-d', $date_end);

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_all = $builder->countAllResults();

    $nodeb = array($nodeb_open, $nodeb_pending, $nodeb_close, $nodeb_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WAN')
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_all = $builder->countAllResults();

    $olo = array($olo_open, $olo_pending, $olo_close, $olo_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $vpn_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $vpn_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $vpn_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN');

    $vpn_all = $builder->countAllResults();

    $vpn = array($vpn_open, $vpn_pending, $vpn_close, $vpn_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $astinet_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $astinet_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $astinet_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET');

    $astinet_all = $builder->countAllResults();

    $astinet = array($astinet_open, $astinet_pending, $astinet_close, $astinet_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $metroe_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $metroe_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $metroe_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET');

    $metroe_all = $builder->countAllResults();

    $metroe = array($metroe_open, $metroe_pending, $metroe_close, $metroe_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $internet_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $internet_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $internet_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET');

    $internet_all = $builder->countAllResults();

    $internet = array($internet_open, $internet_pending, $internet_close, $internet_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $voice_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $voice_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $voice_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE');

    $voice_all = $builder->countAllResults();

    $voice = array($voice_open, $voice_pending, $voice_close, $voice_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $iptv_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $iptv_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $iptv_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV');

    $iptv_all = $builder->countAllResults();

    $iptv = array($iptv_open, $iptv_pending, $iptv_close, $iptv_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $wifi_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $wifi_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $wifi_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->where('divisi', 'WIFI');

    $wifi_all = $builder->countAllResults();

    $wifi = array($wifi_open, $wifi_pending, $wifi_close, $wifi_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $all_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $all_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array)
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $all_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where('reported_date <=', $week_end)
        ->where($array);

    $all_all = $builder->countAllResults();

    $all = array($all_open, $all_pending, $all_close, $all_all);

    $data = array(
        'nodeb' => $nodeb,
        'olo' => $olo,
        'vpn' => $vpn,
        'astinet' => $astinet,
        'metroe' => $metroe,
        'internet' => $internet,
        'voice' => $voice,
        'iptv' => $iptv,
        'wifi' => $wifi,
        'all' => $all,
    );

    return $data;
}

function asrTableToday()
{
    $db = \Config\Database::connect();
    $array = array(
        'deleted_at' => null,
    );

    $date_start = strtotime('today');
    $week_start = date('Y-m-d', $date_start);
    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'])
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->groupStart()
            ->where('customer_name', 'TELEKOMUNIKASI SELULAR')
            ->orLike('summary', 'TSEL')
        ->groupEnd();

    $nodeb_all = $builder->countAllResults();

    $nodeb = array($nodeb_open, $nodeb_pending, $nodeb_close, $nodeb_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM'])
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WAN')
        ->where('customer_name !=', 'TELEKOMUNIKASI SELULAR')
        ->notLike('summary', 'TSEL');

    $olo_all = $builder->countAllResults();

    $olo = array($olo_open, $olo_pending, $olo_close, $olo_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $vpn_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $vpn_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $vpn_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_IPVPN');

    $vpn_all = $builder->countAllResults();

    $vpn = array($vpn_open, $vpn_pending, $vpn_close, $vpn_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $astinet_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $astinet_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $astinet_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_ASTINET');

    $astinet_all = $builder->countAllResults();

    $astinet = array($astinet_open, $astinet_pending, $astinet_close, $astinet_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $metroe_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $metroe_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $metroe_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'MM_METRO_ETHERNET');

    $metroe_all = $builder->countAllResults();

    $metroe = array($metroe_open, $metroe_pending, $metroe_close, $metroe_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $internet_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $internet_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $internet_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'INTERNET');

    $internet_all = $builder->countAllResults();

    $internet = array($internet_open, $internet_pending, $internet_close, $internet_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $voice_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $voice_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $voice_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'VOICE');

    $voice_all = $builder->countAllResults();

    $voice = array($voice_open, $voice_pending, $voice_close, $voice_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $iptv_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $iptv_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $iptv_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'CCAN')
        ->where('service_type', 'IPTV');

    $iptv_all = $builder->countAllResults();

    $iptv = array($iptv_open, $iptv_pending, $iptv_close, $iptv_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $wifi_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $wifi_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WIFI')
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $wifi_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->where('divisi', 'WIFI');

    $wifi_all = $builder->countAllResults();

    $wifi = array($wifi_open, $wifi_pending, $wifi_close, $wifi_all);

    //================================================================================

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->whereIn('status', ['BACKEND', 'NEW', 'DRAFT', 'INPROG', 'QUEUED', 'WAIT', 'HISTEDIT']);

    $all_open = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->whereIn('status', ['PENDING', 'PENDINGS', 'SLAHOLD']);

    $all_pending = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array)
        ->whereIn('status', ['CLOSED', 'FINALCHECK', 'MEDIACARE', 'RESOLVED', 'SALAMSIM']);

    $all_close = $builder->countAllResults();

    $builder = $db->table('assurance_wan')
        ->select('*, COUNT(*) AS total')
        ->where('reported_date >=', $week_start)
        ->where($array);

    $all_all = $builder->countAllResults();

    $all = array($all_open, $all_pending, $all_close, $all_all);

    $data = array(
        'nodeb' => $nodeb,
        'olo' => $olo,
        'vpn' => $vpn,
        'astinet' => $astinet,
        'metroe' => $metroe,
        'internet' => $internet,
        'voice' => $voice,
        'iptv' => $iptv,
        'wifi' => $wifi,
        'all' => $all,
    );

    return $data;
}
