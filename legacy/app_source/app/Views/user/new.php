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
            <div class="font-weight-bold" style="font-size: 1rem;">Add Data User</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 text-center mb-3">
                    <?= view('Myth\Auth\Views\_message_block') ?>
                    <form action="<?= route_to('register') ?>" method="post" class="user">
                        <?= csrf_field(); ?>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" class="form-control form-control-user <?php if (session('errors.username')) : ?>is-invalid<?php endif ?>" name="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>">
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="email" class="form-control form-control-user <?php if (session('errors.email')) : ?>is-invalid<?php endif ?>" name="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>"> <small id="emailHelp" class="form-text text-muted"><?= lang('Auth.weNeverShare') ?></small>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-3 mb-3 mb-sm-0">
                                <input type="password" name="password" class="form-control form-control-user <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>" placeholder="<?= lang('Auth.password') ?>" autocomplete="off">
                            </div>
                            <div class="col-sm-3">
                                <input type="password" name="pass_confirm" class="form-control form-control-user <?php if (session('errors.pass_confirm')) : ?>is-invalid<?php endif ?>" placeholder="<?= lang('Auth.repeatPassword') ?>" autocomplete="off">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-server"></i> Save</button>
                        <button type="reset" class="btn btn-secondary">Reset</i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>