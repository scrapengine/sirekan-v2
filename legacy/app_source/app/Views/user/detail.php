<?= $this->extend('layout/default'); ?>

<?= $this->section('content'); ?>


<!-- alert -->
<?php if (session()->getFlashData('success')) : ?>
    <div class="alert alert-success fade show" role="alert">
        <strong>Success! </strong> <?= session()->getFlashData('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeAlert()">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashData('error')) : ?>
    <div class="alert alert-danger fade show" role="alert">
        <strong>Error! </strong> <?= session()->getFlashData('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeAlert()">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<!-- end alert -->





<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
        </div>
        <div class="section-body">
            <h4 class="section-title">User Details</h4>
            <p class="section-lead">
            </p>
            <div class="content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-user">
                            <div class="card-body">
                                <p class="card-text">
                                <div class="author">
                                    <div class="block block-one"></div>
                                    <div class="block block-two"></div>
                                    <div class="block block-three"></div>
                                    <div class="block block-four"></div>
                                    <a href="javascript:void(0)">
                                        <img class="avatar" src="<?= base_url('/img/' . $users->user_image); ?>" alt="<?= $users->user_image ?>">
                                        <h5 class="title"><?= $users->username; ?></h5>
                                    </a>
                                    <p class="description">
                                        <?php if ($users->fullname) : ?>
                                            <?= $users->fullname; ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="description badge badge-<?= ($users->name == 'superadmin') ? 'danger' : ('user' ? 'success' : 'warning');  ?>">
                                        <?= $users->name; ?>
                                    </p>
                                </div>
                                </p>
                                <div class="card-description text-center">
                                    <?= $users->email; ?>
                                </div>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="title">Edit Profile</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6 col-12">
                                        <label>username</label>
                                        <input type="text" name="username" class="form-control form-control-user" name="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username', $users->username) ?>" readonly>
                                        <div class="invalid-feedback">
                                            Please fill in the username
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <label>email</label>
                                        <input type="email" name="email" class="form-control form-control-user" name="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email', $users->email) ?>" readonly> <small id="emailHelp" class="form-text text-muted"><?= lang('Auth.weNeverShare') ?></small>
                                        <div class="invalid-feedback">
                                            Please fill in the email
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 col-12">
                                        <label>fullname</label>
                                        <input type="text" name="fullname" class="form-control" value="<?= old('fullname', $users->fullname) ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <!-- <fieldset disabled> -->
                                        <label>user_image</label>
                                        <div class="custom-file">
                                            <input type="file" name="user_image" class="custom-file-input <?= ($validation->hasError('user_image')) ? 'is-invalid' : ''; ?>" id="user_image" readonly>

                                            <label for="user_image" class="custom-file-label"><?= old('user_image', $users->user_image) ?></label>

                                            <div class="invalid-feedback">
                                                <?= $validation->getError('user_image'); ?>
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <div class="form-group col-md-6 col-12">
                                        <label>password</label>
                                        <input type="password" name="password" class="form-control form-control-user <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>" placeholder="<?= lang('Auth.password') ?>" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-6 col-12">
                                        <label>pass_confirm</label>
                                        <input type="password" name="pass_confirm" class="form-control form-control-user <?php if (session('errors.pass_confirm')) : ?>is-invalid<?php endif ?>" placeholder="<?= lang('Auth.repeatPassword') ?>" autocomplete="off">
                                    </div>
                                </div> -->
                            </div>
                            <div class="card-footer text-right">
                                <button class="btn btn-fill btn-primary animation-on-hover">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">User Detail</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="<?= base_url('/img/' . $users->user_image); ?>" class="img-fluid rounded-start" alt="<?= $users->username; ?>">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <h4><?= $users->username; ?></h4>
                                </li>

                                <?php if ($users->fullname) : ?>
                                    <li class="list-group-item"><?= $users->fullname; ?></li>
                                <?php endif; ?>

                                <li class="list-group-item"><?= $users->email; ?></li>
                                <li class="list-group-item">
                                    <span class="badge badge-<?= ($users->name == 'superadmin') ? 'danger' : ('user' ? 'success' : 'warning');  ?>"><?= $users->name; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <small><a href="<?= base_url('admin'); ?>">&laquo;back to user list</a></small>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection(); ?>