<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<?php $request = \Config\Services::request(); ?>

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
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="font-weight-bold ml-3" style="font-size: 1rem;">DATA ONT</span></div>

            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                <div class="section-header-button ml-2">
                    <a href="<?= base_url('wan/ont/new'); ?> " class="btn btn-sm btn-link" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-upload"></i></a>
                </div>
                <div class="section-header-breadcrumb col-2 justify-content-end">
                    <div class="btn-group  rounded-pill">
                        <span data-toggle="modal" data-target="#table_import">
                            <a type="button" class="btn btn-sm btn-link text-info" data-toggle="tooltip" data-placement="bottom" title="Import Excel"> <i class="fas fa-file-upload" aria-hidden="true"></i></a>
                        </span>
                        <a href="<?= base_url('wan/ont/trash'); ?> " class="btn btn-sm btn-link text-danger" data-toggle="tooltip" data-placement="bottom" title="Data Trash"><i class="far fa-trash-alt"></i></a>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <!-- alert -->

        <?php if (session()->getFlashData('success')) : ?>
            <div id="flash" data-icon="success" data-title="Success!" data-flash="<?= session()->getFlashData('success'); ?>"></div>
        <?php endif; ?>

        <?php if (session()->getFlashData('error')) : ?>
            <div id="flash" data-icon="error" data-title="Error!" data-flash="<?= session()->getFlashData('error'); ?>"></div>
        <?php endif; ?>

        <!-- end alert -->

        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body p-2 mt-3">
                    <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="ont-stock-tab" data-toggle="pill" data-target="#ont-stock" type="button" role="tab" aria-controls="ont-stock" aria-selected="false">ASSURANCE
                                <span class="badge badge-info"><?= countOntAsr() ?></span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="ont-stockFf-tab" data-toggle="pill" data-target="#ont-stockFf" type="button" role="tab" aria-controls="ont-stockFf" aria-selected="false">FULFILLMENT
                                <span class="badge badge-info"><?= countOntFf() ?></span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="ont-stockOlo-tab" data-toggle="pill" data-target="#ont-stockOlo" type="button" role="tab" aria-controls="ont-stockOlo" aria-selected="false">OLO
                                <span class="badge badge-info"><?= countOntOlo() ?></span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="installed-tab" data-toggle="pill" data-target="#installed" type="button" role="tab" aria-controls="installed" aria-selected="false">INSTALLED</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="return-tab" data-toggle="pill" data-target="#return" type="button" role="tab" aria-controls="return" aria-selected="false">RETURN</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="ont-all-tab" data-toggle="pill" data-target="#ont-all" type="button" role="tab" aria-controls="ont-all" aria-selected="true">ALL</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-5" id="pills-tabContent">
                        <div class="tab-pane fade" id="ont-all" role="tabpanel" aria-labelledby="ont-all-tab">
                            <div class="text-right mb-3 pr-3">
                                <a href="<?= site_url('wan/ont/export' . $param); ?>" class="btn btn-link animation-on-hover">
                                    <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md" id="ontAll" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                                <th>Action</th>
                                            <?php endif ?>
                                            <th>#</th>
                                            <th>MERK</th>
                                            <th>TYPE</th>
                                            <th>SERIAL_NUMBER</th>
                                            <th>STATUS</th>
                                            <th>STO</th>
                                            <th>RECEIVED</th>
                                            <th>INSTALLED</th>
                                            <th>RETURN</th>
                                            <th>DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="ont-stock" role="tabpanel" aria-labelledby="ont-stock-tab">
                            <div class="text-right mb-3 pr-3">
                                <a href="<?= site_url('wan/ont/exportstock' . $param); ?>" class="btn btn-link animation-on-hover">
                                    <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md" id="ontStock" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                                <th>Action</th>
                                            <?php endif ?>
                                            <th>#</th>
                                            <th>MERK</th>
                                            <th>TYPE</th>
                                            <th>SERIAL_NUMBER</th>
                                            <th>STATUS</th>
                                            <th>STO</th>
                                            <th>ALLOCATION</th>
                                            <th>RECEIVED</th>
                                            <th>DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ont-stockFf" role="tabpanel" aria-labelledby="ont-stockFf-tab">
                            <div class="text-right mb-3 pr-3">
                                <a href="<?= site_url('wan/ont/exportstock' . $param); ?>" class="btn btn-link animation-on-hover">
                                    <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md" id="ontStockFf" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                                <th>Action</th>
                                            <?php endif ?>
                                            <th>#</th>
                                            <th>MERK</th>
                                            <th>TYPE</th>
                                            <th>SERIAL_NUMBER</th>
                                            <th>STATUS</th>
                                            <th>STO</th>
                                            <th>ALLOCATION</th>
                                            <th>RECEIVED</th>
                                            <th>DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ont-stockOlo" role="tabpanel" aria-labelledby="ont-stockOlo-tab">
                            <div class="text-right mb-3 pr-3">
                                <a href="<?= site_url('wan/ont/exportstockolo' . $param); ?>" class="btn btn-link animation-on-hover">
                                    <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md" id="ontStockOlo" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                                <th>Action</th>
                                            <?php endif ?>
                                            <th>#</th>
                                            <th>MERK</th>
                                            <th>TYPE</th>
                                            <th>SERIAL_NUMBER</th>
                                            <th>STATUS</th>
                                            <th>STO</th>
                                            <th>RECEIVED</th>
                                            <th>DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="installed" role="tabpanel" aria-labelledby="installed-tab">
                            <div class="text-right mb-3 pr-3">
                                <a href="<?= site_url('wan/ont/exportinstalled' . $param); ?>" class="btn btn-link animation-on-hover">
                                    <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md" id="ontInstalled" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                                <th>Action</th>
                                            <?php endif ?>
                                            <th>#</th>
                                            <th>MERK</th>
                                            <th>TYPE</th>
                                            <th>SERIAL_NUMBER</th>
                                            <th>RECEIVED</th>
                                            <th>INSTALLED</th>
                                            <th>DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="return" role="tabpanel" aria-labelledby="return-tab">
                            <div class="text-right mb-3 pr-3">
                                <a href="<?= site_url('wan/ont/exportreturn' . $param); ?>" class="btn btn-link animation-on-hover">
                                    <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md" id="ontReturn" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                                <th>Action</th>
                                            <?php endif ?>
                                            <th>#</th>
                                            <th>MERK</th>
                                            <th>TYPE</th>
                                            <th>SERIAL_NUMBER</th>
                                            <th>STATUS</th>
                                            <th>RECEIVED</th>
                                            <th>RETURN</th>
                                            <th>DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



