<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>


<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <?php if (in_groups(['superadmin', 'admin'])) : ?>
                    <a href="<?= base_url('user/userlist'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
                <?php endif; ?>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">Change Password</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 mt-3">
                    <form action="<?= base_url(); ?>/user/setPassword" method="post">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="id" class="id" value="<?= $id; ?>">
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12 col-md-4">
                                <input type="password" name="old_password" class="form-control <?= ($validation->hasError('old_password')) ? 'is-invalid' : ''; ?>" placeholder="Old Password" autocomplete="off" value="<?= old('old_password', $UserModel->old_password); ?>" autofocus>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('old_password'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12 col-md-4">
                                <input type="password" name="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : ''; ?>" placeholder="<?= lang('Auth.password') ?>" autocomplete="off" value="<?= old('password', $UserModel->password); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('password'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12 col-md-4">
                                <input type="password" name="pass_confirm" class="form-control <?= ($validation->hasError('pass_confirm')) ? 'is-invalid' : ''; ?>" placeholder="<?= lang('Auth.repeatPassword') ?>" autocomplete="off" value="<?= old('pass_confirm', $UserModel->password); ?>">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('pass_confirm'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4 mt-4 justify-content-center">
                            <div class="col-sm-12 col-md-4">
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

<?= $this->endSection() ?>