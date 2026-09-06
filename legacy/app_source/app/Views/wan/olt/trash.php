<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>




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
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-4">
                                <a href="<?= base_url('wan/olt'); ?> " class="btn btn-sm"><i class="fas fa-arrow-left"></i></a>
                                <strong style="font-size: medium;">Data OLT</strong>
                                <b style="font-size: medium;color : crimson">Trash</b>
                            </div>
                            <div class="col-8">
                                <div class="btn-group btn-group-toggle float-right">
                                    <div class="card-header-action">
                                        <a href="/wan/olt/restore" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="bottom" title="Restore All"><i class="fas fa-recycle"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deletetrash">
                                            <i class="fas fa-trash" data-toggle="tooltip" data-placement="bottom" title="Delete All"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2 mt-5">
                        <div class="table-responsive">
                            <table class="table table-striped table-md" id="myTable">
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
                                                <a href="/wan/olt/restore/<?= $d->idolt; ?>" type="button" class="btn btn-sm"><i class='fas fa-recycle' style='color: DodgerBlue'></i>
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

                                            <td><?= $d->port_metro; ?></td>
                                            <td><?= $d->hostname_olt; ?></td>
                                            <td><?= $d->ip_olt; ?></td>
                                            <td><?= $d->port_olt; ?></td>
                                            <td><?= $d->platform; ?></td>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
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
                <form action="/wan/nodeb/deletetrash/<?= $d->idolt; ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="modal-body">
                        Apakah anda yakin ingin menghapus permanen <?= $d->hostname_olt; ?> ?
                        <input type="hidden" name="idolt" value="<?= $d->idolt; ?>">
                        <br>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endforeach; ?>


<!-- The Modal Delete-->
<div class="modal fade" id="deletetrash">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Data</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form action="/wan/nodeb/deletetrash" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    Apakah anda yakin ingin menghapus permanen seluruh data Trash?
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