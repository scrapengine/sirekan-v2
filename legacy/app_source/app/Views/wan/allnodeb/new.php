<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3">
            <div class="section-header-back">
                <a href="<?= base_url('wan/allnodeb'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">ADD DATA NODE-B</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12">
                    <form action="/wan/allnodeb" method="post" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <div class="form-group row mb-4 justify-content-center">
                            <div class="col-sm-12 col-md-7">
                                <div id="accordion" role="tablist" aria-multiselectable="true" class="card-collapse">
                                    <div class="card card-plain">
                                        <div class="card-header" role="tab" id="headingOne">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                #SITE

                                                <i class="fas fa-chevron-down text-right"></i>
                                            </a>
                                        </div>

                                        <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label for="sto" class="col-12 col-md-3 col-lg-3 col-form-label">STO</label>
                                                    <div class="col-12 col-lg-12">
                                                        <select class="form-control custom-select <?= ($validation->hasError('sto')) ? 'is-invalid' : ''; ?>" id="sto" name="sto" placeholder="sto" value="<?= old('sto'); ?>" autofocus>
                                                            <option value="" hidden>Choose...</option>
                                                            <?php foreach ($dataSto as $d) : ?>
                                                                <option value="<?= old('sto'); ?>" hidden <?= old('sto') == $d->idsto ? 'selected' : null ?>><?= old('sto'); ?></option>
                                                                <option value="<?= $d->idsto ?>"><?= $d->idsto ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            <?= $validation->getError('sto'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="site_id_all" class="col-12 col-md-3 col-lg-3 col-form-label">SITE_ID</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control <?= ($validation->hasError('site_id_all')) ? 'is-invalid' : ''; ?>" id="site_id_all" name="site_id_all" placeholder="site_id" value="<?= old('site_id_all'); ?>">
                                                        <div class="invalid-feedback">
                                                            <?= $validation->getError('site_id_all'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="site_name_all" class="col-12 col-md-3 col-lg-3 col-form-label">SITE_NAME</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="site_name_all" name="site_name_all" placeholder="site_name" value="<?= old('site_name_all'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="nsa" class="col-12 col-md-3 col-lg-3 col-form-label">NSA</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="nsa" name="nsa" placeholder="nsa" value="<?= old('nsa','NOP BENGKULU'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="kabupaten" class="col-12 col-md-3 col-lg-3 col-form-label">KABUPATEN</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="kabupaten" name="kabupaten" placeholder="kabupaten" value="<?= old('kabupaten'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="kecamatan" class="col-12 col-md-3 col-lg-3 col-form-label">KECAMATAN</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="kecamatan" name="kecamatan" placeholder="kecamatan" value="<?= old('kecamatan'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="kelurahan" class="col-12 col-md-3 col-lg-3 col-form-label">KELURAHAN</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="kelurahan" name="kelurahan" placeholder="kelurahan" value="<?= old('kelurahan'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="lat_long" class="col-12 col-md-3 col-lg-3 col-form-label">COORDINATE</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="lat_long" name="lat_long" placeholder="lat_long" value="<?= old('lat_long'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="tp" class="col-12 col-md-3 col-lg-3 col-form-label">TOWER_PROVIDER</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="tp" name="tp" placeholder="tp" value="<?= old('tp'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="s_power" class="col-12 col-md-3 col-lg-3 col-form-label">SOURCE_POWER</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="s_power" name="s_power" placeholder="s_power" value="<?= old('s_power'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="transport" class="col-12 col-md-3 col-lg-3 col-form-label">TRANSPORT</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="transport" name="transport" placeholder="transport" value="<?= old('transport'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-plain">
                                        <div class="card-header" role="tab" id="headingTwo">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                #TECH

                                                <i class="fas fa-chevron-down text-right"></i>
                                            </a>
                                        </div>
                                        <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                        <div class="card-body">
                                                <div class="form-group row">
                                                    <label for="tech" class="col-12 col-md-3 col-lg-3 col-form-label">TECH</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="tech" name="tech" placeholder="tech" value="<?= old('tech'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="band_2g" class="col-12 col-md-3 col-lg-3 col-form-label">BAND_2G</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="band_2g" name="band_2g" placeholder="band_2g" value="<?= old('band_2g'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="band_3g" class="col-12 col-md-3 col-lg-3 col-form-label">BAND_3G</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="band_3g" name="band_3g" placeholder="band_3g" value="<?= old('band_3g'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="band_4g" class="col-12 col-md-3 col-lg-3 col-form-label">BAND_4G</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="band_4g" name="band_4g" placeholder="band_4g" value="<?= old('band_4g'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="band_updated" class="col-12 col-md-3 col-lg-3 col-form-label">BAND_UPDATED</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="band_updated" name="band_updated" placeholder="band_updated" value="<?= old('band_updated'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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



<?= $this->endSection(); ?>
