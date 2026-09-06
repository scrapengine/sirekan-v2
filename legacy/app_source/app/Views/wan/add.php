<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="<?= base_url('wan/dummy'); ?> " class="btn"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Dummy</h1>
        </div>

        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <h4>Add Data Dummy</h4>
                </div>
                <div class="card-body col-md-6">
                    <form action="<?= site_url('wan/dummy'); ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Name Gawe*</label>
                            <input type="text" name="gawe_name" class="form-control" required autofocus>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Gawe*</label>
                            <input type="date" name="date_gawe" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Name Gawe*</label>
                            <textarea type="text" name="info_gawe" class="form-control"></textarea>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"> Save</i></button>
                            <button type="reset" class="btn btn-secondary">Reset</i></button>
                        </div>
                    </form>


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

<?= $this->endSection(); ?>