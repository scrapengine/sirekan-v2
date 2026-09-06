<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>



<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">Work Order Now
                            <div class="dropdown d-inline float-right">
                                <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-divisi">All</a>
                                <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title">Select Divisi</li>
                                    <li><a type="button" class="dropdown-item toggle-order-divisi" data-divisi="All">All</a></li>
                                    <li><a type="button" class="dropdown-item toggle-order-divisi" data-divisi="CCAN">CCAN</a></li>
                                    <li><a type="button" class="dropdown-item toggle-order-divisi" data-divisi="WAN">WAN</a></li>
                                    <li><a type="button" class="dropdown-item toggle-order-divisi" data-divisi="WIFI">WIFI</a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="countOrderNow">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">Assurance -
                            <div class="dropdown d-inline">
                                <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month"><?= date('F') ?></a>
                                <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title">Select Month</li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="January" data-bulan="<?= date('Y'); ?>-01">January</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="February" data-bulan="<?= date('Y'); ?>-02">February</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="March" data-bulan="<?= date('Y'); ?>-03">March</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="April" data-bulan="<?= date('Y'); ?>-04">April</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="May" data-bulan="<?= date('Y'); ?>-05">May</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="June" data-bulan="<?= date('Y'); ?>-06">June</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="July" data-bulan="<?= date('Y'); ?>-07">July</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="August" data-bulan="<?= date('Y'); ?>-08">August</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="September" data-bulan="<?= date('Y'); ?>-09">September</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="October" data-bulan="<?= date('Y'); ?>-10">October</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="November" data-bulan="<?= date('Y'); ?>-11">November</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket" data-divisi="All" data-judul="December" data-bulan="<?= date('Y'); ?>-12">December</a></li>
                                </ul>
                            </div>
                            <div class="dropdown d-inline float-right">
                                <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month-divisi">All</a>
                                <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title">Select Divisi</li>
                                    <li><a type="button" class="dropdown-item toggle-ticket-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="All">All</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="CCAN">CCAN</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="WAN">WAN</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ticket-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="WIFI">WIFI</a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="countWan">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">Fulfillment -
                            <div class="dropdown d-inline">
                                <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month-ff"><?= date('F') ?></a>
                                <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title">Select Month</li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="January" data-bulan="<?= date('Y'); ?>-01">January</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="February" data-bulan="<?= date('Y'); ?>-02">February</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="March" data-bulan="<?= date('Y'); ?>-03">March</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="April" data-bulan="<?= date('Y'); ?>-04">April</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="May" data-bulan="<?= date('Y'); ?>-05">May</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="June" data-bulan="<?= date('Y'); ?>-06">June</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="July" data-bulan="<?= date('Y'); ?>-07">July</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="August" data-bulan="<?= date('Y'); ?>-08">August</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="September" data-bulan="<?= date('Y'); ?>-09">September</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="October" data-bulan="<?= date('Y'); ?>-10">October</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="November" data-bulan="<?= date('Y'); ?>-11">November</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff" data-divisi="All" data-judul="December" data-bulan="<?= date('Y'); ?>-12">December</a></li>
                                </ul>
                            </div>
                            <div class="dropdown d-inline float-right">
                                <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month-ff-divisi">All</a>
                                <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title">Select Divisi</li>
                                    <li><a type="button" class="dropdown-item toggle-ff-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="All">All</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="CCAN">CCAN</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="WAN">WAN</a></li>
                                    <li><a type="button" class="dropdown-item toggle-ff-divisi" data-bulan="<?= date('Y-m'); ?>" data-divisi="WIFI">WIFI</a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="countWanff">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <blockquote class="blockquote bg-transparent">
                <p class="mb-0" id="quote"></p>
                <footer class="blockquote-footer" id="author"></footer>
            </blockquote>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Report</h4>
                <div class="card-header-action">
                    <ul class="nav nav-pills justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="report-gangguan-tab" data-toggle="pill" data-target="#report-gangguan" type="button" role="tab" aria-controls="report-gangguan" aria-selected="false">Assurance
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="report-ont-tab" data-toggle="pill" data-target="#report-ont" type="button" role="tab" aria-controls="report-ont" aria-selected="false">ONT NODE-B
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="report-gangguan" role="tabpanel" aria-labelledby="report-gangguan-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-body">
                                <div id="reportall"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="report-ont" role="tabpanel" aria-labelledby="report-ont-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-borderless table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>LOKASI</th>
                                                <?php foreach ($type as $d) : ?>
                                                    <th><?= $d['type']; ?></th>
                                                <?php endforeach ?>
                                            </tr>
                                        <tbody>
                                            <?php foreach ($pivot as $p) : ?>
                                                <tr>
                                                    <td><?= $p[0]['idsto']; ?> <span class="badge badge-light" type="button" data-sto="<?= $p[0]['idsto']; ?>" data-toggle="modal" data-target="#modalOnt"><?= $p[1]; ?></span></td>
                                                    <?php foreach ($type as $d) : ?>
                                                        <td><?= $p[0][$d['type']]; ?></td>
                                                    <?php endforeach ?>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Assurance</h4>
                <div class="card-header-action">
                    <ul class="nav nav-pills justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="dashboard-week-tab" data-toggle="pill" data-target="#dashboard-week" type="button" role="tab" aria-controls="dashboard-week" aria-selected="false">Week
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="dashboard-month-tab" data-toggle="pill" data-target="#dashboard-month" type="button" role="tab" aria-controls="dashboard-month" aria-selected="false">Month
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="dashboard-week" role="tabpanel" aria-labelledby="dashboard-week-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-body">
                                <canvas id="line" height="100"></canvas>
                                <div class="statistic-details mt-sm-4">
                                    <div class="statistic-details-item">
                                        <span class="text-muted">WAN</span>
                                        <div class="detail-value"><?= asrWeek()[0]; ?></div>
                                        <div class="detail-name">Tickets</div>
                                    </div>
                                    <div class="statistic-details-item">
                                        <span class="text-muted">CCAN</span>
                                        <div class="detail-value"><?= asrWeek()[1]; ?></div>
                                        <div class="detail-name">Tickets</div>
                                    </div>
                                    <div class="statistic-details-item">
                                        <span class="text-muted">WIFI</span>
                                        <div class="detail-value"><?= asrWeek()[2]; ?></div>
                                        <div class="detail-name">Tickets</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="dashboard-month" role="tabpanel" aria-labelledby="dashboard-month-tab">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-body">
                                <canvas id="bar_month" height="100"></canvas>
                                <div class="statistic-details mt-sm-4">
                                    <div class="statistic-details-item">
                                        <span class="text-muted">WAN</span>
                                        <div class="detail-value"><?= asrMonth()[0]; ?></div>
                                        <div class="detail-name">Tickets</div>
                                    </div>
                                    <div class="statistic-details-item">
                                        <span class="text-muted">CCAN</span>
                                        <div class="detail-value"><?= asrMonth()[1]; ?></div>
                                        <div class="detail-name">Tickets</div>
                                    </div>
                                    <div class="statistic-details-item">
                                        <span class="text-muted">WIFI</span>
                                        <div class="detail-value"><?= asrMonth()[2]; ?></div>
                                        <div class="detail-name">Tickets</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="row mt-2" hidden>
            <div class="col-6">
                <canvas id="bar"></canvas>
            </div>
            <div class="col-6">
                <canvas id="pie"></canvas>
            </div>
        </div> -->
        <div class="row" hidden>
            <div class="col-12">
                <div class="card card-chart">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6 text-left">
                                <h5 class="card-category">Total Shipments</h5>
                                <h2 class="card-title">Performance</h2>
                            </div>
                            <div class="col-sm-6">
                                <div class="btn-group btn-group-toggle float-right" data-toggle="buttons">
                                    <label class="btn btn-sm btn-primary btn-simple active" id="0">
                                        <input type="radio" name="options" checked="">
                                        <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Accounts</span>
                                        <span class="d-block d-sm-none">
                                            <i class="tim-icons icon-single-02"></i>
                                        </span>
                                    </label>
                                    <label class="btn btn-sm btn-primary btn-simple" id="1">
                                        <input type="radio" class="d-none d-sm-none" name="options">
                                        <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Purchases</span>
                                        <span class="d-block d-sm-none">
                                            <i class="tim-icons icon-gift-2"></i>
                                        </span>
                                    </label>
                                    <label class="btn btn-sm btn-primary btn-simple" id="2">
                                        <input type="radio" class="d-none" name="options">
                                        <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Sessions</span>
                                        <span class="d-block d-sm-none">
                                            <i class="tim-icons icon-tap-02"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <div style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;" class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                </div>
                            </div>
                            <canvas id="chartBig1" style="display: block; width: 882px; height: 220px;" class="chartjs-render-monitor" width="882" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" hidden>
            <div class="col-lg-4">
                <div class="card card-chart">
                    <div class="card-header">
                        <h5 class="card-category">Total Shipments</h5>
                        <h3 class="card-title"><i class="tim-icons icon-bell-55 text-primary"></i> 763,215</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <div style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;" class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                </div>
                            </div>
                            <canvas id="chartLinePurple" style="display: block; width: 267px; height: 220px;" class="chartjs-render-monitor" width="267" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-chart">
                    <div class="card-header">
                        <h5 class="card-category">Daily Sales</h5>
                        <h3 class="card-title"><i class="tim-icons icon-delivery-fast text-info"></i> 3,500€</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <div style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;" class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                </div>
                            </div>
                            <canvas id="CountryChart" style="display: block; width: 267px; height: 220px;" class="chartjs-render-monitor" width="267" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-chart">
                    <div class="card-header">
                        <h5 class="card-category">Completed Tasks</h5>
                        <h3 class="card-title"><i class="tim-icons icon-send text-success"></i> 12,100K</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <div style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;" class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                    <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                </div>
                            </div>
                            <canvas id="chartLineGreen" style="display: block; width: 267px; height: 220px;" class="chartjs-render-monitor" width="267" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-broadcast-tower"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Node-B</h4>
                            </div>
                            <div class="card-body">
                                <?= countDataa('datanodeb'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>OLT</h4>
                            </div>
                            <div class="card-body">
                                <?= countDataa('dataolt'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>NAKER</h4>
                            </div>
                            <div class="card-body">
                                <?= countDataa('naker'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modaldetailTicket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div id="editModal"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modaldetailFf" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div id="editModal"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTicketNow" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div id="editModal"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFfNow" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div id="editModal"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOrderNow" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div id="editModal"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOnt" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div id="editModal"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('page-js') ?>
<!-- <script src="<?= base_url() ?>/template/assets/js/page/index.js"></script> -->
<!-- <script src="<?= base_url() ?>/template/assets/js/page/index-0.js"></script> -->
<!-- <script src="<?= base_url() ?>/template/assets/js/page/modules-chartjs.js"></script> -->
<script>
    $(document).ready(function() {

        var bulan = ""
        $('a.toggle-ticket, a.toggle-ticket-divisi').on('click', function(e) {
            e.preventDefault();
            var judul = $(this).attr('data-judul');
            var bulan = $(this).attr('data-bulan');
            var divisi = $(this).attr('data-divisi');

            $('a.toggle-ticket').attr('data-divisi', divisi)
            $('a.toggle-ticket-divisi').attr('data-bulan', bulan)
            var divisi = $('a.toggle-ticket').attr('data-divisi');
            var bulan = $('a.toggle-ticket-divisi').attr('data-bulan');

            $('a.toggle-ticket').removeClass('active')
            $('a.toggle-ticket[data-bulan="' + bulan + '"').addClass('active')
            $('#orders-month').html(judul)

            $('a.toggle-ticket-divisi').removeClass('active')
            $('a.toggle-ticket-divisi[data-divisi="' + divisi + '"').addClass('active')
            $('#orders-month-divisi').html(divisi)

            $.ajax({
                url: "<?= site_url('/dashboard/countTicket'); ?>",
                data: "divisi=" + divisi + "&bulan=" + bulan,
                dataType: "html",
                success: function(response) {
                    $('#countWan').empty();
                    $('#countWan').append(response);
                }
            });

            $('#modaldetailTicket').on('show.bs.modal', function(event) {
                var status = $(event.relatedTarget).data('status');
                var bodyModal =
                    '<div class="modal-body table-responsive">' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableTicket" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>No</th>' +
                    '<th>Incident</th>' +
                    '<th>Customer Name</th>' +
                    '<th>Service No</th>' +
                    '<th>TTR Cust</th>' +
                    '<th>Status</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>'


                $(this).find("#editModal").html(bodyModal);

                table = $('#modalTableTicket').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/monthTicket') ?>" + "?" + "divisi=" + divisi + "&bulan=" + bulan + "&status=" + status,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'incident'
                        },
                        {
                            data: 'customer_name'
                        },
                        {
                            data: 'service_no'
                        },
                        {
                            data: 'ttr_customer'
                        },
                        {
                            data: 'status'
                        },
                    ]
                });

            });
        });


        var MyDate = new Date();
        var MyDateString;

        MyDate.setDate(MyDate.getDate());
        // MyDate.setDate(MyDate.getDate() + 20);

        MyDateString = MyDate.getFullYear() + '-' + ('0' + (MyDate.getMonth() + 1)).slice(-2);

        $('#countWan').html('<div class="text-center">' +
            '<h6>' +
            '<span class="spinner-border spinner-border-sm mt-3" role="status" aria-hidden="true"></span> Processing...' +
            '</h6>' +
            '</div>'
        );
        $.ajax({
            url: "<?= site_url('/dashboard/countTicket'); ?>",
            data: "divisi=" + 'All' + "&bulan=" + MyDateString,
            dataType: "html",
            success: function(response) {
                $('#countWan').empty();
                $('#countWan').append(response);
            }
        });

        $('#modaldetailTicket').on('show.bs.modal', function(event) {
            var status = $(event.relatedTarget).data('status');
            var bodyModal =
                '<div class="modal-body table-responsive">' +
                '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableTicket" style="width: 100%">' +
                '<thead>' +
                '<tr>' +
                '<th>No</th>' +
                '<th>Incident</th>' +
                '<th>Customer Name</th>' +
                '<th>Service No</th>' +
                '<th>TTR Cust</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>'


            $(this).find("#editModal").html(bodyModal);

            table = $('#modalTableTicket').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('dashboard/monthTicket') ?>" + "?" + "divisi=" + 'All' + "&bulan=" + MyDateString + "&status=" + status,
                order: [],
                columnDefs: [],
                columns: [{
                        data: 'number',
                        orderable: false
                    },
                    {
                        data: 'incident'
                    },
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'service_no'
                    },
                    {
                        data: 'ttr_customer'
                    },
                    {
                        data: 'status'
                    },
                ]
            });

        });



    });

    $(document).ready(function() {
        $('a.toggle-ff, a.toggle-ff-divisi').on('click', function(e) {
            e.preventDefault();
            var judul = $(this).attr('data-judul');
            var bulan = $(this).attr('data-bulan');
            var divisi = $(this).attr('data-divisi');

            $('a.toggle-ff').attr('data-divisi', divisi)
            $('a.toggle-ff-divisi').attr('data-bulan', bulan)
            var divisi = $('a.toggle-ff').attr('data-divisi');
            var bulan = $('a.toggle-ff-divisi').attr('data-bulan');

            $('a.toggle-ff').removeClass('active')
            $('a.toggle-ff[data-bulan="' + bulan + '"').addClass('active')
            $('#orders-month-ff').html(judul)

            $('a.toggle-ff-divisi').removeClass('active')
            $('a.toggle-ff-divisi[data-divisi="' + divisi + '"').addClass('active')
            $('#orders-month-ff-divisi').html(divisi)

            $.ajax({
                url: "<?= site_url('/dashboard/countFf'); ?>",
                data: "divisi=" + divisi + "&bulan=" + bulan,
                dataType: "html",
                success: function(response) {
                    $('#countWanff').empty();
                    $('#countWanff').append(response);
                }
            });

            $('#modaldetailFf').on('show.bs.modal', function(event) {
                var status = $(event.relatedTarget).data('status');
                var bodyModal =
                    '<div class="modal-body table-responsive">' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableFf" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>NO</th>' +
                    '<th>LAYANAN</th>' +
                    '<th>NO_ORDER</th>' +
                    '<th>ORDER_TYPE</th>' +
                    '<th>NAMA_PELANGGAN</th>' +
                    '<th>Status</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>'


                $(this).find("#editModal").html(bodyModal);

                table = $('#modalTableFf').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/monthFf') ?>" + "?" + "divisi=" + divisi + "&bulan=" + bulan + "&status=" + status,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'layanan'
                        },
                        {
                            data: 'no_order'
                        },
                        {
                            data: 'order_type'
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'status'
                        },
                    ]
                });

            });

        });

        var MyDate = new Date();
        var MyDateString;

        MyDate.setDate(MyDate.getDate());
        // MyDate.setDate(MyDate.getDate() + 20);

        MyDateString = MyDate.getFullYear() + '-' + ('0' + (MyDate.getMonth() + 1)).slice(-2);

        $('#countWanff').html('<div class="text-center">' +
            '<h6>' +
            '<span class="spinner-border spinner-border-sm mt-3" role="status" aria-hidden="true"></span> Processing...' +
            '</h6>' +
            '</div>'
        );
        $.ajax({
            url: "<?= site_url('/dashboard/countFf'); ?>",
            data: "divisi=" + 'All' + "&bulan=" + MyDateString,
            dataType: "html",
            success: function(response) {
                $('#countWanff').empty();
                $('#countWanff').append(response);
            }
        });


        $('#modaldetailFf').on('show.bs.modal', function(event) {
            var status = $(event.relatedTarget).data('status');
            var bodyModal =
                '<div class="modal-body table-responsive">' +
                '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableFf" style="width: 100%">' +
                '<thead>' +
                '<tr>' +
                '<th>NO</th>' +
                '<th>LAYANAN</th>' +
                '<th>NO_ORDER</th>' +
                '<th>ORDER_TYPE</th>' +
                '<th>NAMA_PELANGGAN</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>'


            $(this).find("#editModal").html(bodyModal);

            table = $('#modalTableFf').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('dashboard/monthFf') ?>" + "?" + "divisi=" + 'All' + "&bulan=" + MyDateString + "&status=" + status,
                order: [],
                columnDefs: [],
                columns: [{
                        data: 'number',
                        orderable: false
                    },
                    {
                        data: 'layanan'
                    },
                    {
                        data: 'no_order'
                    },
                    {
                        data: 'order_type'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'status'
                    },
                ]
            });

        });

    });

    $(document).ready(function() {
        $('a.toggle-order-divisi').on('click', function(e) {
            e.preventDefault();
            var divisi = $(this).attr('data-divisi');

            $('a.toggle-order-divisi').removeClass('active')
            $('a.toggle-order-divisi[data-divisi="' + divisi + '"').addClass('active')
            $('#orders-divisi').html(divisi)

            $.ajax({
                url: "<?= site_url('/dashboard/countOrderNow'); ?>",
                data: "divisi=" + divisi,
                dataType: "html",
                success: function(response) {
                    $('#countOrderNow').empty();
                    $('#countOrderNow').append(response);
                }
            });


            $('#modalTicketNow').on('show.bs.modal', function(event) {
                var status = $(event.relatedTarget).data('status');
                var bodyModal =
                    '<div class="modal-body table-responsive">' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableTicketNow" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>No</th>' +
                    '<th>Incident</th>' +
                    '<th>Customer Name</th>' +
                    '<th>Service No</th>' +
                    '<th>TTR Cust</th>' +
                    '<th>Status</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>'


                $(this).find("#editModal").html(bodyModal);

                table = $('#modalTableTicketNow').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/ticketNow') ?>" + "?" + "divisi=" + divisi + "&status=" + status,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'incident'
                        },
                        {
                            data: 'customer_name'
                        },
                        {
                            data: 'service_no'
                        },
                        {
                            data: 'ttr_customer'
                        },
                        {
                            data: 'status'
                        },
                    ]
                });

            });


            $('#modalFfNow').on('show.bs.modal', function(event) {
                var status = $(event.relatedTarget).data('status');
                var bodyModal =
                    '<div class="modal-body table-responsive">' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableFfNow" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>NO</th>' +
                    '<th>LAYANAN</th>' +
                    '<th>NO_ORDER</th>' +
                    '<th>ORDER_TYPE</th>' +
                    '<th>NAMA_PELANGGAN</th>' +
                    '<th>Status</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>'


                $(this).find("#editModal").html(bodyModal);

                table = $('#modalTableFfNow').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/ffNow') ?>" + "?" + "divisi=" + divisi + "&status=" + status,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'layanan'
                        },
                        {
                            data: 'no_order'
                        },
                        {
                            data: 'order_type'
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'status'
                        },
                    ]
                });

            });


            $('#modalOrderNow').on('show.bs.modal', function(event) {
                var statusff = $(event.relatedTarget).data('status-ff');
                var statusasr = $(event.relatedTarget).data('status-asr');
                var bodyModal =
                    '<div class="modal-body table-responsive"><h5>Assurance</h5>' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableTicketNow" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>No</th>' +
                    '<th>Incident</th>' +
                    '<th>Customer Name</th>' +
                    '<th>Service No</th>' +
                    '<th>TTR Cust</th>' +
                    '<th>Status</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>' +
                    '<div class="modal-body table-responsive"><h5>Fulfillment</h5>' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableFfNow" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>NO</th>' +
                    '<th>LAYANAN</th>' +
                    '<th>NO_ORDER</th>' +
                    '<th>ORDER_TYPE</th>' +
                    '<th>NAMA_PELANGGAN</th>' +
                    '<th>Status</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>'


                $(this).find("#editModal").html(bodyModal);

                tableff = $('#modalTableFfNow').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/ffNow') ?>" + "?" + "divisi=" + divisi + "&status=" + statusff,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'layanan'
                        },
                        {
                            data: 'no_order'
                        },
                        {
                            data: 'order_type'
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'status'
                        },
                    ]
                });

                tableasr = $('#modalTableTicketNow').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/ticketNow') ?>" + "?" + "divisi=" + divisi + "&status=" + statusasr,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'incident'
                        },
                        {
                            data: 'customer_name'
                        },
                        {
                            data: 'service_no'
                        },
                        {
                            data: 'ttr_customer'
                        },
                        {
                            data: 'status'
                        },
                    ]
                });

            });

        });

        $('#countOrderNow').html('<div class="text-center">' +
            '<h6>' +
            '<span class="spinner-border spinner-border-sm mt-3" role="status" aria-hidden="true"></span> Processing...' +
            '</h6>' +
            '</div>'
        );
        $.ajax({
            url: "<?= site_url('/dashboard/countOrderNow'); ?>",
            data: "divisi=" + 'All',
            dataType: "html",
            success: function(response) {
                $('#countOrderNow').empty();
                $('#countOrderNow').append(response);
            }
        });

        $('#modalTicketNow').on('show.bs.modal', function(event) {
            var status = $(event.relatedTarget).data('status');
            var bodyModal =
                '<div class="modal-body table-responsive">' +
                '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableTicketNow" style="width: 100%">' +
                '<thead>' +
                '<tr>' +
                '<th>No</th>' +
                '<th>Incident</th>' +
                '<th>Customer Name</th>' +
                '<th>Service No</th>' +
                '<th>TTR Cust</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>'


            $(this).find("#editModal").html(bodyModal);

            table = $('#modalTableTicketNow').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('dashboard/ticketNow') ?>" + "?" + "divisi=" + 'All' + "&status=" + status,
                order: [],
                columnDefs: [],
                columns: [{
                        data: 'number',
                        orderable: false
                    },
                    {
                        data: 'incident'
                    },
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'service_no'
                    },
                    {
                        data: 'ttr_customer'
                    },
                    {
                        data: 'status'
                    },
                ]
            });

        });


        $('#modalFfNow').on('show.bs.modal', function(event) {
            var status = $(event.relatedTarget).data('status');
            var bodyModal =
                '<div class="modal-body table-responsive">' +
                '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableFfNow" style="width: 100%">' +
                '<thead>' +
                '<tr>' +
                '<th>NO</th>' +
                '<th>LAYANAN</th>' +
                '<th>NO_ORDER</th>' +
                '<th>ORDER_TYPE</th>' +
                '<th>NAMA_PELANGGAN</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>'


            $(this).find("#editModal").html(bodyModal);

            table = $('#modalTableFfNow').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('dashboard/ffNow') ?>" + "?" + "divisi=" + 'All' + "&status=" + status,
                order: [],
                columnDefs: [],
                columns: [{
                        data: 'number',
                        orderable: false
                    },
                    {
                        data: 'layanan'
                    },
                    {
                        data: 'no_order'
                    },
                    {
                        data: 'order_type'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'status'
                    },
                ]
            });

        });


        $('#modalOrderNow').on('show.bs.modal', function(event) {
            var statusff = $(event.relatedTarget).data('status-ff');
            var statusasr = $(event.relatedTarget).data('status-asr');
            var bodyModal =
                '<div class="modal-body table-responsive"><h5>ASSURANCE</h5>' +
                '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableTicketNow" style="width: 100%">' +
                '<thead>' +
                '<tr>' +
                '<th>No</th>' +
                '<th>Incident</th>' +
                '<th>Customer Name</th>' +
                '<th>Service No</th>' +
                '<th>TTR Cust</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>' +
                '<div class="modal-body table-responsive"><h5>FULFILLMENT</h5>' +
                '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableFfNow" style="width: 100%">' +
                '<thead>' +
                '<tr>' +
                '<th>NO</th>' +
                '<th>LAYANAN</th>' +
                '<th>NO_ORDER</th>' +
                '<th>ORDER_TYPE</th>' +
                '<th>NAMA_PELANGGAN</th>' +
                '<th>Status</th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>'


            $(this).find("#editModal").html(bodyModal);

            tableff = $('#modalTableFfNow').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('dashboard/ffNow') ?>" + "?" + "divisi=" + 'All' + "&status=" + statusff,
                order: [],
                columnDefs: [],
                columns: [{
                        data: 'number',
                        orderable: false
                    },
                    {
                        data: 'layanan'
                    },
                    {
                        data: 'no_order'
                    },
                    {
                        data: 'order_type'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'status'
                    },
                ]
            });

            tableasr = $('#modalTableTicketNow').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('dashboard/ticketNow') ?>" + "?" + "divisi=" + 'All' + "&status=" + statusasr,
                order: [],
                columnDefs: [],
                columns: [{
                        data: 'number',
                        orderable: false
                    },
                    {
                        data: 'incident'
                    },
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'service_no'
                    },
                    {
                        data: 'ttr_customer'
                    },
                    {
                        data: 'status'
                    },
                ]
            });



        });


    });

    $(document).ready(function() {

        $('#modalOnt').on('show.bs.modal', function(event) {
            var sto = $(event.relatedTarget).data('sto');
            if (sto == '<strong>Total</strong>') {
                sto = 'Total';
            }

            if (sto == 'Total') {
                var bodyModal =
                    '<div class="modal-body table-responsive">' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableOnt" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>NO</th>' +
                    '<th>MERK</th>' +
                    '<th>TYPE</th>' +
                    '<th>SERIAL_NUMBER</th>' +
                    '<th>STATUS</th>' +
                    '<th>LOKASI</th>' +
                    '<th>DESCRIPTION</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>'


                $(this).find("#editModal").html(bodyModal);

                return table = $('#modalTableOnt').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/OntNodeb') ?>" + "?" + "sto=" + sto,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'merk'
                        },
                        {
                            data: 'type'
                        },
                        {
                            data: 'serial_number'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'idsto'
                        },
                        {
                            data: 'desc'
                        },
                    ]
                });

            }

            if (sto == 'RUSAK' || sto == 'OLO' || sto == 'FULFILLMENT') {
                var bodyModal =
                    '<div class="modal-body table-responsive">' +
                    '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableOnt" style="width: 100%">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>NO</th>' +
                    '<th>MERK</th>' +
                    '<th>TYPE</th>' +
                    '<th>SERIAL_NUMBER</th>' +
                    '<th>LOKASI</th>' +
                    '<th>DESCRIPTION</th>' +
                    '</tr>' +
                    '</thead>' +
                    '</table>' +
                    '</div>'


                $(this).find("#editModal").html(bodyModal);

                return table = $('#modalTableOnt').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    destroy: true,
                    ajax: "<?php echo site_url('dashboard/OntNodeb') ?>" + "?" + "sto=" + sto,
                    order: [],
                    columnDefs: [],
                    columns: [{
                            data: 'number',
                            orderable: false
                        },
                        {
                            data: 'merk'
                        },
                        {
                            data: 'type'
                        },
                        {
                            data: 'serial_number'
                        },
                        {
                            data: 'idsto'
                        },
                        {
                            data: 'desc'
                        },
                    ]
                });

            }

            var allocation = $(event.relatedTarget).data('allocation');
            var bodyModal =
                '<div class="modal-body table-responsive">' +
                '<table class="table table-striped table-hover table-borderless table-sm" id="modalTableOnt" style="width: 100%">' +
                '<thead>' +
                '<tr>' +
                '<th>NO</th>' +
                '<th>MERK</th>' +
                '<th>TYPE</th>' +
                '<th>SERIAL_NUMBER</th>' +
                '<th>DESCRIPTION</th>' +
                '</tr>' +
                '</thead>' +
                '</table>' +
                '</div>'


            $(this).find("#editModal").html(bodyModal);

            table = $('#modalTableOnt').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('dashboard/OntNodeb') ?>" + "?" + "sto=" + sto,
                order: [],
                columnDefs: [],
                columns: [{
                        data: 'number',
                        orderable: false
                    },
                    {
                        data: 'merk'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'serial_number'
                    },
                    {
                        data: 'desc'
                    },
                ]
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#reportall').html('<div class="text-center">' +
            '<h6>' +
            '<span class="spinner-border spinner-border-sm mt-3" role="status" aria-hidden="true"></span> Processing...' +
            '</h6>' +
            '</div>'
        );
        $.ajax({
            url: "<?= site_url('/dashboard/reportall'); ?>",
            dataType: "html",
            success: function(response) {
                $('#reportall').empty();
                $('#reportall').append(response);
            }
        });
    });
