<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data NODE-B via Metro-e</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <!-- Button to Open the Modal -->
            <!-- <button type="button" class="m-0 font-weight-bold btn btn-primary" data-toggle="modal" data-target="#myModal">
                Add Data
            </button> -->
            <a href="<?= base_url('wan/addnodeb'); ?>" class="m-0 font-weight-bold btn btn-primary">
                Add Data
            </a>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('pesan')) : ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong> <?= session()->getFlashdata('pesan'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>

            <?php endif; ?>
            <div class="table table-hover table-responsive">
                <table class="table table-container" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Action</th>
                            <th>Divisi</th>
                            <th>Regional Tsel</th>
                            <th>STO</th>
                            <th>Site ID</th>
                            <th>Site Name</th>
                            <th>Hostname Metro</th>
                            <th>Ip Metro</th>
                            <th>Port Metro</th>
                            <th>Hostname Gpon</th>
                            <th>Ip Gpon</th>
                            <th>Port Gpon</th>
                            <th>Hostname ONT</th>
                            <th>Ip ONT</th>
                            <th>Port ONT</th>
                            <th>Bandwidth</th>
                            <th>Reported to Regional</th>
                            <th>ONT Type</th>
                            <th>Serial Number</th>
                            <th>ODC</th>
                            <th>ODP</th>
                            <th>Tikor Site</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1 ?>
                        <?php foreach ($dataNodeb as $d) : ?>

                            <tr>
                                <td><?= $i++ ?></td>
                                <td class="btn-group" role="group">

                                    <!-- <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#edit<?= $d['idnodeb']; ?>"><i class='fas fa-pencil-alt' style='color: orange'></i>
                                    </button> -->
                                    <a href="/wan/updatenodeb/<?= $d['idnodeb']; ?>" type="button" class="btn btn-sm"><i class='fas fa-pencil-alt' style='color: orange'></i>
                                    </a>
                                    <input type="hidden" name="deletesiteid" value="<?= $d['idnodeb']; ?>">
                                    <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#delete<?= $d['idnodeb']; ?>">
                                        <i class='fa fa-trash' style='color: red'></i>
                                    </button>
                                </td>
                                <td><?= $d['divisi']; ?></td>
                                <td><?= $d['regional_tsel']; ?></td>
                                <td><?= $d['sto']; ?></td>
                                <td><?= $d['site_id']; ?></td>
                                <td><?= $d['site_name']; ?></td>
                                <td><?= $d['hostname_metro']; ?></td>
                                <td><?= $d['ip_metro']; ?></td>
                                <td><?= $d['port_metro']; ?></td>
                                <td><?= $d['hostname_gpon']; ?></td>
                                <td><?= $d['ip_gpon']; ?></td>
                                <td><?= $d['port_gpon']; ?></td>
                                <td><?= $d['hostname_ont']; ?></td>
                                <td><?= $d['ip_ont']; ?></td>
                                <td><?= $d['port_ont']; ?></td>
                                <td><?= $d['bandwidth']; ?></td>
                                <td><?= $d['reported_to_regional']; ?></td>
                                <td><?= $d['ont_type']; ?></td>
                                <td><?= $d['serial_number']; ?></td>
                                <td><?= $d['odc']; ?></td>
                                <td><?= $d['odp']; ?></td>
                                <td><?= $d['tikor_site']; ?></td>
                            </tr>

                            <!-- The Modal -->
                            <div class="modal fade" id="delete<?= $d['idnodeb']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h4 class="modal-title">Delete Data</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <!-- Modal body -->
                                        <form action="/wan/deletenodeb/<?= $d['idnodeb']; ?>" method="post">
                                            <div class="modal-body">
                                                Apakah anda yakin ingin menghapus <?= $d['site_id']; ?> ?
                                                <input type="hidden" name="idnodeb" value="<?= $d['idnodeb']; ?>">
                                                <br>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger" name="deletenodeb">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?= $this->endSection(); ?>