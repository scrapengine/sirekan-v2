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
            <div class="font-weight-bold ml-3" style="font-size: 1rem;">DATA NAKER</span></div>

            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                <div class="section-header-button ml-2">
                    <a href="<?= base_url('additional/naker/new'); ?> " class="btn btn-sm btn-link" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-upload"></i></a>
                </div>
            <?php endif ?>
            <div class="section-header-breadcrumb col-2 justify-content-end">
                <div class="btn-group  rounded-pill">
                    <span data-toggle="collapse" data-parent="#accordianId" href="#section1ContentId" aria-expanded="true" aria-controls="section1ContentId">
                        <a type="button" class="btn btn-sm btn-link text-info" data-toggle="tooltip" data-placement="bottom" title="Filter"> <i class="fas fa-filter" aria-hidden="true"></i></a>
                    </span>
                    <?php if (in_groups(['superadmin', 'admin'])) : ?>
                        <a href="<?= base_url('additional/naker/trash'); ?> " class="btn btn-sm btn-link text-danger" data-toggle="tooltip" data-placement="bottom" title="Data Trash"><i class="far fa-trash-alt"></i></a>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php if (session()->getFlashData('success')) : ?>
            <div id="flash" data-icon="success" data-title="Success!" data-flash="<?= session()->getFlashData('success'); ?>"></div>
        <?php endif; ?>

        <?php if (session()->getFlashData('error')) : ?>
            <div id="flash" data-icon="error" data-title="Error!" data-flash="<?= session()->getFlashData('error'); ?>"></div>
        <?php endif; ?>
        <div id="accordianId" class="mt-3" role="tablist" aria-multiselectable="true">
            <div class="mb-0 bg-transparent">
                <div id="section1ContentId" class="collapse" role="tabpanel" aria-labelledby="section1HeaderId">
                    <div class="card-body">
                        <form method="GET" name="SearchCloseForm" id="SearchCloseForm" onkeydown="return event.key != 'Enter';">

                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-lg-3">
                                        <select class="form-control  d-block m-2 text-primary" name="choice[]" id="choice">
                                            <option value="" disabled selected>Select Data</option>
                                            <option value="nama">Nama</option>
                                            <option value="divisi">Divisi</option>
                                            <option value="jobdesk">Jobdesk</option>
                                            <option value="idsto">STO</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <input class="form-control  d-block m-2" placeholder="Value" name="values[]" type="text" autocomplete="off">
                                    </div>
                                </div>
                                <div id="penambahanInput" class="row  justify-content-center">
                                </div>
                                <div class="col-12 d-flex justify-content-center mt-3">
                                    <div class="row">
                                        <a name="" id="exportclose" href="javascript:void(0)" class="btn btn-sm btn-info m-2">Export</a>
                                        <a name="" id="submitSearchClose" href="javascript:void(0)" class="btn btn-sm btn-info m-2">Search Table</a>
                                        <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                            <a href="javascript:void(0)" id="importData" class="btn btn-sm btn-primary m-2" data-toggle="modal" data-target="#table_import">Import</a>
                                        <?php endif ?>
                                        <a name="" id="addColumn" href="javascript:void(0)" class="btn btn-sm btn-success m-2"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                                <p class="text-center mt-3"> Click + to show another search parameter</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-primary" id="table-contents">
                <div class="card-body p-2 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm" id="dataNaker" style="width: 100%">
                            <thead>
                                <tr>
                                    <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                        <th>Action</th>
                                    <?php endif ?>
                                    <th>#</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Divisi</th>
                                    <th>Jobdesk</th>
                                    <th>No_HP</th>
                                    <th>STO_Handling_Area</th>
                                    <th>Labor</th>
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
            <form action="/additional/naker/import" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <a class="btn btn-sm btn-link float-right text-info" href="<?= base_url('NAKER-Format-Import.xlsx'); ?>">
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
        table = $('#dataNaker').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            ajax: "<?php echo site_url('additional/naker/listdata' . $param) ?>",
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
                    data: 'nik'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'divisi'
                },
                {
                    data: 'jobdesk'
                },
                {
                    data: 'no_hp'
                },
                {
                    data: 'idsto'
                },
                {
                    data: 'labor'
                },
            ]
        });

        $("#addColumn").click(function() {
            var x = Math.floor(Math.random() * 100);
            $('#penambahanInput').append(
                '<div class="col-lg-12" id="choiceDiv' + x + '">' +
                '<div class="row  justify-content-center">' +
                '<div class="col-lg-3">' +
                '<select class="form-control  d-block m-2 text-primary" name="choice[]" id="choice' + x + '">' +
                '<option value="cancel" selected>Cancel Search</option>' +
                '<option value="" disabled selected>Select Data</option>' +
                '<option value="nama">Nama</option>' +
                '<option value="divisi">Divisi</option>' +
                '<option value="jobdesk">Jobdesk</option>' +
                '<option value="idsto">STO</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-lg-4">' +
                '<input class="form-control  d-block m-2 dateselesai" placeholder="Value" name="values[]" type="text" autocomplete="off">' +
                '</div>' +
                // '<div class="col-lg-1 p-0">' +
                // '<a name="" class="text-danger d-block m-2" id="remove' + x + '" href="javascript:void(0)" role="button"><i class="fa fa-minus" aria-hidden="true"></i></a>' +
                // '</div>' +
                '</div>' +
                '</div>'
            );

            $("#remove" + x).on("click", function() {
                console.log('<select class="form-control  d-block m-2 text-primary" name="choice[]" id="choice' + x + '">')
                $("#choiceDiv" + x).remove();
            })

            $("#choice" + x).change(function() {
                if ($("#choice" + x).val() == "cancel") {
                    $("#choiceDiv" + x).remove();
                }
            });

        });

        $("#exportclose").click(function() {
            document.getElementById('SearchCloseForm').action = "<?= base_url('additional/naker/export'); ?>";
            document.getElementById('SearchCloseForm').submit();
        });

        $("#submitSearchClose").click(function() {

            $('html, body').animate({
                scrollTop: $("#table-contents").offset().top
            }, 500);

            $('#dataNaker').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('additional/naker/listdata' . $param) ?>" + "?" + $('#SearchCloseForm').serialize(),
                order: [],
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
                        data: 'nik'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'divisi'
                    },
                    {
                        data: 'jobdesk'
                    },
                    {
                        data: 'no_hp'
                    },
                    {
                        data: 'idsto'
                    },
                    {
                        data: 'labor'
                    },
                ]
            });
            // table.ajax.reload();
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
        var idnaker = $(this).data('idnaker');
        var nama = $(this).data('nama');
        var url = "<?php echo site_url('/additional/naker/') ?>" + idnaker

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus ' + nama + ' ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('/additional/naker/') ?>" + idnaker,
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