</script>
<script>
    const baseUrl = "<?php echo base_url(); ?>"
    const myChart = (chartType) => {
        $.ajax({
            url: "<?= site_url('/dashboard/chart_data'); ?>",
            dataType: 'json',
            method: 'get',
            success: data => {
                let chartX = []
                let chartY = []
                let chartWan = []
                let chartCcan = []
                let chartWifi = []
                data.map(data => {
                    chartX.push(data.day)
                    // chartY.push(data.total)
                    chartWan.push(data.wan)
                    chartCcan.push(data.ccan)
                    chartWifi.push(data.wifi)
                })
                var chartData = {
                    labels: chartX,
                    datasets: [{
                            label: 'WAN',
                            data: chartWan,
                            // backgroundColor: ['lightcoral'],
                            // borderColor: ['lightcoral'],
                            // borderWidth: 4
                            backgroundColor: '#5fd7f8',
                            borderWidth: 2,
                            borderColor: '#5fd7f8',
                            backgroundColor: 'transparent',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6777ef',
                            pointRadius: 4
                        },
                        {
                            label: 'CCAN',
                            data: chartCcan,
                            // backgroundColor: ['lightcoral'],
                            // borderColor: ['lightcoral'],
                            // borderWidth: 4
                            backgroundColor: '#f8d95f',
                            borderWidth: 2,
                            borderColor: '#f8d95f',
                            backgroundColor: 'transparent',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6777ef',
                            pointRadius: 4
                        },
                        {
                            label: 'WIFI',
                            data: chartWifi,
                            // backgroundColor: ['lightcoral'],
                            // borderColor: ['lightcoral'],
                            // borderWidth: 4
                            backgroundColor: '#5ff8b5',
                            borderWidth: 2,
                            borderColor: '#5ff8b5',
                            backgroundColor: 'transparent',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6777ef',
                            pointRadius: 4
                        }
                    ]
                }
                var ctx = document.getElementById(chartType).getContext('2d')
                var config = {
                    type: chartType,
                    data: chartData
                }
                switch (chartType) {
                    case 'pie':
                        const pieColor = ['salmon', 'red', 'green', 'blue', 'aliceblue', 'pink', 'orange', 'gold', 'plum', 'darkcyan', 'wheat', 'silver']
                        chartData.datasets[0].backgroundColor = pieColor
                        chartData.datasets[0].borderColor = pieColor
                        break;
                    case 'bar':
                        chartData.datasets[0].backgroundColor = ['skyblue']
                        chartData.datasets[0].borderColor = ['skyblue']
                        config.options = {
                            legend: {
                                display: false
                            },
                            scales: {
                                yAxes: [{
                                    gridLines: {
                                        drawBorder: false,
                                        color: '#f2f2f2',
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        stepSize: 1
                                    }
                                }],
                                xAxes: [{
                                    gridLines: {
                                        display: false
                                    }
                                }]
                            },
                        }
                        break;
                    default:
                        config.options = {
                            // scales: {
                            //     y: {
                            //         beginAtZero: true,
                            //     }
                            // }
                            legend: {
                                display: true
                            },
                            // scales: {
                            //     yAxes: [{
                            //         gridLines: {
                            //             display: false,
                            //             drawBorder: false,
                            //         },
                            //         ticks: {
                            //             stepSize: 1
                            //         }
                            //     }],
                            //     xAxes: [{
                            //         gridLines: {
                            //             color: '#fbfbfb',
                            //             lineWidth: 2
                            //         }
                            //     }]
                            // },
                        }
                }
                var chart = new Chart(ctx, config)
            }
        })
        $.ajax({
            url: "<?= site_url('/dashboard/chart_data_month'); ?>",
            dataType: 'json',
            method: 'get',
            success: data => {
                let chartX = []
                let chartY = []
                let chartWan = []
                let chartCcan = []
                let chartWifi = []
                data.map(data => {
                    chartX.push(data.month)
                    // chartY.push(data.total)
                    chartWan.push(data.wan)
                    chartCcan.push(data.ccan)
                    chartWifi.push(data.wifi)
                })
                var chartData = {
                    labels: chartX,
                    datasets: [{
                            label: 'WAN',
                            data: chartWan,
                            borderWidth: 1,
                            backgroundColor: '#5fd7f8',
                            borderColor: '#6777ef',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6777ef',
                            pointRadius: 4
                        },
                        {
                            label: 'CCAN',
                            data: chartCcan,
                            borderWidth: 1,
                            backgroundColor: '#f8d95f',
                            borderColor: '#6777ef',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6777ef',
                            pointRadius: 4
                        },
                        {
                            label: 'WIFI',
                            data: chartWifi,
                            borderWidth: 1,
                            backgroundColor: '#5ff8b5',
                            borderColor: '#6777ef',
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6777ef',
                            pointRadius: 4
                        }
                    ]
                }
                var ctx = document.getElementById('bar_month').getContext('2d')
                var config = {
                    type: 'bar',
                    data: chartData,
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                }

                var chart = new Chart(ctx, config)
            }
        })
    }

    // myChart('pie')
    myChart('line')
    // myChart('bar')
    // myChart('bar_month')
</script>

<script>
    const text = document.getElementById("quote");
    const author = document.getElementById("author");

    const getNewQuote = async () => {
 
        //function to dynamically display the quote and the author
        text.innerHTML = "Since when did we lose genuine happiness...";
        author.innerHTML = "Anonymous";
    // const getNewQuote = async () => {
    //     //api for quotes
    //     var url = "https://type.fit/api/quotes";

    //     // fetch the data from api
    //     const response = await fetch(url);
    //     // console.log(typeof response);
    //     //convert response to json and store it in quotes array
    //     const allQuotes = await response.json();

    //     // Generates a random number between 0 and the length of the quotes array
    //     const indx = Math.floor(Math.random() * allQuotes.length);

    //     //Store the quote present at the randomly generated index
    //     const quote = allQuotes[indx].text;

    //     //Store the author of the respective quote
    //     const auth = allQuotes[indx].author;

    //     if (auth == null) {
    //         author = "Anonymous";
    //     }

    //     //function to dynamically display the quote and the author
    //     text.innerHTML = quote;
    //     author.innerHTML = auth.replace(", type.fit", "");

    }

    getNewQuote();
</script>
<?= $this->endSection(); ?>