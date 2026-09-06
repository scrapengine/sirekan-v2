<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('additional/sto'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">EDIT DATA STO</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 mt-3">
                    <form action="/additional/sto/<?= $dataSto->idsto; ?>" method="post">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PATCH">
                        <div class="form-group row">
                            <label for="witel" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">witel*</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control <?= ($validation->hasError('witel')) ? 'is-invalid' : ''; ?>" id="witel" name="witel" placeholder="witel" value="<?= old('witel', $dataSto->witel); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('witel'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idsto" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">idsto*</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control <?= ($validation->hasError('idsto')) ? 'is-invalid' : ''; ?>" id="idsto" name="idsto" placeholder="idsto" value="<?= old('idsto', $dataSto->idsto); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('idsto'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_sto" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">nama_sto</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="nama_sto" name="nama_sto" placeholder="nama_sto" value="<?= old('nama_sto', $dataSto->nama_sto); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="longitude" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">longitude</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="longitude" value="<?= old('longitude', $dataSto->longitude); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="latitude" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">latitude</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="latitude" value="<?= old('latitude', $dataSto->latitude); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">alamat</label>
                            <div class="col-sm-12 col-md-7">
                                <textarea class="form-control" style="height:100%;" id="alamat" name="alamat" placeholder="alamat"><?= old('alamat', $dataSto->alamat); ?></textarea>
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