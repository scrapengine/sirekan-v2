<?= $this->extend('layout/default') ?>

<?= $this->section('content'); ?>


<!-- Main Content -->
<div class="main-content">
    <section class="section">

        <?php if (session()->getFlashData('success')) : ?>
            <div id="flash" data-icon="success" data-title="Success!" data-flash="<?= session()->getFlashData('success'); ?>"></div>
        <?php endif; ?>

        <?php if (session()->getFlashData('error')) : ?>
            <div id="flash" data-icon="error" data-title="Error!" data-flash="<?= session()->getFlashData('error'); ?>"></div>
        <?php endif; ?>

        <div class="section-body pt-5">
            <h4 class="section-title">Hi, <?= user()->username; ?></h4>
            <p class="section-lead">
                Change information about yourself on this page.
            </p>
            <div class="row mt-sm-3">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card profile-widget">
                        <div class="profile-widget-header">
                            <img src="<?= base_url('/img/' . user()->user_image); ?>" alt="<?= user()->user_image; ?>" class="rounded-circle profile-widget-picture">
                            <div class="profile-widget-items">
                                <div class="profile-widget-item">
                                    <div class="profile-widget-item-label">Username</div>
                                    <div class="profile-widget-item-value"><?= user()->username; ?></div>
                                </div>
                                <div class="profile-widget-item">
                                    <div class="profile-widget-item-label">Name</div>
                                    <div class="profile-widget-item-value">
                                        <?php if (user()->fullname) : ?>
                                            <?= user()->fullname; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="profile-widget-item">
                                    <div class="profile-widget-item-label">Email</div>
                                    <div class="profile-widget-item-value"><?= user()->email; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-widget-description">
                            <div class="profile-widget-name"><?= user()->username; ?> <div class="text-muted d-inline font-weight-normal">
                                    <div class="slash"></div> Web Developer
                                </div>
                            </div>
                            <?= user()->username; ?> is a superhero name in <b>Indonesia</b>, especially in my family. He is not a fictional character but an original hero in my family, a hero for his children and for his wife. So, I use the name as a user in this template. Not a tribute, I'm just bored with <b>'John Doe'</b>.
                        </div>
                        <div class="card-footer text-center">
                            <div class="font-weight-bold mb-2">Edit Profile</div>
                            <form action="/user/<?= user_id(); ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="_method" value="PATCH">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6 col-12">
                                            <label>Username</label>
                                            <input type="text" name="username" class="form-control form-control-user <?php if (session('errors.username')) : ?>is-invalid<?php endif ?>" name="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username', user()->username) ?>" required>
                                            <div class="invalid-feedback">
                                                Please fill in the username
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 col-12">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control form-control-user <?php if (session('errors.email')) : ?>is-invalid<?php endif ?>" name="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email', user()->email) ?>" required> <small id="emailHelp" class="form-text text-muted"><?= lang('Auth.weNeverShare') ?></small>
                                            <div class="invalid-feedback">
                                                Please fill in the email
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6 col-12">
                                            <label>Fullname</label>
                                            <input type="text" name="fullname" class="form-control" value="<?= old('fullname', user()->fullname) ?>">
                                        </div>
                                        <div class="form-group col-md-6 col-12">
                                            <!-- <fieldset disabled> -->
                                            <input type="text" name="user_image_old" id="user_image_old" hidden value="<?= user()->user_image; ?>">
                                            <label>User Image</label>
                                            <div class="custom-file">
                                                <input type="file" name="user_image" class="custom-file-input <?= ($validation->hasError('user_image')) ? 'is-invalid' : ''; ?>" id="user_image">

                                                <label for="user_image" class="custom-file-label"><?= old('user_image', user()->user_image) ?></label>

                                                <div class="invalid-feedback">
                                                    <?= $validation->getError('user_image'); ?>
                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-fill btn-primary animation-on-hover">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?= $this->endSection(); ?>

<?= $this->section('swal-js') ?>
<script src="<?= base_url() ?>/template/node_modules/sweetalert/dist/sweetalert.min.js"></script>
<script>
    let title = $('#flash').data('title');
    let icon = $('#flash').data('icon');
    let flash = $('#flash').data('flash');
    if (flash) {
        swal({
            title: title,
            icon: icon,
            text: flash,
        })
    }
</script>
<?= $this->endSection(); ?>