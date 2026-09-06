<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>



<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('wan/fulfillment'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">DATA FULFILLMENT <span style="color : crimson">Trash</span></div>
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

        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body p-2 mt-5">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm table-bordered" id="fulfillmentWanTrash" style="width: 100%">
                            <thead>
                                <tr style="height: 50px;">
                                    <th>Action</th>
                                    <th>#</th>
                                    <th>TANGGAL</th>
                                    <th>STO</th>
                                    <th>LAYANAN</th>
                                    <th>NO_ORDER</th>
                                    <th>ORDER_TYPE</th>
                                    <th>NAMA_PELANGGAN</th>
                                    <th>ALAMAT</th>
                                    <th>STATUS</th>
                                    <th>KETERANGAN</th>
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
            <form action="/wan/fulfillment/deletetrash/<?php foreach ($dataFfwan as $d) : ?><?= $d->idff; ?><?php endforeach; ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    Apakah anda yakin ingin menghapus permanen seluruh data Trash?
                    <br>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn btn-danger" name="deletenodeb">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- The Modal Detail-->
<div class="modal fade" id="modaldetailData">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">Fulfillment Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="bodymodal_detailData">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- dataTables serverside -->
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-3.5.1.js"></script>
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-1.12.1-dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        table = $('#fulfillmentWanTrash').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: "<?php echo site_url('wan/fulfillment/listdatatrash') ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "2, 'desc'" : "1, 'desc'"; ?>
                ]
            ],
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
                    data: 'tanggal'
                },
                {
                    data: 'idsto'
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
                    data: 'alamat'
                },
                {
                    data: 'status'
                },
                {
                    data: 'desc'
                },
            ]
        });

        $('#deleteModal').on('show.bs.modal', function(event) {
            var idff = $(event.relatedTarget).data('idff');
            var no_order = $(event.relatedTarget).data('no_order');
            var nama = $(event.relatedTarget).data('nama');

            var bodyModal =
                '<form action="/wan/fulfillment/deletetrash/' + idff + '"' + ' method="post">' +
                '<?= csrf_field(); ?>' +
                '<div class="modal-body">' +
                'Apakah anda yakin ingin menghapus permanen ' + no_order + ' - ' + nama + ' ?' +
                '<br>' +
                '</div>' +
                '<div class="modal-footer justify-content-end">' +
                '<button type="submit" class="btn btn-danger">Delete</button>' +
                '</div>' +
                '</form>';


            $(this).find("#editModal").html(bodyModal);

        });
    });

    function showDetail(idff) {
        $('#bodymodal_detailData').html('<h6 class="text-center">Processing</h6>');
        $.ajax({
            url: "<?= site_url('/wan/fulfillment/detaildata'); ?>",
            data: "idff=" + idff,
            dataType: "html",
            success: function(response) {
                $('#bodymodal_detailData').empty('Processing');
                $('#bodymodal_detailData').append(response);
            }
        });
    }
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
        var idff = $(this).data('idff');
        var no_order = $(this).data('no_order');
        var nama = $(this).data('nama');
        var url = "<?php echo site_url('/wan/fulfillment/deletetrash/') ?>" + idff

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus permanen ' + no_order + ' - ' + nama + ' ?',
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
        var url = "<?php echo site_url('/wan/fulfillment/deletetrash') ?>"

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
        var url = "<?php echo site_url('/wan/fulfillment/restore') ?>"

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