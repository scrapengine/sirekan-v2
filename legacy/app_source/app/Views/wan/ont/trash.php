<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>



<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('wan/ont'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">DATA ONT <span style="color : crimson">Trash</span></div>
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
                <div class="card-body p-2 mt-5">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-md" id="ontTrash">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>#</th>
                                    <th>MERK</th>
                                    <th>TYPE</th>
                                    <th>SERIAL_NUMBER</th>
                                    <th>STATUS</th>
                                    <th>STO</th>
                                    <th>ALLOCATION</th>
                                    <th>RECEIVED</th>
                                    <th>INSTALLED</th>
                                    <th>RETURN</th>
                                    <th>DESCRIPTION</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-right">
                </div>
            </div>
        </div>
    </section>
</div>


<!-- The Modal Delete-->
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Data</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div id="editModal"></div>
        </div>
    </div>
</div>


<!-- dataTables serverside -->
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-3.5.1.js"></script>
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-1.12.1-dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        table = $('#ontTrash').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: "<?php echo site_url('wan/ont/listdatatrash') ?>",
            order: [],
            columnDefs: [{
                    targets: [0, 1],
                    orderable: false
                }, //first column is not orderable.
            ],
            columns: [{
                    data: 'action'
                },
                {
                    data: 'number'
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

        $('#deleteModal').on('show.bs.modal', function(event) {
            var idont = $(event.relatedTarget).data('idont');
            var serial_number = $(event.relatedTarget).data('serial_number');

            var bodyModal =
                '<form action="/wan/ont/deletetrash/' + idont + '"' + ' method="post">' +
                '<?= csrf_field(); ?>' +
                '<div class="modal-body">' +
                'Apakah anda yakin ingin menghapus permanen ' + serial_number + ' ?' +
                '<br>' +
                '</div>' +
                '<div class="modal-footer justify-content-end">' +
                '<button type="submit" class="btn btn-danger">Delete</button>' +
                '</div>' +
                '</form>';


            $(this).find("#editModal").html(bodyModal);

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
        var url = "<?php echo site_url('/wan/ont/deletetrash/') ?>" + idont

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus permanen ' + serial_number + ' ?',
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
        var url = "<?php echo site_url('/wan/ont/deletetrash') ?>"

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

    $(document).on('click', '#btnRestoreAll', function(event) {
        event.preventDefault();
        var url = "<?php echo site_url('/wan/ont/restore') ?>"

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