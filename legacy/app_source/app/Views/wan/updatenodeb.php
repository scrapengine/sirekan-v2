<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Update Data NODE-B</h1>

    <div class="col-10">
        <div class="card shadow mb-4">
            <div class="card-header py-3"></div>
            <div class="card-body">
                <form action="/wan/saveupdatenodeb/<?= $dataNodeb['idnodeb']; ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="form-group row">
                        <label for="sto" class="col-sm-2 col-form-label">STO</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="sto" name="sto" placeholder="STO" autofocus value="<?= old('sto', $dataNodeb['sto']); ?>">
                            <input type="hidden" class="form-control" id="idnodeb" name="idnodeb" value="<?= $dataNodeb['idnodeb']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="site_id" class="col-sm-2 col-form-label">site_id</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control <?= ($validation->hasError('site_id')) ? 'is-invalid' : ''; ?>" id="site_id" name="site_id" placeholder="site_id" value="<?= old('site_id', $dataNodeb['site_id']); ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('site_id'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="site_name" class="col-sm-2 col-form-label">site_name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="site_name" name="site_name" placeholder="site_name" value="<?= old('site_name', $dataNodeb['site_name']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hostname_metro" class="col-sm-2 col-form-label">hostname_metro</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="hostname_metro" name="hostname_metro" placeholder="hostname_metro" value="<?= old('hostname_metro', $dataNodeb['hostname_metro']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ip_metro" class="col-sm-2 col-form-label">ip_metro</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ip_metro" name="ip_metro" placeholder="ip_metro" value="<?= old('ip_metro', $dataNodeb['ip_metro']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="port_metro" class="col-sm-2 col-form-label">port_metro</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="port_metro" name="port_metro" placeholder="port_metro" value="<?= old('port_metro', $dataNodeb['port_metro']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hostname_gpon" class="col-sm-2 col-form-label">hostname_gpon</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="hostname_gpon" name="hostname_gpon" placeholder="hostname_gpon" value="<?= old('hostname_gpon', $dataNodeb['hostname_gpon']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ip_gpon" class="col-sm-2 col-form-label">ip_gpon</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ip_gpon" name="ip_gpon" placeholder="ip_gpon" value="<?= old('ip_gpon', $dataNodeb['ip_gpon']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="port_gpon" class="col-sm-2 col-form-label">port_gpon</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="port_gpon" name="port_gpon" placeholder="port_gpon" value="<?= old('port_gpon', $dataNodeb['port_gpon']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hostname_ont" class="col-sm-2 col-form-label">hostname_ont</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="hostname_ont" name="hostname_ont" placeholder="hostname_ont" value="<?= old('hostname_ont', $dataNodeb['hostname_ont']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ip_ont" class="col-sm-2 col-form-label">ip_ont</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ip_ont" name="ip_ont" placeholder="ip_ont" value="<?= old('ip_ont', $dataNodeb['ip_ont']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="port_ont" class="col-sm-2 col-form-label">port_ont</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="port_ont" name="port_ont" placeholder="port_ont" value="<?= old('port_ont', $dataNodeb['port_ont']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ont_type" class="col-sm-2 col-form-label">ont_type</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ont_type" name="ont_type" placeholder="ont_type" value="<?= old('ont_type', $dataNodeb['ont_type']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="serial_number" class="col-sm-2 col-form-label">serial_number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="serial_number" value="<?= old('serial_number', $dataNodeb['serial_number']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="odc" class="col-sm-2 col-form-label">odc</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="odc" name="odc" placeholder="odc" value="<?= old('odc', $dataNodeb['odc']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="odp" class="col-sm-2 col-form-label">odp</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="odp" name="odp" placeholder="odp" value="<?= old('odp', $dataNodeb['odp']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tikor_site" class="col-sm-2 col-form-label">tikor_site</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="tikor_site" name="tikor_site" placeholder="tikor_site" value="<?= old('tikor_site', $dataNodeb['tikor_site']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



</div>
<!-- /.container-fluid -->

<?= $this->endSection(); ?>