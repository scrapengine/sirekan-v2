<?= $this->extend('layout/default') ?>

<?= $this->section('chocolat-css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/chocolat/dist/css/chocolat.css">
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3 px-3">
            <div class="section-header-back">
                <a href="<?= base_url('wan/olo'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">DATA OLO</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 p-0">
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
                                    <div class="row">
                                        <div class="col-12">
                                            <table class="table table-sm table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 30%">Service ID</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['service_id'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nama Pelanggan</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['nama'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Alamat</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['alamat_olo'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tag Lokasi</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['tag_lokasi'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>STO</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['idsto_olo'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Hostname Metro</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['hostname_metro_olo'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>IP Metro</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['ip_metro'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Port Metro</th>
                                                        <td>:</td>
                                                        <td><?= ($dataOlo[0]['port_metro_olt'] != null) ? $dataOlo[0]['port_metro_olt'] : $dataOlo[0]['port_metro_olo'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Hostname OLT</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['hostname_olt_olo'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>IP OLT</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['ip_olt'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Port Onu</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['port_onu'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Vlan</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['vlan'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <?php if (!is_null($dataOlo[0]['hostname_ont'])) : ?>
                                                        <tr>
                                                            <th>Hostname ONT</th>
                                                            <td>:</td>
                                                            <td><?= $dataOlo[0]['hostname_ont'] ?></td>
                                                        </tr>
                                                    <?php endif ?>
                                                    <?php if (!is_null($dataOlo[0]['ip_ont'])) : ?>
                                                        <tr>
                                                            <th>IP ONT</th>
                                                            <td>:</td>
                                                            <td><?= $dataOlo[0]['ip_ont'] ?></td>
                                                        </tr>
                                                    <?php endif ?>
                                                    <tr>
                                                        <th>ONT Type</th>
                                                        <td>:</td>
                                                        <td><?= strtoupper($dataOlo[0]['ont_type']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Serial Number</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['serial_number'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>ODC</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['odc'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>ODP</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['odp'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Keterangan</th>
                                                        <td>:</td>
                                                        <td><?= $dataOlo[0]['desc'] ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-plain">
                            <div class="card-header" role="tab" id="headingTwo">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    #TICKETS

                                    <i class="fas fa-chevron-down text-right"></i>
                                </a>
                            </div>
                            <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="card-body">
                                    <table class="table table-sm table-borderedless">
                                        <tr>
                                            <th>Incident</th>
                                            <th>Reported Date</th>
                                            <th>TTR Customer</th>
                                            <th>Incident Domain</th>
                                        </tr>
                                        <tbody>
                                            <?php foreach ($dataAsrwan as $d) : ?>
                                                <tr>
                                                    <td><?= $d->incident ?></td>
                                                    <td><?= $d->reported_date ?></td>
                                                    <td><?= $d->ttr_customer ?></td>
                                                    <td><?= $d->incident_domain ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card card-plain">
                            <div class="card-header" role="tab" id="headingThree">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    #WORK_LOGS

                                    <i class="fas fa-chevron-down text-right"></i>
                                </a>
                            </div>
                            <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                                <div class="card-body">
                                    <table class="table table-sm table-borderedless">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No Order</th>
                                            <th>Order Type</th>
                                            <th>Bandwidth</th>
                                            <th>Status</th>
                                        </tr>
                                        <tbody>
                                            <?php foreach ($dataFfwan as $d) : ?>
                                                <tr>
                                                    <td><?= $d->tanggal ?></td>
                                                    <td><?= $d->no_order ?></td>
                                                    <td><?= $d->order_type ?></td>
                                                    <td><?= $d->bandwidth ?></td>
                                                    <td><?= $d->status ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card card-plain mb-0">
                            <div class="card-header" role="tab" id="headingFour">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    #OTHERS_DATA

                                    <i class="fas fa-chevron-down text-right"></i>
                                </a>
                            </div>
                            <div id="collapseFour" class="collapse" role="tabpanel" aria-labelledby="headingFour">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <tr>
                                                <?php if ($dataOlo[0]['evidence'] != '') : ?>
                                                    <th style="width: 30%">Evidence</th>
                                                    <th>:</th>
                                                    <td>
                                                        <div class="gallery gallery-md">
                                                            <div class="gallery-item" data-image="<?= ($dataOlo[0]['evidence'] != '') ? base_url('/img/fulfillment/' . $dataOlo[0]['evidence']) : ''; ?>" data-title="<?= $dataOlo[0]['service_id']; ?>"></div>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<div class="modal fade" id="ukurNodeModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">Hasil Ukur</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="bodymodal_detailData"></div>

            <!-- Modal footer -->
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function ukurolo(idolo) {
        $('#bodymodal_detailData').html(
            '<div class="text-center">' +
            '<h6>' +
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...' +
            '</h6>' +
            '</div>'
        );
        $.ajax({
            url: "<?= site_url('/wan/olo/ukurolo'); ?>",
            data: "idolo=" + idolo,
            dataType: "html",
            success: function(response) {
                $('#bodymodal_detailData').empty();
                $('#bodymodal_detailData').append(response);
            }
        });
    }
</script>


<?= $this->endSection(); ?>

<?= $this->section('chocolat-js') ?>
<script src="<?= base_url() ?>/template/node_modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
<?= $this->endSection(); ?>