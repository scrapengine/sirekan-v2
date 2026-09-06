<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<!-- alert -->
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
<!-- end alert -->




<div class="main-content">
    <section class="section">
        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4 text-left">
                            <h4 class="card-title" style="color: #d3d4d8;"><strong>Data OLT</strong>
                                <a href="<?= base_url('wan/olt/new'); ?> " class="a btn btn-sm btn-round btn-primary ml-3" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-plus"></i></a>
                            </h4>
                        </div>
                        <div class="col-8">
                            <div class="btn-group btn-group-toggle float-right">
                                <div class="card-header-action">
                                    <a href="<?= base_url('wan/olt/trash'); ?> " class="btn btn-sm rounded-pill btn-outline-danger" data-toggle="tooltip" data-placement="bottom" title="Data Trash"><i class="fas fa-trash" style="color: red;"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header p-0">
                        <form action="" method="get" autocomplete="off">
                            <div class="row no-gutters mt-3 mb-2 align-items-center">
                                <div class="float-left">
                                    <?php $request = \Config\Services::request(); ?>
                                    <input type="text" name="keyword" value="<?= $request->getGet("keyword") ?>" class="form-control border-10 rounded-pill pr-5" style="width: 155pt;" placeholder="Search">
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm border-0 rounded-pill ml-n5" type="submit" style="background-color: transparent;">
                                        <i class="fa fa-search text-primary"></i>
                                    </button>
                                </div>

                                <div class="search-icon float-right ml-2">

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

                                    <a href="<?= site_url('wan/olt/export' . $param); ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-download"></i> Export Excel
                                    </a>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-file-upload"></i> Import Excel
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item has-icon" href="<?= base_url('OLT-Format-Import.xlsx'); ?>"><i class="far fa-file-excel"></i> Download Format</a>
                                            <a class="dropdown-item has-icon" href="" data-toggle="modal" data-target="#olt_import"><i class="fas fa-file-import"></i> Upload File</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-md" id="myTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action</th>
                                    <th>Witel</th>
                                    <th>STO</th>
                                    <th>Hostname_Metro</th>
                                    <th>Ip_Metro</th>
                                    <th>Port_Metro</th>
                                    <th>Hostname_OLT</th>
                                    <th>Ip_OLT</th>
                                    <th>Port_OLT</th>
                                    <th>Platform</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $no = 1 + (10 * ($page - 1));
                                ?>
                                <?php foreach ($dataOlt as $d) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="btn-group" role="group">

                                            <!-- <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#edit<?= $d->idolt; ?>"><i class='fas fa-pencil-alt' style='color: orange'></i>
                                    </button> -->
                                            <a href="/wan/olt/<?= $d->idolt; ?>/edit" type="button" class="btn btn-sm"><i class='fas fa-pencil-alt' style='color: orange'></i>
                                            </a>
                                            <input type="hidden" name="deletesiteid" value="<?= $d->idolt; ?>">
                                            <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#delete<?= $d->idolt; ?>">
                                                <i class='fa fa-trash' style='color: red'></i>
                                            </button>
                                        </td>
                                        <td><?= $d->witel; ?></td>
                                        <td><?= $d->sto; ?></td>

                                        <td><?= $d->hostname_metro; ?></td>
                                        <td><?= $d->ip_metro; ?></td>

                                        <td><?= ($d->port_metro != null) ? $d->port_metro : $d->ip_metro; ?></td>
                                        <td><?= $d->hostname_olt; ?></td>
                                        <td><?= $d->ip_olt; ?></td>
                                        <td><?= $d->port_olt; ?></td>
                                        <td><?= $d->platform; ?></td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>

                        <!-- pagination -->
                        <div class="float-left">
                            <i>Showing <?= 1 + (10 * ($page - 1)) ?> to <?= $no - 1 ?> of <?= $pager->getTotal() ?> entries</i>
                        </div>
                        <div class="float-right">
                            <?= $pager->links('default', 'pagination') ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php foreach ($dataOlt as $d) : ?>

    <!-- The Modal Delete-->
    <div class="modal fade" id="delete<?= $d->idolt; ?>">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Delete Data</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <form action="/wan/olt/<?= $d->idolt; ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="modal-body">
                        Apakah anda yakin ingin menghapus <?= $d->hostname_olt; ?> ?
                        <input type="hidden" name="idnodeb" value="<?= $d->idolt; ?>">
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
            <form action="/wan/olt/import" method="post" enctype="multipart/form-data">
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



<?= $this->endSection(); ?>