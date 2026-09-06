<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3">
            <div class="section-header-back">
                <a href="<?= base_url('wan/fulfillment'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">ADD DATA FULFILLMENT</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12">
                    <form action="/wan/fulfillment" method="post" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <div class="form-group row mb-4 justify-content-center">
                            <div class="col-sm-12 col-md-7">
                                <div id="accordion" role="tablist" aria-multiselectable="true" class="card-collapse">
                                    <div class="card card-plain">
                                        <div class="card-header" role="tab" id="headingOne">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                #CUSTOMER

                                                <i class="fas fa-chevron-down text-right"></i>
                                            </a>
                                        </div>

                                        <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label for="tanggal" class="col-12 col-md-3 col-lg-3 col-form-label">TANGGAL</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="date" class="form-control" id="tanggal" name="tanggal" placeholder="tanggal" value="<?= old('tanggal'); ?>" autofocus>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="idsto">STO*</label>
                                                            <select class="form-control custom-select <?= ($validation->hasError('idsto')) ? 'is-invalid' : ''; ?>" id="idsto" name="idsto" placeholder="idsto" value="<?= old('idsto'); ?>">
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
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="layanan">LAYANAN</label>
                                                            <select class="form-control custom-select" id="layanan" name="layanan" placeholder="layanan" value="<?= old('layanan'); ?>">
                                                                <option value="" hidden>Choose...</option>
                                                                <?php foreach ($dataLayanan as $d) : ?>
                                                                    <option value="<?= old('layanan'); ?>" hidden <?= old('layanan') == $d->layanan ? 'selected' : null ?>><?= old('layanan'); ?></option>
                                                                    <option value="<?= $d->layanan ?>"><?= $d->layanan ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="no_order">NO_ORDER*</label>
                                                            <input type="text" class="form-control <?= ($validation->hasError('no_order')) ? 'is-invalid' : ''; ?>" id="no_order" name="no_order" placeholder="no_order" value="<?= old('no_order'); ?>">
                                                            <div class="invalid-feedback">
                                                                <?= $validation->getError('no_order'); ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="order_type">ORDER_TYPE</label>
                                                            <select class="form-control custom-select" id="order_type" name="order_type" placeholder="order_type" value="<?= old('order_type'); ?>">
                                                                <option value="" hidden>Choose...</option>
                                                                <option value="New Install" <?= (old('order_type') == 'New Install') ? 'checked' : ''; ?>>New Install</option>
                                                                <option value="Modify" <?= (old('order_type') == 'Modify') ? 'checked' : ''; ?>>Modify</option>
                                                                <option value="Disconnect" <?= (old('order_type') == 'Disconnect') ? 'checked' : ''; ?>>Disconnect</option>
                                                                <option value="Suspend" <?= (old('order_type') == 'Suspend') ? 'checked' : ''; ?>>Suspend</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="nama" class="col-12 col-md-3 col-lg-3 col-form-label">NAMA*</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control <?= ($validation->hasError('nama')) ? 'is-invalid' : ''; ?>" id="nama" name="nama" placeholder="nama" value="<?= old('nama'); ?>">
                                                        <div class="invalid-feedback">
                                                            <?= $validation->getError('nama'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="alamat" class="col-12">ALAMAT</label>
                                                    <div class="col-12 col-lg-12">
                                                        <textarea type="text" class="form-control" style="height: 100%;" id="alamat" name="alamat" placeholder="alamat" value="<?= old('alamat'); ?>"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="tag_lokasi">TAG_LOKASI</label>
                                                            <input type="text" class="form-control" id="tag_lokasi" name="tag_lokasi" placeholder="tag_lokasi" value="<?= old('tag_lokasi'); ?>">
                                                        </div>
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="service_id">SERVICE_ID</label>
                                                            <input type="text" class="form-control" id="service_id" name="service_id" placeholder="service_id" value="<?= old('service_id'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-plain">
                                        <div class="card-header" role="tab" id="headingTwo">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                #METRO-OLT

                                                <i class="fas fa-chevron-down text-right"></i>
                                            </a>
                                        </div>
                                        <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                            <div class="card-body">
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="hostname_metro">HOSTNAME_METRO</label>
                                                            <select class="form-control custom-select" id="hostname_metro" name="hostname_metro" placeholder="hostname_metro" value="<?= old('hostname_metro'); ?>">
                                                                <option value="-" hidden>Choose...</option>
                                                                <?php foreach ($dataMetro as $d) : ?>
                                                                    <option value="<?= old('hostname_metro'); ?>" hidden <?= old('hostname_metro') == $d->hostname_metro ? 'selected' : null ?>><?= old('hostname_metro'); ?></option>
                                                                    <option value="<?= $d->hostname_metro ?>"><?= $d->hostname_metro ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="port_metro">PORT_METRO</label>
                                                            <input type="text" class="form-control" id="port_metro" name="port_metro" placeholder="port_metro" value="<?= old('port_metro'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="hostname_olt">HOSTNAME_OLT</label>
                                                            <div class="form-group mb-0">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="hostname_olt" name="hostname_olt" placeholder="hostname_olt" value="<?= old('hostname_olt'); ?>">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#hostname_olt_modal"><i class="fa fa-search"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="ip_olt">IP_OLT</label>
                                                            <input type="text" class="form-control" id="ip_olt" name="ip_olt" placeholder="ip_olt" value="<?= old('ip_olt'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="port_onu" class="col-12">PORT_ONU</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="port_onu" name="port_onu" placeholder="port_onu" value="<?= old('port_onu'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-plain">
                                        <div class="card-header" role="tab" id="headingThree">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                #ONT

                                                <i class="fas fa-chevron-down text-right"></i>
                                            </a>
                                        </div>
                                        <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                                            <div class="card-body">
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="hostname_ont">HOSTNAME_ONT</label>
                                                            <input type="text" class="form-control" id="hostname_ont" name="hostname_ont" placeholder="hostname_ont" value="<?= old('hostname_ont'); ?>">
                                                        </div>
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="ip_ont">IP_ONT</label>
                                                            <input type="text" class="form-control" id="ip_ont" name="ip_ont" placeholder="ip_ont" value="<?= old('ip_ont'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="ont_type">ONT_TYPE</label>
                                                            <input type="text" class="form-control" id="ont_type" name="ont_type" placeholder="ont_type" value="<?= old('ont_type'); ?>">
                                                        </div>
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="serial_number">SERIAL_NUMBER</label>
                                                            <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="serial_number" value="<?= old('serial_number'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="vlan">VLAN</label>
                                                            <input type="text" class="form-control" id="vlan" name="vlan" placeholder="vlan" value="<?= old('vlan'); ?>">
                                                        </div>
                                                        <div class="form-group col-lg-6 col-sm-12">
                                                            <label for="bandwidth">BANDWIDTH</label>
                                                            <input type="number" step="any" class="form-control" id="bandwidth" name="bandwidth" placeholder="Mbps" value="<?= old('bandwidth'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-plain">
                                        <div class="card-header" role="tab" id="headingFour">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                #OTHERS_DATA

                                                <i class="fas fa-chevron-down text-right"></i>
                                            </a>
                                        </div>
                                        <div id="collapseFour" class="collapse" role="tabpanel" aria-labelledby="headingFour">
                                            <div class="card-body">
                                                <div class="form-group row mb-0">
                                                    <div class="form-row col-12 col-lg-12">
                                                        <div class="form-group col-lg-4 col-sm-12">
                                                            <label for="odc">ODC</label>
                                                            <input type="text" class="form-control" id="odc" name="odc" placeholder="odc" value="<?= old('odc'); ?>">
                                                        </div>
                                                        <div class="form-group col-lg-5 col-sm-12">
                                                            <label for="odp">ODP</label>
                                                            <input type="text" class="form-control" id="odp" name="odp" placeholder="odp" value="<?= old('odp'); ?>">
                                                        </div>
                                                        <div class="form-group col-lg-3 col-sm-12">
                                                            <label for="p_tarikan">P_TARIKAN</label>
                                                            <input type="number" step="any" class="form-control" id="p_tarikan" name="p_tarikan" placeholder="Meter" value="<?= old('p_tarikan'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-3">
                                                    <label for="status" class="col-12 col-md-3 col-lg-3 col-form-label">STATUS</label>
                                                    <div class="col-12 col-lg-12 selectgroup w-100">
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="status" value="OPEN" class="selectgroup-input" <?= (old('status') == 'OPEN') ? 'checked' : ''; ?>>
                                                            <span class="selectgroup-button">OPEN</span>
                                                        </label>
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="status" value="CLOSE" class="selectgroup-input" <?= (old('status') == 'CLOSE') ? 'checked' : ''; ?>>
                                                            <span class="selectgroup-button">CLOSE</span>
                                                        </label>
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="status" value="REJECT" class="selectgroup-input" <?= (old('status') == 'REJECT') ? 'checked' : ''; ?>>
                                                            <span class="selectgroup-button">REJECT</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="desc" class="col-12 col-md-3 col-lg-3 col-form-label">DESC</label>
                                                    <div class="col-12 col-lg-12">
                                                        <input type="text" class="form-control" id="desc" name="desc" placeholder="desc" value="<?= old('desc'); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="desc" class="col-12 col-md-3 col-lg-3 col-form-label">EVIDENCE</label>
                                                    <div class="col-12 col-lg-12">
                                                        <div class="file-drop-area">
                                                            <span class="btn btn-sm btn-info mr-2">Choose files</span>
                                                            <span class="file-message">or drag and drop files here</span>
                                                            <input class="file-input  <?= ($validation->hasError('evidence')) ? 'is-invalid' : ''; ?>" type="file" name="evidence" id="evidence">
                                                            <div class="invalid-feedback">
                                                                <?= $validation->getError('evidence'); ?>
                                                            </div>
                                                        </div>
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
                                <button type="submit" class="btn btn-primary" onclick="oltNull()"><i class="fas fa-server"></i> Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>


<!-- The Modal OLT-->
<div class="modal fade" id="hostname_olt_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title text-white">Data OLT</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body table-responsive">
                <table class="table table-striped table-hover table-borderless table-sm" id="modalTable">
                    <thead>
                        <tr>
                            <th>Hostname_OLT</th>
                            <th>IP_OLT</th>
                            <th>Platform</th>
                            <th>Action</th>
                        </tr>
                    <tbody>
                        <?php foreach ($dataOlt as $d) : ?>
                            <tr>
                                <td><?= $d->hostname_olt; ?></td>
                                <td><?= $d->ip_olt; ?></td>
                                <td><?= $d->platform; ?></td>
                                <td class="text-right">
                                    <button class="btn btn-sm btn-info" id="select" data-hostname_metro="<?= $d->hostname_metro; ?>" data-port_metro="<?= $d->port_metro; ?>" data-hostname_olt="<?= $d->hostname_olt; ?>" data-ip_olt="<?= $d->ip_olt; ?>">
                                        <i class="fa fa-check"></i> Select
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    </thead>
                </table>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('select-olt') ?>

<script>
    $(document).ready(function() {
        $(document).on('click', '#select', function() {
            var hostname_metro = $(this).data('hostname_metro');
            var port_metro = $(this).data('port_metro');
            var hostname_olt = $(this).data('hostname_olt');
            var ip_olt = $(this).data('ip_olt');
            $('#hostname_metro').val(hostname_metro);
            $('#port_metro').val(port_metro);
            $('#hostname_olt').val(hostname_olt);
            $('#ip_olt').val(ip_olt);
            $('#hostname_olt_modal').modal('hide');

        })
    })


    function oltNull() {
        if (document.getElementById("hostname_metro").value == "-") {
            $('#hostname_metro').val('-');
            $('#hostname_olt').val('-');
        } else if (document.getElementById("hostname_metro").value != "-" && document.getElementById("hostname_olt").value.length == 0) {
            $('#hostname_olt').val('DIRECT_METRO');
        }
    }
</script>

<?= $this->endSection(); ?>