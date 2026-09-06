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
                <a href="<?= base_url('wan/nodeb'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">DATA NODE-B</div>
        </div>
        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body col-md-12 p-0">
                    <!-- <div class="container mt-5">
                        <div id="gmapBlock"></div>
                    </div> -->
                    <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3981.050239221519!2d102.275046!3d-3.771156!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x6e7b3959b332039b!2zM8KwNDcnNTcuMiJTIDEwMsKwMTYnNTMuOCJF!5e0!3m2!1sid!2sid!4v1668163546041!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> -->
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
                                    <button type="button" class="btn btn-sm btn-primary mb-3" onclick="ukurNodeb(<?= $dataNodeb[0]['idnodeb'] ?>)" data-toggle="modal" data-target="#ukurNodeModal" data-backdrop="static" data-keyboard="false">UKUR NODE-B
                                        <i class='' data-toggle='tooltip' data-placement='bottom' title='Ukur'></i>
                                    </button>
                                    <div class="row">
                                        <div class="col-12">
                                            <table class="table table-sm table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 30%">Site ID</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['site_id'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Site Name</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['site_name'] ?></td>
                                                    </tr>
                                                    <?php if ($statusOnt !== 'direct') : ?>
                                                        <tr>
                                                            <th>Status</th>
                                                            <td>:</td>
                                                            <td>
                                                                <?php if ($statusOnt == 1) : ?>
                                                                    <h6>
                                                                        <div class="badge badge-success">Online</div>
                                                                    </h6>
                                                                <?php elseif ($statusOnt == 500) : ?>
                                                                    <h6>
                                                                        <div class="badge badge-warning">500 Internal Server Error</div>
                                                                    </h6>
                                                                <?php else : ?>
                                                                    <h6>
                                                                        <div class="badge badge-danger">Offline</div>
                                                                    </h6>
                                                                <?php endif ?>
                                                            </td>
                                                        </tr>
                                                    <?php endif ?>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>STO</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['idsto_nodeb'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Hostname Metro</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['hostname_metro_nodeb'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>IP Metro</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['ip_metro'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Port Metro</th>
                                                        <td>:</td>
                                                        <td><?= ($dataNodeb[0]['port_metro_olt'] != null) ? $dataNodeb[0]['port_metro_olt'] : $dataNodeb[0]['port_metro_nodeb'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Hostname OLT</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['hostname_olt_nodeb'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>IP OLT</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['ip_olt'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Port Onu</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['port_onu'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Hostname ONT</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['hostname_ont'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>IP ONT</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['ip_ont'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>ONT Type</th>
                                                        <td>:</td>
                                                        <td><?= strtoupper($dataNodeb[0]['ont_type']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Serial Number</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['serial_number'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <hr class="m-0">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>ODC</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['odc'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>ODP</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['odp'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Coordinate Site</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['tikor_site'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>On Air Date</th>
                                                        <td>:</td>
                                                        <td><?= $dataNodeb[0]['on_air'] ?></td>
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
                                            <th>Ticket Type</th>
                                        </tr>
                                        <tbody>
                                            <?php foreach ($dataAsrwan as $d) : ?>
                                                <tr>
                                                    <td><?= $d->incident ?></td>
                                                    <td><?= $d->reported_date ?></td>
                                                    <td><?= $d->ttr_customer ?></td>
                                                    <td><?= $d->kategori_site_tsel ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="card card-plain">
                            <div class="card-header" role="tab" id="headingThree">
                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    #WORK_LOGS

                                    <i class="fas fa-chevron-down text-right"></i>
                                </a>
                            </div>
                            <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                                <div class="card-body">
                                </div>
                            </div>
                        </div> -->
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
                                                <?php if ($dataNodeb[0]['evidence'] != '') : ?>
                                                    <th style="width: 30%">Evidence</th>
                                                    <th>:</th>
                                                    <td>
                                                        <div class="gallery gallery-md">
                                                            <div class="gallery-item" data-image="<?= ($dataNodeb[0]['evidence'] != '') ? base_url('/img/nodeb/' . $dataNodeb[0]['evidence']) : ''; ?>" data-title="<?= $dataNodeb[0]['site_id']; ?>"></div>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                            <tr>
                                                <?php if ($site_id != '') : ?>
                                                    <th style="width: 30%">Site</th>
                                                    <th>:</th>
                                                    <td>
                                                        <div class="gallery gallery-md">
                                                            <div class="gallery-item" data-image="<?= "data:image/jpeg;base64,".$site_id; ?>" data-title="<?= $dataNodeb[0]['site_id']; ?>"></div>
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
    function ukurNodeb(idnodeb) {
        $('#bodymodal_detailData').html(
            '<div class="text-center">' +
            '<h6>' +
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...' +
            '</h6>' +
            '</div>'
        );
        $.ajax({
            url: "<?= site_url('/wan/nodeb/ukurnodeb'); ?>",
            data: "idnodeb=" + idnodeb,
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