<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>



<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('additional/olt'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">DATA OLT <span style="color : crimson">Trash</span></div>
            <div class="section-header-breadcrumb col-2 justify-content-end">
                <div class="btn-group  rounded-pill">
                    <button type="button" id="btnRestoreAll" class="btn btn-sm btn-link" data-toggle="tooltip" data-placement="bottom" title="Restore All"><i class="fas fa-recycle"></i></button>
                    <button type="button" id="btnDeleteAll" class="btn btn-sm btn-link" data-toggle="tooltip" data-placement="bottom" title="Delete All">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- alert -->
        <?php if (session()->getFlashData('success')) : ?>
            <div class="alert alert-success fade show" role="alert">
                <strong>Success! </strong> <?= session()->getFlashData('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeAlert()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashData('error')) : ?>
            <div class="alert alert-danger fade show" role="alert">
                <strong>Error! </strong> <?= session()->getFlashData('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeAlert()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <!-- end alert -->
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body p-2 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="dataOltTrash">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>#</th>
                                    <th>WITEL</th>
                                    <th>STO</th>
                                    <th>HOSTNAME_METRO</th>
                                    <th>IP_METRO</th>
                                    <th>PORT_METRO</th>
                                    <th>HOSTNAME_OLT</th>
                                    <th>IP_OLT</th>
                                    <th>PORT_OLT</th>
                                    <th>PLATFORM</th>

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
        table = $('#dataOltTrash').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo site_url('additional/olt/listdatatrash') ?>",
            order: [],
            columnDefs: [],
            columns: [
                <?php if (in_groups(['superadmin', 'admin'])) : ?> {
                        data: 'action',
                        orderable: false
                    },
                <?php endif; ?> {
                    data: 'number',
                    orderable: false
                },
                {
                    data: 'witel'
                },
                {
                    data: 'idsto'
                },
                {
                    data: 'hostname_metro'
                },
                {
                    data: 'ip_metro'
                },
                {
                    data: 'port_metro'
                },
                {
                    data: 'hostname_olt'
                },
                {
                    data: 'ip_olt'
                },
                {
                    data: 'port_olt'
                },
                {
                    data: 'platform'
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
        var hostname_olt = $(this).data('hostname_olt');
        var url = "<?php echo site_url('/additional/olt/deletetrash/') ?>" + hostname_olt

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus permanen ' + hostname_olt + ' ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            [csrf_token]: csrf_hash,
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
                    table.ajax.reload();
                }
            });
    });

    $(document).on('click', '#btnDeleteAll', function(event) {
        event.preventDefault();
        var url = "<?php echo site_url('/additional/olt/deletetrash') ?>"

        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus permanen seluruh Data Trash ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            [csrf_token]: csrf_hash,
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
                    table.ajax.reload();
                }
            });
    });


    $(document).on('click', '#btnRestore', function(event) {
        event.preventDefault();
        var hostname_olt = $(this).data('hostname_olt');
        var url = "<?php echo site_url('/additional/olt/restore/') ?>" + hostname_olt

        // console.log(url);
        swal({
                title: 'Restore Data',
                text: 'Apakah anda yakin ingin Restore data ' + hostname_olt + ' ?',
                icon: 'info',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        url: url,
                        data: {
                            [csrf_token]: csrf_hash,
                        },
                        cache: false,
                        success: function(response) {
                            swal({
                                title: 'Success',
                                icon: 'success',
                                text: 'Data berhasil direstore.',
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
                    table.ajax.reload();
                }
            });
    });

    $(document).on('click', '#btnRestoreAll', function(event) {
        event.preventDefault();
        var url = "<?php echo site_url('/additional/olt/restore') ?>"

        swal({
                title: 'Restore Data',
                text: 'Apakah anda yakin ingin Restore seluruh Data Trash ?',
                icon: 'info',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        url: url,
                        data: {
                            [csrf_token]: csrf_hash,
                        },
                        cache: false,
                        success: function(response) {
                            swal({
                                title: 'Success',
                                icon: 'success',
                                text: 'Data berhasil direstore.',
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
                    table.ajax.reload();
                }
            });
    });
</script>
<?= $this->endSection(); ?>