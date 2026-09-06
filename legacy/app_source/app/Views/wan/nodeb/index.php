<?= $this->extend('layout/default') ?>

<?= $this->section('selectric-css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/selectric/public/selectric.css">
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

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

        <?php if (session()->getFlashData('success')) : ?>
            <div id="flash" data-icon="success" data-title="Success!" data-flash="<?= session()->getFlashData('success'); ?>"></div>
        <?php endif; ?>

        <?php if (session()->getFlashData('error')) : ?>
            <div id="flash" data-icon="error" data-title="Error!" data-flash="<?= session()->getFlashData('error'); ?>"></div>
        <?php endif; ?>

        <div class="section-body">
            <div id="accordianId" class="mt-3" role="tablist" aria-multiselectable="true">
                <div class="card mb-3">
                    <div class="card-header" role="tab" id="section1HeaderId">
                        <h4>
                            <a data-toggle="collapse" data-parent="#accordianId" href="#section1ContentId" aria-expanded="true" aria-controls="section1ContentId"> <i class="fas fa-filter" aria-hidden="true"></i> Filter Data</a>
                        </h4>
                    </div>
                    <div id="section1ContentId" class="collapse" role="tabpanel" aria-labelledby="section1HeaderId">
                        <div class="card-body">
                            <form method="GET" name="SearchCloseForm" id="SearchCloseForm" onkeydown="return event.key != 'Enter';">

                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <select class="form-control d-block m-2" name="dateParam" id="dateParam">
                                                <option value="on_air">On Air</option>
                                                <option value="datanodeb.created_at">Created at</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <input class="form-control m-2" placeholder="from date" id="fromdate" name="fromdate" type="date" value="<?= old('fromdate', date('Y-m-01')) ?>" autocomplete="off">
                                        </div>
                                        <div class="col-lg-4">
                                            <input class="form-control m-2" placeholder="Until Date" id="untildate" name="untildate" type="date" value="<?= old('untildate', date('Y-m-d')) ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div id="penambahanInput" class="row  justify-content-center">
                                    </div>
                                    <div class="col-12 d-flex justify-content-center">
                                        <div class="row">
                                            <a name="" id="exportclose" href="javascript:void(0)" class="btn btn-sm btn-info m-2">Export</a>
                                            <a name="" id="submitSearchClose" href="javascript:void(0)" class="btn btn-sm btn-info m-2">Search Table</a>
                                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                                <a href="" id="importData" class="btn btn-sm btn-primary m-2" data-toggle="modal" data-target="#table_import">Import</a>
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

            <div class="card card-primary" id="table-contents">
                <div class=" card-header p-0">
                    <div class="col-10 pr-0">
                        <h4>DATA NODE-B
                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                <a href="<?= base_url('wan/nodeb/new'); ?> " class="btn btn-sm btn-round btn-primary ml-3" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-plus"></i></a>
                            <?php endif ?>
                        </h4>
                    </div>
                    <?php if (in_groups(['superadmin', 'admin'])) : ?>
                        <div class="col-2 text-right pl-0">
                            <a href="<?= base_url('wan/nodeb/trash'); ?> " class="btn btn-sm rounded-pill btn-outline-danger" data-toggle="tooltip" data-placement="bottom" title="Data Trash"><i class="far fa-trash-alt"></i></a>
                        </div>
                    <?php endif ?>
                </div>
                <div class="card-body p-2 mt-3">
                    <div class="dropdown mb-2 text-right">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle text-dark" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Columns
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu" role="menu" style="max-height: 250px; overflow-y: scroll;">
                            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                <a type="button" class="dropdown-item toggle-vis active" data-column="0"><span>Action</span></a>
                            <?php endif ?>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "1" : "0" ?>"><span>Nomor</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "2" : "1" ?>"><span>STO</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "3" : "2" ?>"><span>Site ID</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "4" : "3" ?>"><span>Site Name</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "5" : "4" ?>"><span>Hostname Metro</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "6" : "5" ?>"><span>IP Metro</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "7" : "6" ?>"><span>Port Metro</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "8" : "7" ?>"><span>Hostname OLT</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "9" : "8" ?>"><span>IP OLT</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "10" : "9" ?>"><span>Port Onu</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "11" : "10" ?>"><span>Hostname ONT</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "12" : "11" ?>"><span>IP ONT</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "13" : "12" ?>"><span>ONT Type</span></a>
                            <a type="button" class="dropdown-item toggle-vis active" data-column="<?= (in_groups(['superadmin', 'admin'])) ? "14" : "13" ?>"><span>Serial Number</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "15" : "14" ?>"><span>ODC</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "16" : "15" ?>"><span>ODP</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "17" : "16" ?>"><span>Coordinate</span></a>
                            <a type="button" class="dropdown-item toggle-vis " data-column="<?= (in_groups(['superadmin', 'admin'])) ? "18" : "17" ?>"><span>On Air</span></a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm table-bordered" id="nodebWan" style="width: 100%">
                            <thead>
                                <tr>
                                    <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                        <th>Action</th>
                                    <?php endif ?>
                                    <th>#</th>
                                    <th>STO</th>
                                    <th>Site ID</th>
                                    <th>Site Name</th>
                                    <th>Hostname Metro</th>
                                    <th>IP Metro</th>
                                    <th>Port Metro</th>
                                    <th>Hostname OLT</th>
                                    <th>IP OLT</th>
                                    <th>Port Onu</th>
                                    <th>Hostname ONT</th>
                                    <th>IP ONT</th>
                                    <th>ONT Type</th>
                                    <th>Serial Number</th>
                                    <th>ODC</th>
                                    <th>ODP</th>
                                    <th>Coordinate</th>
                                    <th>On Air</th>
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
            <form action="/wan/nodeb/import" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <a class="btn btn-sm btn-link float-right text-info" href="<?= base_url('NODEB-Format-Import.xlsx'); ?>">
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
        var form = $("#SearchCloseForm").serialize();
        var keyword = $('#keyword').val();
        var fromdate = $('#fromdate').val();
        var untildate = $('#untildate').val();
        table = $('#nodebWan').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            ajax: "<?php echo site_url('wan/nodeb/listdata' . $param) ?>",
            order: [
                [
                    <?= (in_groups(['superadmin', 'admin'])) ? "18, 'desc'" : "17, 'desc'"; ?>
                ]
            ],
            columnDefs: [],
            buttons: [
                'colvis',
                'copyHtml5',
                'csvHtml5',
                'excelHtml5',
                'pdfHtml5',
                'print'
            ],
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
                    data: 'idsto_nodeb'
                },
                {
                    data: 'site_id'
                },
                {
                    data: 'site_name'
                },
                {
                    data: 'hostname_metro_nodeb'
                },
                {
                    data: 'ip_metro',
                    visible: false
                },
                {
                    data: 'port_metro'
                },
                {
                    data: 'hostname_olt_nodeb',
                    visible: false
                },
                {
                    data: 'ip_olt'
                },
                {
                    data: 'port_onu'
                },
                {
                    data: 'hostname_ont',
                    visible: false
                },
                {
                    data: 'ip_ont'
                },
                {
                    data: 'ont_type',
                    visible: false
                },
                {
                    data: 'serial_number'
                },
                {
                    data: 'odc',
                    visible: false
                },
                {
                    data: 'odp',
                    visible: false
                },
                {
                    data: 'tikor_site',
                    visible: false
                },
                {
                    data: 'on_air',
                    visible: false
                },
            ]
        });


        // $('a.toggle-vis').on('click', function(e) {
        //     e.preventDefault();

        //     // Get the column API object
        //     var column = table.column($(this).attr('data-column'));

        //     // Toggle the visibility
        //     column.visible(!column.visible());
        // });

        $('a.toggle-vis').on('click', function(e) {
            e.preventDefault();
            // Get the column API object
            var columnnumb = $(this).attr('data-column');
            var column = table.column(columnnumb);
            // Toggle the visibility
            column.visible(!column.visible());

            if ($('a.toggle-vis[data-column="' + columnnumb + '"').hasClass('active')) {
                $('a.toggle-vis[data-column="' + columnnumb + '"').removeClass('active')
            } else {
                $('a.toggle-vis[data-column="' + columnnumb + '"').addClass('active')
            }

            // checkcolumnvis();

        });


        function checkcolumnvis() {
            table.columns().every(function() {
                var columnnumb = this.index();

                if ((this.responsiveHidden()) && (this.visible())) {
                    $('a.toggle-vis[data-column="' + columnnumb + '"').removeClass('toggle-hidden');
                    $('a.toggle-vis[data-column="' + columnnumb + '"').addClass('toggle-shown');
                } else {
                    $('a.toggle-vis[data-column="' + columnnumb + '"').removeClass('toggle-shown');
                    $('a.toggle-vis[data-column="' + columnnumb + '"').addClass('toggle-hidden');
                }
            });
        }

        // checkcolumnvis();

        // $(window).on('resize', function() {
        //     checkcolumnvis();
        // });

        $("#addColumn").click(function() {
            var x = Math.floor(Math.random() * 100);
            $('#penambahanInput').append(
                '<div class="col-lg-10" id="choiceDiv' + x + '">' +
                '<div class="row  justify-content-center">' +
                '<div class="col-lg-4">' +
                '<select class="form-control  d-block m-2 text-primary" name="choice[]" id="choice' + x + '">' +
                '<option value="cancel" selected>Cancel Search</option>' +
                '<option value="" disabled selected>Select Data</option>' +
                '<option value="site_id">Site ID</option>' +
                '<option value="site_name">Site Name</option>' +
                '<option value="datanodeb.idsto">STO</option>' +
                '<option value="datanodeb.hostname_metro">Hostname Metro</option>' +
                '<option value="datanodeb.hostname_olt">Hostname OLT</option>' +
                '<option value="ip_olt">IP OLT</option>' +
                '<option value="serial_number">SN</option>' +
                '<option value="odc">ODC</option>' +
                '<option value="odp">ODP</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-lg-5">' +
                '<input class="form-control  d-block m-2 dateselesai" placeholder="Value" name="values[]" type="text" autocomplete="off">' +
                '</div>' +
                '<div class="col-lg-1 p-0">' +
                '<a name="" class="text-danger d-block m-2" id="remove' + x + '" href="javascript:void(0)" role="button"><i class="fa fa-minus" aria-hidden="true"></i></a>' +
                '</div>' +
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
            var form = $("#SearchCloseForm").serialize();
            var fromdate = document.querySelector("input[name='fromdate']").value;
            var untildate = document.querySelector("input[name='untildate']").value;

            document.getElementById('SearchCloseForm').action = "<?= base_url('wan/nodeb/export'); ?>";
            document.getElementById('SearchCloseForm').submit();
        });
        $("#submitSearchClose").click(function() {
            var form = $("#SearchCloseForm").serialize();
            var fromdate = document.querySelector("input[name='fromdate']").value;
            var untildate = document.querySelector("input[name='untildate']").value;

            $('html, body').animate({
                scrollTop: $("#table-contents").offset().top
            }, 500);

            table = $('#nodebWan').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: "<?php echo site_url('wan/nodeb/listdata' . $param) ?>" + "?" + $('#SearchCloseForm').serialize(),
                order: [
                    [
                        <?= (in_groups(['superadmin', 'admin'])) ? "18, 'desc'" : "17, 'desc'"; ?>
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
                        data: 'idsto_nodeb'
                    },
                    {
                        data: 'site_id'
                    },
                    {
                        data: 'site_name'
                    },
                    {
                        data: 'hostname_metro_nodeb'
                    },
                    {
                        data: 'ip_metro',
                        visible: false
                    },
                    {
                        data: 'port_metro'
                    },
                    {
                        data: 'hostname_olt_nodeb',
                        visible: false
                    },
                    {
                        data: 'ip_olt'
                    },
                    {
                        data: 'port_onu'
                    },
                    {
                        data: 'hostname_ont',
                        visible: false
                    },
                    {
                        data: 'ip_ont'
                    },
                    {
                        data: 'ont_type',
                        visible: false
                    },
                    {
                        data: 'serial_number'
                    },
                    {
                        data: 'odc',
                        visible: false
                    },
                    {
                        data: 'odp',
                        visible: false
                    },
                    {
                        data: 'tikor_site',
                        visible: false
                    },
                    {
                        data: 'on_air',
                        visible: false
                    },
                ]
            });
            // table.ajax.reload();
        });
    });

    function showDetail(idnodeb) {
        $('#bodymodal_detailData').html('<h6 class="text-center">Processing</h6>');
        $.ajax({
            url: "<?= site_url('/wan/nodeb/detaildata'); ?>",
            data: "idnodeb=" + idnodeb,
            dataType: "html",
            success: function(response) {
                $('#bodymodal_detailData').empty();
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
        var idnodeb = $(this).data('idnodeb');
        var site_id = $(this).data('site_id');
        var site_name = $(this).data('site_name');
        var url = "<?php echo site_url('/wan/nodeb/') ?>" + idnodeb

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus ' + site_id + ' - ' + site_name + ' ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('/wan/nodeb/') ?>" + idnodeb,
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
                    table.ajax.reload();
                }
            });
    });
</script>
<?= $this->endSection(); ?>

<?= $this->section('selectric-js') ?>
<script src="<?= base_url() ?>/template/node_modules/selectric/public/jquery.selectric.min.js"></script>
<?= $this->endSection(); ?>