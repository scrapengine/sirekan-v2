<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>METRO</h1>
            <div class="section-header-button">
                <a href="<?= base_url('wan/metro/new'); ?> " class="a btn btn-primary">Add Data</a>
            </div>
        </div>

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
                <div class="card-header">
                    <h4>Data METRO</h4>
                    <div class="card-header-action">
                        <a href="/wan/metro/trash" class="btn btn-outline-danger"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <div class="card-header">
                    <form action="" method="get" autocomplete="off">
                        <div class="float-left">
                            <?php $request = \Config\Services::request(); ?>
                            <input type="text" name="keyword" value="<?= $request->getGet("keyword") ?>" class="form-control" style="width: 155pt;" placeholder="Search">
                        </div>
                        <div class="float-right ml-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>

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

                            <a href="<?= site_url('wan/metro/export' . $param); ?>" class="btn btn-primary">
                                <i class="fas fa-file-download"></i> Export Excel
                            </a>
                            <div class="dropdown d-inline">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-upload"></i> Import Excel
                                </button>
                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <a class="dropdown-item has-icon" href="<?= base_url('METRO-Format-Import.xlsx'); ?>"><i class="far fa-file-excel"></i> Download Format</a>
                                    <a class="dropdown-item has-icon" href="" data-toggle="modal" data-target="#olt_import"><i class="fas fa-file-import"></i> Upload File</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-md" id="datametro" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action</th>
                                    <th>Hostname_Metro</th>
                                    <th>Ip_Metro</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php foreach ($dataMetro as $d) : ?>

    <!-- The Modal Delete-->
    <div class="modal fade" id="delete<?= $d->idmetro; ?>">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Delete Data</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <form action="/wan/metro/<?= $d->idmetro; ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="modal-body">
                        Apakah anda yakin ingin menghapus <?= $d->hostname_metro; ?> ?
                        <input type="hidden" name="idnodeb" value="<?= $d->idmetro; ?>">
                        <br>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" name="deletenodeb">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endforeach; ?>



<!-- The Modal Import-->
<div class="modal fade" id="olt_import">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Import</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <!-- Don't Forget to add enctype for input file -->
            <form action="/wan/metro/import" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <label>File Excel</label>
                    <div class="custom-file">
                        <input type="file" name="file_excel" class="custom-file-input" id="file_excel" required>
                        <label for="file_excel" class="custom-file-label">Pilih File</label>
                        <br>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" /> -->


<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#datametro').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo site_url('wan/metro/listdata') ?>",
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
                    data: 'hostname_metro'
                },
                {
                    data: 'ip_metro'
                },
            ]
        });
    });
</script>


<?= $this->endSection(); ?>