<!-- The Modal Import-->
<div class="modal fade" id="table_import">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Import</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <!-- Don't Forget to add enctype for input file -->
            <form action="/wan/ont/import" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <a class="btn btn-sm btn-info float-right animation-on-hover" href="<?= base_url('ONT-Format-Import.xlsx'); ?>">
                        <i class="far fa-file-excel"></i> Download Format
                    </a>
                    <label>File Excel</label>
                    <div class="file-drop-area">
                        <span class="btn btn-sm btn-info mr-2">Choose files</span>
                        <span class="file-message">or drag and drop files here</span>
                        <input class="file-input" type="file" name="file_excel" id="file_excel" required>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- dataTables serverside -->
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-3.5.1.js"></script>
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-1.12.1-dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        tableAll = $('#ontAll').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: "<?php echo site_url('wan/ont/listdata' . $param) ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "5, 'desc'" : "4, 'desc'"; ?>
                ]
            ],
            columnDefs: [],
            columns: [<?php if (in_groups(['superadmin', 'admin'])) : ?> {
                        data: 'action',
                        orderable: false
                    },
                <?php endif; ?> {
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
                    data: 'received'
                },
                {
                    data: 'installed'
                },
                {
                    data: 'return'
                },
                {
                    data: 'desc'
                },
            ]
        });

        tableStock = $('#ontStock').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo site_url('wan/ont/listdatastock' . $param) ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "6, 'asc'" : "5, 'asc'"; ?>
                ]
            ],
            columnDefs: [],
            columns: [<?php if (in_groups(['superadmin', 'admin'])) : ?> {
                        data: 'action',
                        orderable: false
                    },
                <?php endif; ?> {
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
                    data: 'allocation'
                },
                {
                    data: 'received'
                },
                {
                    data: 'desc'
                },
            ]
        });

        tableStockff = $('#ontStockFf').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo site_url('wan/ont/listdatastockff' . $param) ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "8, 'desc'" : "7, 'desc'"; ?>
                ]
            ],
            columnDefs: [],
            columns: [<?php if (in_groups(['superadmin', 'admin'])) : ?> {
                        data: 'action',
                        orderable: false
                    },
                <?php endif; ?> {
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
                    data: 'allocation'
                },
                {
                    data: 'received'
                },
                {
                    data: 'desc'
                },
            ]
        });

        tableStockolo = $('#ontStockOlo').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo site_url('wan/ont/listdatastockolo' . $param) ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "5, 'desc'" : "4, 'desc'"; ?>
                ]
            ],
            columnDefs: [],
            columns: [<?php if (in_groups(['superadmin', 'admin'])) : ?> {
                        data: 'action',
                        orderable: false
                    },
                <?php endif; ?> {
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
                    data: 'received'
                },
                {
                    data: 'desc'
                },
            ]
        });

        tableInstalled = $('#ontInstalled').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo site_url('wan/ont/listdatainstalled' . $param) ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "6, 'desc'" : "5, 'desc'"; ?>
                ]
            ],
            columnDefs: [],
            columns: [<?php if (in_groups(['superadmin', 'admin'])) : ?> {
                        data: 'action',
                        orderable: false
                    },
                <?php endif; ?> {
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
                    data: 'received'
                },
                {
                    data: 'installed'
                },
                {
                    data: 'desc'
                },
            ]
        });

        tableReturn = $('#ontReturn').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo site_url('wan/ont/listdatareturn' . $param) ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "7, 'desc'" : "6, 'desc'"; ?>
                ]
            ],
            columnDefs: [],
            columns: [<?php if (in_groups(['superadmin', 'admin'])) : ?> {
                        data: 'action',
                        orderable: false
                    },
                <?php endif; ?> {
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
                    data: 'received'
                },
                {
                    data: 'return'
                },
                {
                    data: 'desc'
                },
            ]
        });


    });
