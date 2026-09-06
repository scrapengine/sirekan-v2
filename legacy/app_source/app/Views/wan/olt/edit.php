<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="<?= base_url('wan/olt'); ?> " class="btn"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>OLT</h1>
        </div>

        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <h4>Edit Data OLT</h4>
                </div>
                <div class="card-body col-md-12">
                    <form action="/wan/olt/<?= $dataOlt->idolt; ?>" method="post">
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
                                <select class="form-control custom-select" id="sto" name="sto" placeholder="sto" autofocus value="<?= old('sto', $dataOlt->sto); ?>">
                                    <!-- <option selected>Choose...</option> -->
                                    <option value="<?= $dataOlt->sto; ?>"><?= $dataOlt->sto; ?></option>
                                    <option value="BNC">BNC</option>
                                    <option value="PDW">PDW</option>
                                    <option value="TIS">TIS</option>
                                    <option value="MNA">MNA</option>
                                    <option value="">BTH</option>
                                    <option value="BTH">KPH</option>
                                    <option value="CRP">CRP</option>
                                    <option value="MUA">MUA</option>
                                    <option value="AGR">AGR</option>
                                    <option value="KTH">KTH</option>
                                    <option value="IPU">IPU</option>
                                    <option value="MKO">MKO</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hostname_metro" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">hostname_metro</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control <?= ($validation->hasError('hostname_metro')) ? 'is-invalid' : ''; ?>" id="hostname_metro" name="hostname_metro" placeholder="hostname_metro" value="<?= old('hostname_metro'); ?>">
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
                        </div>>
                        <div class="form-group row">
                            <label for="port_metro" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">port_metro</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="port_metro" name="port_metro" placeholder="port_metro" value="<?= old('port_metro', $dataOlt->port_metro); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hostname_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">hostname_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="hostname_olt" name="hostname_olt" placeholder="hostname_olt" value="<?= old('hostname_olt', $dataOlt->hostname_olt); ?>">
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
                        <!-- <div class="form-group row">
                            <label for="platform" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">platform</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="platform" name="platform" placeholder="platform" value="<?= old('platform', $dataOlt->platform); ?>">
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <label for="platform" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">platform</label>
                            <div class="form-check ml-3">
                                <input class="form-check-input" type="radio" name="platform" id="platform1" value="ZTE" <?= $dataOlt->platform == 'ZTE' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="platform1">
                                    ZTE
                                </label>
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input" type="radio" name="platform" id="platform2" value="HUAWEI" <?= $dataOlt->platform == 'HUAWEI' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="platform2">
                                    HUAWEI
                                </label>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-server"> Save</i></button>
                                <button type="reset" class="btn btn-secondary">Reset</i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>