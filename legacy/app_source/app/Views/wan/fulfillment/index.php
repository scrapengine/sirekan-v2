<?= $this->extend('layout/default') ?>

<?= $this->section('chocolat-css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/chocolat/dist/css/chocolat.css">
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

<?php $request = \Config\Services::request(); ?>

<!-- trigger keyword -->
<?php
$request = \Config\Services::request();
$keyword = $request->getGet("keyword");
$fromdate = $request->getGet("fromdate");
$untildate = $request->getGet("untildate");
if ($fromdate != '') {
    $param = "?keyword=" . $keyword . "&fromdate=" . $fromdate . "&untildate=" . $untildate;
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
                                            <select class="form-control d-block m-2" name="" id="">
                                                <option value="Status_Date">Order Date</option>
                                            </select>
                                        </div>
                                        <!-- <div class="col-lg-4">
                                            <input class="form-control m-2" placeholder="Search" id="keyword" name="keyword" type="text" value="<?= old('keyword', $request->getGet('keyword')) ?>" autocomplete="off">
                                        </div> -->
                                        <div class="col-lg-4">
                                            <input class="form-control m-2" placeholder="from date" id="fromdate" name="fromdate" type="date" value="<?= old('fromdate', date('Y-m-01')) ?>" autocomplete="off">
                                        </div>
                                        <div class="col-lg-4">
                                            <input class="form-control m-2" placeholder="Until Date" id="untildate" name="untildate" type="date" value="<?= old('untildate', date('Y-m-d')) ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div id="penambahanInput" class="row  justify-content-center">
                                    </div>
                                    <!-- <div class="row">

                                        <div class="col-12 d-flex justify-content-center mt-5 mb-3 ">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <select class="form-control d-block" name="limrotab" id="">
                                                        <option value="Reported_Date">Limit Row Table</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input class="form-control d-block" placeholder="" name="limit" value="10000" type="number" autocomplete="off">
                                                </div>
                                                <div class="col-lg-6 mt-2">
                                                    <select class="form-control d-block" name="filter" id="">
                                                        <option value="Reported_Date">Select Filter</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 mt-2">
                                                    <div class="form-group">
                                                        <input type="hidden" name="Product" value="closed">
                                                        <select class="form-control d-block" name="tipe" id="tipeD">
                                                            <option value="all">All Data</option>
                                                            <option value="open">Open</option>
                                                            <option value="reject">Reject</option>
                                                            <option value="close">Close</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                    <div class="col-12 d-flex justify-content-center">
                                        <div class="row">
                                            <!-- <input type="text" name="tipeDetail" value="all" hidden id="tipeDetail"> -->
                                            <a name="" id="exportclose" href="javascript:void(0)" class="btn btn-sm btn-info m-2">Export</a>
                                            <a name="" id="submitSearchClose" href="javascript:void(0)" class="btn btn-sm btn-info m-2">Search Table</a>
                                            <!-- <a name="" id="apiClose" href="javascript:void(0)" class="btn btn-sm btn-primary m-2">Get Api</a> -->
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
        </div>
        <div class="card card-primary" id="table-contents">
            <div class="card-header p-0">
                <div class="col-10 pr-0">
                    <h4>DATA FULFILLMENT
                        <?php if (in_groups(['superadmin', 'admin'])) : ?>
                            <a href="<?= base_url('wan/fulfillment/new'); ?> " class="btn btn-sm btn-round btn-primary ml-3" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-plus"></i></a>
                        <?php endif ?>
                    </h4>
                </div>
                <?php if (in_groups(['superadmin', 'admin'])) : ?>
                    <div class="col-2 text-right pl-0">
                        <a href="<?= base_url('wan/fulfillment/trash'); ?> " class="btn btn-sm rounded-pill btn-outline-danger" data-toggle="tooltip" data-placement="bottom" title="Data Trash"><i class="far fa-trash-alt"></i></a>
                    </div>
                <?php endif ?>
            </div>
            <div class="card-body p-2 mt-3">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm table-bordered" id="fulfillmentWan" style="width: 100%">
                        <thead class="bg-info bg-gradient text-white">
                            <tr style="height: 50px;">
                                <?php if (in_groups(['superadmin', 'admin'])) : ?>
                                    <th>Action</th>
                                <?php endif ?>
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
            <form action="/wan/fulfillment/import" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <a class="btn btn-sm btn-link float-right text-info" href="<?= base_url('FULFILLMENT-Format-Import.xlsx'); ?>">
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
            <div class="modal-body" id="bodymodal_detailData"></div>

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
        var form = $("#SearchCloseForm").serialize();
        var keyword = $('#keyword').val();
        var fromdate = $('#fromdate').val();
        var untildate = $('#untildate').val();
        table = $('#fulfillmentWan').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            ajax: {
                url: "<?php echo site_url('wan/fulfillment/listdata' . $param) ?>",
            },

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
                '<form action="/wan/fulfillment/' + idff + '"' + ' method="post">' +
                '<?= csrf_field(); ?>' +
                '<input type="hidden" name="_method" value="DELETE">' +
                '<div class="modal-body">' +
                'Apakah anda yakin ingin menghapus ' + no_order + ' - ' + nama + ' ?' +
                '<br>' +
                '</div>' +
                '<div class="modal-footer justify-content-end">' +
                '<button type="submit" class="btn btn-danger">Delete</button>' +
                '</div>' +
                '</form>';


            $(this).find("#editModal").html(bodyModal);

        });

        $("#addColumn").click(function() {
            var x = Math.floor(Math.random() * 100);
            $('#penambahanInput').append(
                '<div class="col-lg-10" id="choiceDiv' + x + '">' +
                '<div class="row  justify-content-center">' +
                '<div class="col-lg-4">' +
                '<select class="form-control  d-block m-2 text-primary" name="choice[]" id="choice' + x + '">' +
                '<option value="cancel" selected>Cancel Search</option>' +
                '<option value="" disabled selected>Select Data</option>' +
                '<option value="layanan">Layanan</option>' +
                '<option value="no_order">No Order</option>' +
                '<option value="ffwan.idsto">STO</option>' +
                '<option value="serial_number">Serial Number</option>' +
                '<option value="nama">Nama Pelanggan</option>' +
                '<option value="status">Status</option>' +
                // '<option value="Incident">Ticket</option>' +
                // '<option value="Summary">Summary</option>' +
                // '<option value="datasto.idsto">STO</option>' +
                // '<option value="Reg_Telkom">Reg Telkom</option>' +
                // '<option value="Reg_Tsel">Reg Tsel</option>' +
                // '<option value="Status">Status</option>' +
                // '<option value="Site_Id">Site Id</option>' +
                // '<option value="Product_Name">Product Name</option>' +
                // '<option value="Gangguan">Gangguan</option>' +
                // '<option value="Witel">Witel</option>' +
                // '<option value="Owner_Solution_Fusion">Owner Solution</option>' +
                // '<option value="owner_rca_fusion">Owner rca_fusion</option>' +
                // '<option value="Actual_Solution_Fusion">Actual Solution</option>' +
                // '<option value="Detail_Actual_Solution_Fusion">Detail_Actual_Solution_WeCare</option>' +
                // '<option value="detail_rca_fusion">detail_rca_fusion</option>' +
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

            document.getElementById('SearchCloseForm').action = "<?= base_url('wan/fulfillment/export'); ?>";
            document.getElementById('SearchCloseForm').submit();
        });
        $("#submitSearchClose").click(function() {
            var form = $("#SearchCloseForm").serialize();
            var fromdate = document.querySelector("input[name='fromdate']").value;
            var untildate = document.querySelector("input[name='untildate']").value;


            $('html, body').animate({
                scrollTop: $("#table-contents").offset().top
            }, 500);
            // document.getElementById('SearchCloseForm').action = "#table-contents";
            // document.getElementById('SearchCloseForm').submit();

            $("#headerTable").text("PROCESSING");
            $("#analyzeCard").attr("hidden", true);


            $('#fulfillmentWan').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                destroy: true,
                ajax: {
                    url: "<?php echo site_url('wan/fulfillment/listdata' . $param) ?>" + "?" + $('#SearchCloseForm').serialize(),
                },

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
            // table.ajax.reload();
        });
    });

    function showDetail(idff) {
        $('#bodymodal_detailData').html(
            '<div class="text-center">' +
            '<h6>' +
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...' +
            '</h6>' +
            '</div>'
        );
        $.ajax({
            url: "<?= site_url('/wan/fulfillment/detaildata'); ?>",
            data: "idff=" + idff,
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
        var idff = $(this).data('idff');
        var no_order = $(this).data('no_order');
        var nama = $(this).data('nama');
        var url = "<?php echo site_url('/wan/fulfillment/') ?>" + idff

        // console.log(url);
        swal({
                title: 'Delete Data',
                text: 'Apakah anda yakin ingin menghapus ' + no_order + ' - ' + nama + ' ?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('/wan/fulfillment/') ?>" + idff,
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

<?= $this->section('chocolat-js') ?>
<script src="<?= base_url() ?>/template/node_modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
<?= $this->endSection(); ?>