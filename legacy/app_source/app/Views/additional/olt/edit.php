<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3">
            <div class="row  pl-3">
                <a href="<?= base_url('additional/olt'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
                <h5 class="pl-3">EDIT DATA OLT</h5>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 mt-3">
                    <form action="/additional/olt/<?= $dataOlt->hostname_olt; ?>" method="post">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PATCH">
                        <div class="form-group row mb-4">
                            <label for="witel" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">witel</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="witel" name="witel" placeholder="witel" value="<?= old('witel', $dataOlt->witel); ?>">
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label for="sto" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">sto</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control custom-select <?= ($validation->hasError('idsto')) ? 'is-invalid' : ''; ?>" id="idsto" name="idsto" placeholder="idsto" value="<?= old('idsto'); ?>">
                                    <option value="" hidden>idsto</option>
                                    <?php foreach ($dataSto as $d) : ?>
                                        <option value="<?= $d->idsto ?>" <?= $dataOlt->idsto == $d->idsto ? 'selected' : null ?>><?= $d->idsto ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('idsto'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hostname_metro" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">hostname_metro</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control custom-select <?= ($validation->hasError('hostname_metro')) ? 'is-invalid' : ''; ?>" id="hostname_metro" name="hostname_metro" placeholder="hostname_metro" value="<?= old('hostname_metro'); ?>">
                                    <option value="" hidden>hostname_metro</option>
                                    <?php foreach ($dataMetro as $d) : ?>
                                        <option value="<?= $d->hostname_metro ?>" <?= $dataOlt->hostname_metro == $d->hostname_metro ? 'selected' : null ?>><?= $d->hostname_metro ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('hostname_metro'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="port_metro" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">port_metro</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="port_metro" name="port_metro" placeholder="port_metro" value="<?= old('port_metro', $dataOlt->port_metro); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hostname_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">hostname_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control <?= ($validation->hasError('hostname_olt')) ? 'is-invalid' : ''; ?>" id="hostname_olt" name="hostname_olt" placeholder="hostname_olt" value="<?= old('hostname_olt', $dataOlt->hostname_olt); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('hostname_olt'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ip_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">ip_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="ip_olt" name="ip_olt" placeholder="ip_olt" value="<?= old('ip_olt', $dataOlt->ip_olt); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="port_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">port_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="port_olt" name="port_olt" placeholder="port_olt" value="<?= old('port_olt', $dataOlt->port_olt); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="platform" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">platform</label>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="platform" id="platform1" value="ZTE" <?= old('platform', $dataOlt->platform) == 'ZTE' ? 'checked' : ''; ?>>
                                    ZTE
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="platform" id="platform2" value="HUAWEI" <?= old('platform', $dataOlt->platform) == 'HUAWEI' ? 'checked' : ''; ?>>
                                    HUAWEI
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">type_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="type_olt" name="type_olt" placeholder="type_olt" value="<?= old('type_olt', $dataOlt->type_olt); ?>">
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