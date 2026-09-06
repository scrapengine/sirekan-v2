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
            <div class="font-weight-bold ml-3" style="font-size: 1rem;">DATA METRO</span></div>

            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                <div class="section-header-button ml-2">
                    <a href="<?= base_url('additional/metro/new'); ?> " class="btn btn-sm btn-link" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-upload"></i></a>
                </div>
                <div class="section-header-breadcrumb col-2 justify-content-end">
                    <div class="btn-group  rounded-pill">
                        <span data-toggle="modal" data-target="#table_import">
                            <a type="button" class="btn btn-sm btn-link text-info" data-toggle="tooltip" data-placement="bottom" title="Import Excel"> <i class="fas fa-file-upload" aria-hidden="true"></i></a>
                        </span>
                        <a href="<?= base_url('additional/metro/trash'); ?> " class="btn btn-sm btn-link text-danger" data-toggle="tooltip" data-placement="bottom" title="Data Trash"><i class="far fa-trash-alt"></i></a>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <?php if (session()->getFlashData('success')) : ?>
            <div id="flash" data-icon="success" data-title="Success!" data-flash="<?= session()->getFlashData('success'); ?>"></div>
        <?php endif; ?>

        <?php if (session()->getFlashData('error')) : ?>
            <div id="flash" data-icon="error" data-title="Error!" data-flash="<?= session()->getFlashData('error'); ?>"></div>
        <?php endif; ?>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body p-2 mt-3">
                    <div class="text-right mb-3 pr-3">
                        <a href="<?= site_url('additional/metro/export' . $param); ?>" class="btn btn-link animation-on-hover">
                            <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm" id="dataMetro">
                            <thead>
                                <tr>
                                    <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                        <th>Action</th>
                                    <?php endif ?>
                                    <th>#</th>
                                    <th>STO</th>
                                    <th>HOSTNAME_METRO</th>
                                    <th>IP_METRO</th>
                                </tr>
                            </thead>
                        </table>
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
            <form action="/additional/metro/import" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <a class="btn btn-sm btn-link float-right text-info" href="<?= base_url('METRO-Format-Import.xlsx'); ?>">
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
        table = $('#dataMetro').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: "<?php echo site_url('additional/metro/listdata') ?>",
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
                    data: 'idsto'
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
        var hostname_metro = $(this).data('hostname_metro');
        var url = "<?php echo site_url('/additional/metro/') ?>" + hostname_metro

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus ' + hostname_metro + ' ?',
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
                    table.ajax.reload();
                }
            });
    });
</script>
<?= $this->endSection(); ?>