</script>


<?= $this->endSection(); ?>
<?= $this->section('swal-js') ?>
<script src="<?= base_url() ?>/template/node_modules/sweetalert/dist/sweetalert.min.js"></script>
<script>
    let title = $('#flash').data('title');
    let icon = $('#flash').data('icon');
    let flash = $('#flash').data('flash');
    if (flash) {
        swal({
            title: title,
            icon: icon,
            text: flash,
        })
    }

    let csrf_token = "<?= csrf_token(); ?>";
    let csrf_hash = "<?= csrf_hash(); ?>";

    $(document).on('click', '#btnDelete', function(event) {
        event.preventDefault();
        var idont = $(this).data('idont');
        var serial_number = $(this).data('serial_number');
        var url = "<?php echo site_url('/wan/ont/') ?>" + idont

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus ' + serial_number + ' ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('/wan/ont/') ?>" + idont,
                        data: {
                            [csrf_token]: csrf_hash,
                            '_method': 'DELETE',
                        },
                        cache: false,
                        success: function(response) {
                            swal({
                                title: 'Success',
                                icon: 'success',
                                text: 'Data berhasil dihapus.',
                            })
                        },
                        failure: function(jqXHR, textStatus, errorThrown) {
                            swal({
                                title: 'Error!',
                                icon: 'error',
                                text: textStatus,
                            })
                        }
                    })
                    tableAll.ajax.reload();
                    tableStock.ajax.reload();
                    tableStockff.ajax.reload();
                    tableStockolo.ajax.reload();
                    tableInstalled.ajax.reload();
                    tableReturn.ajax.reload();
                }
            });
    });
</script>
<?= $this->endSection(); ?>