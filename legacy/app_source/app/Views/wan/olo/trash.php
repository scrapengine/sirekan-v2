<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>


<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('wan/olo'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">DATA OLO <span style="color : crimson">Trash</span></div>
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
                        <table class="table table-striped table-hover table-sm table-bordered" id="oloWanTrash" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>#</th>
                                    <th>STO</th>
                                    <th>Layanan</th>
                                    <th>Service ID</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Alamat</th>
                                    <th>Coordinate</th>
                                    <th>Hostname Metro</th>
                                    <th>IP Metro</th>
                                    <th>Port Metro</th>
                                    <th>Hostname OLT</th>
                                    <th>IP OLT</th>
                                    <th>Port Onu</th>
                                    <th>Vlan</th>
                                    <th>Hostname ONT</th>
                                    <th>IP ONT</th>
                                    <th>ONT Type</th>
                                    <th>Serial Number</th>
                                    <th>ODC</th>
                                    <th>ODP</th>
                                    <th>Desc</th>
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
<!-- The Modal Detail-->
<div class="modal fade" id="modaldetailData">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">OLO Details</h5>
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
        table = $('#oloWanTrash').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: "<?php echo site_url('wan/olo/listdatatrash') ?>",
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
                    data: 'idsto_olo'
                },
                {
                    data: 'layanan'
                },
                {
                    data: 'service_id'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'alamat_olo'
                },
                {
                    data: 'tag_lokasi'
                },
                {
                    data: 'hostname_metro_olo'
                },
                {
                    data: 'ip_metro'
                },
                {
                    data: 'port_metro'
                },
                {
                    data: 'hostname_olt_olo'
                },
                {
                    data: 'ip_olt'
                },
                {
                    data: 'port_onu'
                },
                    {
                        data: 'vlan'
                    },
                {
                    data: 'hostname_ont'
                },
                {
                    data: 'ip_ont'
                },
                {
                    data: 'ont_type'
                },
                {
                    data: 'serial_number'
                },
                {
                    data: 'odc'
                },
                {
                    data: 'odp'
                },
                {
                    data: 'desc'
                },
            ]
        });
    });

    function showDetail(idolo) {
        $('#bodymodal_detailData').html('<h6 class="text-center">Processing</h6>');
        $.ajax({
            url: "<?= site_url('/wan/olo/detaildata'); ?>",
            data: "idolo=" + idolo,
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
        var idolo = $(this).data('idolo');
        var service_id = $(this).data('service_id');
        var nama = $(this).data('nama');
        var url = "<?php echo site_url('/wan/olo/deletetrash/') ?>" + idolo

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus permanen ' + service_id + ' - ' + nama + ' ?',
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
        var url = "<?php echo site_url('/wan/olo/deletetrash') ?>"

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
        var url = "<?php echo site_url('/wan/olo/restore') ?>"

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