<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3">
            <div class="section-header-back">
                <a href="<?= base_url('wan/ont'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">EDIT DATA ONT</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 mt-3">
                    <form action="/wan/ont/<?= $dataOnt->idont; ?>" method="post">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PATCH">
                        <!-- <div class="form-group row">
                            <label for="merk" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Merk</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="merk" name="merk" placeholder="merk" value="<?= old('merk', $dataOnt->merk); ?>" autofocus>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Type</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="type" name="type" placeholder="type" value="<?= old('type', $dataOnt->type); ?>">
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <label for="type" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">ONT Type</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="form-group mb-0">
                                    <div class="input-group">
                                        <select class="form-control custom-select" id="ont_type" name="ont_type" placeholder="ont_type" value="<?= old('ont_type'); ?>">
                                            <option value="" hidden>Choose...</option>
                                            <?php foreach ($ontType as $d) : ?>
                                                <option value="<?= old('ont_type', $d->merk . "-" . $d->type); ?>" hidden <?= old('ont_type', $d->merk . "-" . $d->type) == $dataOnt->merk . "-" . $dataOnt->type ? 'selected' : null ?>><?= old('ont_type', $d->merk . "-" . $d->type); ?></option>
                                                <option value="<?= $d->merk . "-" . $d->type ?>"><?= $d->merk . "-" . $d->type ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#ont_type_modal"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="serial_number" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Serial Number *</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control <?= ($validation->hasError('serial_number')) ? 'is-invalid' : ''; ?>" id="serial_number" name="serial_number" placeholder="serial_number" value="<?= old('serial_number', $dataOnt->serial_number); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('serial_number'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Status</label>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="status" id="status1" value="BAIK" <?= (old('status', $dataOnt->status) == 'BAIK') ? 'checked' : ''; ?>>
                                    BAIK
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="status" id="status2" value="RUSAK" <?= (old('status', $dataOnt->status) == 'RUSAK') ? 'checked' : ''; ?>>
                                    RUSAK
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sto" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">STO</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control custom-select" id="idsto" name="idsto" placeholder="idsto" value="<?= old('idsto'); ?>">
                                    <option value="" hidden>Choose...</option>
                                    <?php foreach ($dataSto as $d) : ?>
                                        <option value="<?= $d->idsto ?>" <?= $dataOnt->idsto == $d->idsto ? 'selected' : null ?>><?= $d->idsto ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="allocation" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Allocation</label>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="allocation" id="allocation1" value="ASSURANCE" <?= (old('allocation', $dataOnt->allocation) == 'ASSURANCE') ? 'checked' : ''; ?>>
                                    ASSURANCE
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="allocation" id="allocation2" value="FULFILLMENT" <?= (old('allocation', $dataOnt->allocation) == 'FULFILLMENT') ? 'checked' : ''; ?>>
                                    FULFILLMENT
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="allocation" id="allocation3" value="OLO" <?= (old('allocation', $dataOnt->allocation) == 'OLO') ? 'checked' : ''; ?>>
                                    OLO
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="received" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Received</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="date" class="form-control" id="received" name="received" placeholder="received" value="<?= old('received', $dataOnt->received); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="installed" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Installed</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="date" class="form-control" id="installed" name="installed" placeholder="installed" value="<?= old('installed', $dataOnt->installed); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="return" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Return</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="date" class="form-control" id="return" name="return" placeholder="return" value="<?= old('return', $dataOnt->return); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="desc" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Desc</label>
                            <div class="col-sm-12 col-md-7">
                                <textarea type="text" class="form-control" style="height: 100%;" id="desc" name="desc" placeholder="desc"><?= old('desc', $dataOnt->desc); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group row mb-4 mt-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-server"></i> Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>


<div class="modal fade" id="ont_type_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title text-white">Data OLT</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->

            <div class="card-body mt-3 col-md-12">
                <form action="/wan/ont/type" method="post" id="submitOntType">
                    <?= csrf_field(); ?>

                    <div class="form-group row">
                        <label for="merk" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Merk</label>
                        <div class="col-sm-12 col-md-7">
                            <input type="text" class="form-control" id="merk" name="merk" placeholder="merk" value="<?= old('merk'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">Type</label>
                        <div class="col-sm-12 col-md-7">
                            <input type="text" class="form-control" id="type" name="type" placeholder="type" value="<?= old('type'); ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4 mt-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                        <div class="col-sm-12 col-md-7">
                            <button type="button" class="btn btn-primary" onclick="submitFormOntType()"><i class="fas fa-server"></i> Save</button>
                            <button type="reset" class="btn btn-secondary">Reset</i></button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script>
    function submitFormOntType() {
        document.getElementById("submitOntType").submit();
    }
</script>

<?= $this->endSection(); ?>