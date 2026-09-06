<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3">
            <div class="row  pl-3">
                <a href="<?= base_url('wan/olt'); ?> " class="btn btn-sm"><i class="fas fa-arrow-left"></i></a>
                <h4 class="pl-3">Add Data OLT</h4>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header mb-3">
                </div>
                <div class="card-body col-md-12">
                    <form action="/wan/olt" method="post">
                        <?= csrf_field(); ?>
                        <div class="form-group row mb-4">
                            <label for="witel" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">witel</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="witel" name="witel" placeholder="witel" value="<?= old('witel', 'BENGKULU'); ?>">
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label for="sto" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">sto</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control custom-select" id="sto" name="sto" placeholder="sto" autofocus value="<?= old('sto'); ?>">
                                    <!-- <option selected>Choose...</option> -->
                                    <option value="" hidden>sto</option>
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
                                <select class="form-control <?= ($validation->hasError('idmetro')) ? 'is-invalid' : ''; ?>" id="hostname_metro" name="idmetro" placeholder="hostname_metro" value="<?= old('hostname_metro'); ?>">
                                    <option value="" hidden>hostname_metro</option>
                                    <?php foreach ($dataMetro as $d) : ?>
                                        <option value="<?= $d->idmetro ?>"><?= $d->hostname_metro ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('idmetro'); ?>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label for="ip_metro" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">ip_metro</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="ip_metro" name="ip_metro" placeholder="ip_metro" value="<?= old('ip_metro'); ?>">
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <label for="port_metro" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">port_metro</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="port_metro" name="port_metro" placeholder="port_metro" value="<?= old('port_metro'); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hostname_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">hostname_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="hostname_olt" name="hostname_olt" placeholder="hostname_olt" value="<?= old('hostname_olt'); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ip_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">ip_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="ip_olt" name="ip_olt" placeholder="ip_olt" value="<?= old('ip_olt'); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="port_olt" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">port_olt</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="port_olt" name="port_olt" placeholder="port_olt" value="<?= old('port_olt'); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="platform" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">platform</label>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="platform" id="platform1" value="ZTE" <?= (old('platform') == 'ZTE') ? 'checked' : ''; ?>>
                                    ZTE
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                            <div class="form-check form-check-radio form-check-inline ml-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="platform" id="platform2" value="HUAWEI" <?= (old('platform') == 'HUAWEI') ? 'checked' : ''; ?>>
                                    HUAWEI
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row mb-4 mt-4">
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

<!-- <script type="text/javascript">
    $(function() {

        $("#hostname_metro")({
            source: "<?php echo base_url() ?>/wan/olt/ip_metro",
            select: function(event, ui) {
                $("#hostname_metro").trigger('blur');
                $("#hostname_metro").val(ui.item.value);
                ip_metro();
            }
        });
    });

    function ip_metro() {
        var hostname_metro = $("#hostname_metro").val();
        $.ajax({
            success: function() {
                $("#ip_metro").val(hostname_metro);

            }
        });

    }
</script> -->

<?= $this->endSection(); ?>