<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('additional/metro'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">ADD DATA METRO</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 mt-3">
                    <form action="/additional/metro" method="post">
                        <?= csrf_field(); ?>
                        <div class="form-group row">
                            <label for="sto" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">sto</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control custom-select <?= ($validation->hasError('idsto')) ? 'is-invalid' : ''; ?>" id="idsto" name="idsto" placeholder="idsto" value="<?= old('idsto'); ?>" autofocus>
                                    <option value="" hidden>Choose...</option>
                                    <?php foreach ($dataSto as $d) : ?>
                                        <option value="<?= old('idsto'); ?>" hidden <?= old('idsto') == $d->idsto ? 'selected' : null ?>><?= old('idsto'); ?></option>
                                        <option value="<?= $d->idsto ?>"><?= $d->idsto ?></option>
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
                                <input type="text" class="form-control <?= ($validation->hasError('hostname_metro')) ? 'is-invalid' : ''; ?>" id="hostname_metro" name="hostname_metro" placeholder="hostname_metro" value="<?= old('hostname_metro'); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('hostname_metro'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ip_metro" class="text-md-right col-12 col-md-3 col-lg-3 col-form-label">ip_metro</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" id="ip_metro" name="ip_metro" placeholder="ip_metro" value="<?= old('ip_metro'); ?>">
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

<!-- <script type="text/javascript">
    $(function() {

        $("#hostname_metro")({
            source: "<?php echo base_url() ?>/additional/olt/ip_metro",
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