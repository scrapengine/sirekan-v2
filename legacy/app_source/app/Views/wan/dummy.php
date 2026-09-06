<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dummy</h1>
            <div class="section-header-button">
                <a href="<?= base_url('wan/add'); ?> " class="a btn btn-primary">Add Data</a>
            </div>
        </div>

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

        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <h4>Data Dummy</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md" id="myTable">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>gawe_name</th>
                                    <th>date_gawe</th>
                                    <th>info_gawe</th>
                                    <th>Action</th>
                                </tr>
                                <?php $i = 1 ?>
                                <?php foreach ($gawe as $d) : ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= $d->gawe_name; ?></td>
                                        <td><?= date('d/m/Y', strtotime($d->date_gawe)); ?></td>
                                        <td><?= $d->info_gawe; ?></td>
                                        <td class="btn-group btn-group-toggle text-justify-center" style="width: 15%">
                                            <a href="<?= site_url('wan/edit/' . $d->gawe_id); ?>" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?= $d->gawe_id; ?>">
                                                <i class='fas fa-trash' style='color: white'></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <nav class="d-inline-block">
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1"><i class="fas fa-chevron-left"></i></a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1 <span class="sr-only">(current)</span></a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- The Modal -->
<div class="modal fade" id="delete<?= $d->gawe_id; ?>">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Data</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form action="<?= site_url('wan/delete/' . $d->gawe_id); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    Apakah anda yakin ingin menghapus <?= $d->gawe_name; ?> ?
                    <input type="hidden" name="idnodeb" value="<?= $d->gawe_id; ?>">
                    <br>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" name="deletenodeb">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>