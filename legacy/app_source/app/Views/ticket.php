<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <!-- <div class="section-header">
            <h1>assurance</h1>
            <div class="section-header-button">
                <a href="<?= base_url('wan/assurance/new'); ?>" class="a btn btn-primary">Add Data</a>
            </div>
        </div> -->

        <?php if (session()->getFlashData('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success! </strong> <?= session()->getFlashData('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashData('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error! </strong> <?= session()->getFlashData('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="section-body">
            <div class="card">
                <div class="card-header p-0">
                    <div class="col-10 pr-0">
                        <h4>DATA ASSURANCE
                            <a href="<?= base_url('wan/assurance/new'); ?> " class="btn btn-sm btn-round btn-primary ml-3" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-plus"></i></a>
                        </h4>
                    </div>
                    <div class="col-2 text-right pl-0">
                        <a href="<?= base_url('wan/assurance/trash'); ?> " class="btn btn-sm rounded-pill btn-outline-danger" data-toggle="tooltip" data-placement="bottom" title="Data Trash"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <div class="card-header justify-content-center">
                    <form class="row justify-content-center" action="" method="get" autocomplete="off">
                        <div class="row no-gutters mt-3 mb-2 align-items-center" style="float: none; margin: 0 auto;">
                            <div class="float-none ml-5">
                                <?php $request = \Config\Services::request(); ?>
                                <input type="text" name="keyword" value="<?= $request->getGet("keyword") ?>" class="form-control border-10 rounded-pill pr-5" style="width: 155pt;" placeholder="Search">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-link btn-sm border-0 rounded-pill ml-n5" type="submit" style="background-color: transparent;">
                                    <i class="fa fa-search text-primary"></i>
                                </button>
                            </div>

                            <div class="search-icon float-none m-auto">

                                <!-- trigger keyword to export excel -->
                                <?php
                                $request = \Config\Services::request();
                                $keyword = $request->getGet("keyword");
                                if ($keyword != '') {
                                    $param = "?keyword=" . $keyword;
                                } else {
                                    $param = "";
                                }
                                ?>
                                <div class="btn-group">
                                    <a href="<?= site_url('wan/assurance/export' . $param); ?>" class="btn btn-sm btn-primary animation-on-hover">
                                        <i class="fas fa-file-download"></i> Export Excel
                                    </a>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-file-upload"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item has-icon" href="<?= base_url('ASSURANCE-Format-Import.xlsx'); ?>"><i class="far fa-file-excel"></i> Download Format</a>
                                            <a class="dropdown-item has-icon" href="" data-toggle="modal" data-target="#table_import"><i class="fas fa-file-import"></i> Upload File</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body p-2 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm table-bordered" id="assuranceWan" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action</th>
                                    <th>Incident</th>
                                    <th>Customer Name</th>
                                    <th>Summary</th>
                                    <th>Service No</th>
                                    <th>Reported Date</th>
                                    <th>TTR Customer</th>
                                    <th>Status</th>
                                    <th>Workzone</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>




<!-- dataTables serverside -->
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-3.5.1.js"></script>
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-1.12.1-dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#assuranceWan').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: "<?php echo site_url('wan/assurance/listdata' . $param) ?>",
            order: [],
            columnDefs: [{
                    targets: 0,
                    orderable: false
                }, //first column is not orderable.
            ],
            columns: [{
                    data: 'number'
                },
                {
                    data: 'action'
                },
                {
                    data: 'incident'
                },
                {
                    data: 'customer_name'
                },
                {
                    data: 'summary'
                },
                {
                    data: 'service_no'
                },
                {
                    data: 'reported_date'
                },
                {
                    data: 'ttr_customer'
                },
                {
                    data: 'status'
                },
                {
                    data: 'workzone'
                },
            ]
        });
    });
</script>



<?= $this->endSection(); ?>