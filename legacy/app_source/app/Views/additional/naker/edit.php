<?= $this->extend('layout/default') ?>

<?= $this->section('selectric-css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/selectric/public/selectric.css">
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('additional/naker'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">EDIT DATA NAKER</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 mt-3">
                    <form action="/additional/naker/<?= $dataNaker->idnaker; ?>" method="post">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PATCH">
                        <div class="form-group row">
                            <label for="nik" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">NIK</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="nik" name="nik" placeholder="nik" value="<?= old('nik', $dataNaker->nik); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">NAMA*</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control <?= ($validation->hasError('nama')) ? 'is-invalid' : ''; ?>" id="nama" name="nama" placeholder="nama" value="<?= old('nama', $dataNaker->nama); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('nama'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="divisi" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">DIVISI</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="divisi" name="divisi" placeholder="divisi" value="<?= old('divisi', $dataNaker->divisi); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jobdesk" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">JOBDESK</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" style="height:100%;" id="jobdesk" name="jobdesk" placeholder="jobdesk" value="<?= old('jobdesk', $dataNaker->jobdesk); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_hp" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">NO_HP</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" style="height:100%;" id="no_hp" name="no_hp" placeholder="no_hp" value="<?= old('no_hp', $dataNaker->no_hp); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sto" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">STO</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control selectric <?= ($validation->hasError('idsto')) ? 'is-invalid' : ''; ?>" id="idsto" name="idsto[]" placeholder="idsto" value="<?= old('idsto'); ?>" multiple>
                                    <option value="" hidden>Choose...</option>
                                    <?php foreach ($dataSto as $d) : ?>
                                        <?php
                                        $array = array($d->idsto);
                                        $separator = explode(", ", $dataNaker->idsto);
                                        ?>

                                        <option value="<?= $d->idsto ?>" <?php foreach ($separator as $s) : ?><?= in_array($s, $array) ? 'selected' : '' ?><?php endforeach; ?>>
                                            <?= $d->idsto ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('idsto'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="labor" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">LABOR</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" style="height:100%;" id="labor" name="labor" placeholder="labor" value="<?= old('labor', $dataNaker->labor); ?>">
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-server"></i> Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>

<?= $this->section('selectric-js') ?>
<script src="<?= base_url() ?>/template/node_modules/selectric/public/jquery.selectric.min.js"></script>
<?= $this->endSection(); ?>