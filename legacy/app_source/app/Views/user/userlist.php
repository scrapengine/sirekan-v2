<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>


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
            <div class="font-weight-bold ml-3" style="font-size: 1rem;">DATA USER</span></div>

            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                <div class="section-header-button ml-2">
                    <a href="<?= base_url('user/new'); ?> " class="btn btn-sm btn-link" data-toggle="tooltip" data-placement="bottom" title="Add Data"><i class="fas fa-upload"></i></a>
                </div>
            <?php endif ?>
            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                <div class="section-header-breadcrumb col-2 justify-content-end">
                    <div class="btn-group  rounded-pill">
                        <span data-toggle="modal" data-target="#table_import">
                            <a type="button" class="btn btn-sm btn-link text-info" data-toggle="tooltip" data-placement="bottom" title="Import Excel"> <i class="fas fa-file-upload" aria-hidden="true"></i></a>
                        </span>
                    </div>
                </div>
            <?php endif ?>
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
                <div class="card-body p-2 mt-3">
                    <div class="text-right mb-3 pr-3">
                        <a href="<?= site_url('user/userlist/export' . $param); ?>" class="btn btn-link animation-on-hover">
                            <i class="fas fa-download" data-toggle="tooltip" data-placement="left" title="Download"></i>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="myTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>USERNAME</th>
                                    <th>EMAIL</th>
                                    <th>ROLE</th>
                                    <th style="width: 60px;">STATUS</th>
                                    <th style="width: 90px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($users as $row) : ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= $row->username; ?></td>
                                        <td><?= $row->email; ?></td>
                                        <td><?= $row->name; ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-circle btn-active-users" data-id="<?= $row->userid; ?>" data-active="<?= $row->active == 1 ? 1 : 0; ?>" title="Klik untuk Mengaktifkan atau Menonaktifkan">
                                                <?= $row->active == 1 ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown d-inline dropleft">
                                                    <button class="btn btn-sm animation-on-hover" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-tasks"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-dark dropleft" aria-labelledby="dropdownMenuButton">
                                                        <a href="<?= base_url(); ?>/user/changePassword/<?= $row->userid; ?>" class="btn btn-link btn-sm" title="Ubah Password">
                                                            <i class="fas fa-key"></i> Password</a>
                                                        <a href="#" class="btn btn-link btn-sm btn-change-role" data-id="<?= $row->userid; ?>" title="Ubah Role">
                                                            <i class="fas fa-user-tag"></i> Role</a>
                                                        <a href="#" class="btn btn-link btn-sm" data-toggle="modal" data-target="#delete<?= $row->userid; ?>">
                                                            <i class='fas fa-trash'></i> Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php foreach ($users as $d) : ?>

    <!-- The Modal Delete-->
    <div class="modal fade" id="delete<?= $d->userid; ?>">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Delete Data</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <form action="/user/userlist/<?= $d->userid; ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="modal-body">
                        Apakah anda yakin ingin menghapus <?= $d->username; ?> ?
                        <input type="hidden" name="userid" value="<?= $d->userid; ?>">
                        <br>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer justify-content-end">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endforeach; ?>

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
            <form action="/user/import" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <a class="btn btn-sm btn-link float-right text-info" href="<?= base_url('USER-Format-Import.xlsx'); ?>">
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

<!-- The Modal Activate-->
<div class="modal fade" id="activateModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Activate</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <!-- Don't Forget to add enctype for input file -->
            <form action="<?= base_url(); ?>/user/activate" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">Apakah anda yakin untuk mengupdate user ?</div>

                <!-- Modal footer -->
                <div class="modal-footer justify-content-end">
                    <input type="hidden" name="id" class="id">
                    <input type="hidden" name="active" class="active">
                    <button class="btn btn-secondary mr-3" type="button" data-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-primary">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form action="<?= base_url(); ?>/user/changeGroup" method="post">
    <?= csrf_field(); ?>
    <div class="modal fade" id="changeGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Change Role</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="list-group-item p-3">
                        <div class="row align-items-start">
                            <div class="col-md-4 mb-8pt mb-md-0">
                                <div class="media align-items-left">
                                    <div class="d-flex flex-column media-body media-middle">
                                        <span class="card-title">Group</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-8pt mb-md-0">
                                <select name="group" class="form-control custom-select" data-toggle="select">
                                    <?php foreach ($GroupModel as $d) : ?>
                                        <option value="<?= $d->id ?>" <?= $d->name == $d->name ? 'selected' : null ?>><?= $d->name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ubah</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- <script>
    function activateModal() {
        
        // get data from button edit
        const id = $(this).data('id');
        const active = $(this).data('active');

        // Set data to Form Edit
        $('.id').val(id);
        $('.active').val(active);
        // Call Modal Edit
        $('#activateModal').modal('show');
    }

    function changeRole() {
        // get data from button edit
        const id = $(this).data('id');

        // Set data to Form Edit
        $('.id').val(id);
        // Call Modal Edit
        $('#changeGroupModal').modal('show');
    }
</script> -->

<?= $this->section('user-manage') ?>

<script type="text/javascript">
    $(document).ready(function() {
        // get Delete Page
        $('.btn-active-users').on('click', function() {
            // get data from button edit
            const id = $(this).data('id');
            const active = $(this).data('active');

            // Set data to Form Edit
            $('.id').val(id);
            $('.active').val(active);
            // Call Modal Edit
            $('#activateModal').modal('show');
        });

        $('.btn-change-role').on('click', function() {
            // get data from button edit
            const id = $(this).data('id');

            // Set data to Form Edit
            $('.id').val(id);
            // Call Modal Edit
            $('#changeGroupModal').modal('show');
        });

    });
</script>

<?= $this->endSection() ?>

<!-- dataTables serverside -->
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-3.5.1.js"></script>
<script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery-1.12.1-dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#userList').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: "<?php echo site_url('user/listdata' . $param) ?>",
            order: [],
            columnDefs: [{
                    targets: 0,
                    orderable: false
                }, //first column is not orderable.
            ],
            columns: [{
                    data: 'number'
                },
                {
                    data: 'action'
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
    });
</script>


<?= $this->endSection(); ?>