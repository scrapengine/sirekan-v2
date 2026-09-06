<?= $this->extend('layout/default') ?>

<?= $this->section('select2-css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/select2/dist/css/select2.min.css">
<?= $this->endSection(); ?>
<?= $this->section('timepicker-css') ?>
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/template/node_modules/bootstrap-daterangepicker/daterangepicker.css">
<?= $this->endSection(); ?>

<?= $this->section('content') ?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header mb-3">
            <div class="section-header-back">
                <a href="<?= base_url('ccan/assurance'); ?> " class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="font-weight-bold" style="font-size: 1rem;">ADD DATA ASSURANCE</div>
        </div>
        <!-- alert -->
        <?php if ($validation->getErrors()) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error! </strong>
                <?= ($validation->hasError('worklogs')) ? "<br>Worklogs &bull; " . $validation->getError('worklogs') : "" ?>
                <?= ($validation->hasError('evidence')) ? "<br>Evidence &bull; " . $validation->getError('evidence') : "" ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <!-- end alert -->
        
        <?php if (session()->getFlashData('error')) : ?>
            <div id="flash" data-icon="error" data-title="Error!" data-flash="<?= session()->getFlashData('error'); ?>"></div>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error! </strong> <?= session()->getFlashData('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="section-body">
            <div class="card card-primary">
                <div class="card-body">
                    <ul class="nav nav-pills justify-content-center" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="ticket-tab" data-toggle="tab" href="#ticket" role="tab" aria-controls="ticket" aria-selected="true">Ticket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="no-ticket-tab" data-toggle="tab" href="#no-ticket" role="tab" aria-controls="no-ticket" aria-selected="false">Tanpa Ticket</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="ticket" role="tabpanel" aria-labelledby="ticket-tab">
                            <div class="card-body col-md-12 pt-0">
                                <form action="/ccan/assurance" method="post" enctype="multipart/form-data">
                                    <?= csrf_field(); ?>
                                    <div class="form-group row mb-4 justify-content-center">
                                        <div class="col-sm-12 col-md-10">
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
                                                            <div class="row ">
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Incident</label>
                                                                        <input type="text" name="incident" id="incident" class="form-control bg-white <?= ($validation->hasError('incident')) ? 'is-invalid' : ''; ?>" value="<?= old('incident'); ?>">
                                                                        <div class="invalid-feedback">
                                                                            <?= $validation->getError('incident'); ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Customer_Name</label>
                                                                        <input type="text" name="customer_name" id="customer_name" class="form-control bg-white" value="<?= old('customer_name'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">External_TicketID</label>
                                                                        <input type="text" name="external_ticketid" id="external_ticketid" class="form-control bg-white" value="<?= old('external_ticketid'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">Summary</label>
                                                                        <textarea class="form_edit form-control bg-white" style="height: 200%;" name="summary" id="summary" rows="3"><?= old('summary'); ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">RCA</label>
                                                                        <textarea class="form_edit form-control bg-white" style="height: 200%;" name="rca" id="rca" rows="3"><?= old('rca'); ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="" class="">Owner</label>
                                                                        <input type="text" name="owner" id="owner" class=" form-control bg-white" value="<?= old('owner'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="" class="">Owner_Group</label>
                                                                        <input type="text" name="owner_group" id="owner_group" class=" form-control bg-white" value="<?= old('owner_group'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Reported_Date</label>
                                                                        <input type="text" name="reported_date" id="reported_date" class="form-control bg-white datetimepicker" value="<?= old('reported_date'); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Service_No</label>
                                                                        <input type="text" name="service_no" id="service_no" class="form_edit form-control bg-white" value="<?= old('service_no'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Service_Type</label>
                                                                        <input type="text" name="service_type" id="service_type" class="form_edit form-control bg-white" value="<?= old('service_type'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Customer_Segment</label>
                                                                        <select class="form-control custom-select" id="customer_segment" name="customer_segment" placeholder="customer_segment" value="<?= old('customer_segment'); ?>">
                                                                            <option value="" hidden>Choose...</option>
                                                                            <option value="DBS" <?= (old('customer_segment') == 'DBS') ? 'selected' : ''; ?>>DBS</option>
                                                                            <option value="DCS" <?= (old('customer_segment') == 'DCS') ? 'selected' : ''; ?>>DCS</option>
                                                                            <option value="DES" <?= (old('customer_segment') == 'DES') ? 'selected' : ''; ?>>DES</option>
                                                                            <option value="DGS" <?= (old('customer_segment') == 'DGS') ? 'selected' : ''; ?>>DGS</option>
                                                                            <option value="DWS" <?= (old('customer_segment') == 'DWS') ? 'selected' : ''; ?>>DWS</option>
                                                                            <option value="UNS" <?= (old('customer_segment') == 'UNS') ? 'selected' : ''; ?>>UNSEGMENT</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Lapul</label>
                                                                        <input type="text" name="lapul" id="lapul" class="form_edit form-control bg-white" value="<?= old('lapul'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Gaul</label>
                                                                        <input type="text" name="gaul" id="gaul" class="form_edit form-control bg-white" value="<?= old('gaul'); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Actual_Solution</label>
                                                                        <input type="text" name="actual_solution" id="actual_solution" class="form_edit form-control bg-white" value="<?= old('actual_solution'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Incident_Domain</label>
                                                                        <input type="text" name="incident_domain" id="incident_domain" class="form_edit form-control bg-white" value="<?= old('incident_domain'); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-2 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">TTR_Customer</label>
                                                                        <input type="text" name="ttr_customer" id="ttr_customer" class=" form-control bg-white" value="<?= old('ttr_customer'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">TTR_Nasional</label>
                                                                        <input type="text" name="ttr_nasional" id="ttr_nasional" class=" form-control bg-white" value="<?= old('ttr_nasional'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">TTR_Regional</label>
                                                                        <input type="text" name="ttr_regional" id="ttr_regional" class=" form-control bg-white" value="<?= old('ttr_regional'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">TTR_Witel</label>
                                                                        <input type="text" name="ttr_witel" id="ttr_witel" class=" form-control bg-white" value="<?= old('ttr_witel'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">TTR_Mitra</label>
                                                                        <input type="text" name="ttr_mitra" id="ttr_mitra" class=" form-control bg-white" value="<?= old('ttr_mitra'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">TTR_Agent</label>
                                                                        <input type="text" name="ttr_agent" id="ttr_agent" class=" form-control bg-white" value="<?= old('ttr_agent'); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-2 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">TTR_Pending</label>
                                                                        <input type="text" name="ttr_pending" id="ttr_pending" class=" form-control bg-white" value="<?= old('ttr_pending'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-10 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Pending_Reason</label>
                                                                        <input type="text" name="pending_reason" id="pending_reason" class=" form-control bg-white" value="<?= old('pending_reason'); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-3 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Status</label>
                                                                        <select class="form-control custom-select" id="status" name="status" placeholder="status" value="<?= old('status'); ?>">
                                                                            <option value="" hidden>Choose...</option>
                                                                            <option value="BACKEND" <?= (old('status') == 'BACKEND') ? 'selected' : ''; ?>>BACKEND</option>
                                                                            <option value="CLOSED" <?= (old('status') == 'CLOSED') ? 'selected' : ''; ?>>CLOSED</option>
                                                                            <option value="DRAFT" <?= (old('status') == 'DRAFT') ? 'selected' : ''; ?>>DRAFT</option>
                                                                            <option value="FINALCHECK" <?= (old('status') == 'FINALCHECK') ? 'selected' : ''; ?>>FINALCHECK</option>
                                                                            <option value="HISTEDIT" <?= (old('status') == 'HISTEDIT') ? 'selected' : ''; ?>>HISTEDIT</option>
                                                                            <option value="INPROG" <?= (old('status') == 'INPROG') ? 'selected' : ''; ?>>INPROG</option>
                                                                            <option value="MEDIACARE" <?= (old('status') == 'MEDIACARE') ? 'selected' : ''; ?>>MEDIACARE</option>
                                                                            <option value="NEW" <?= (old('status') == 'NEW') ? 'selected' : ''; ?>>NEW</option>
                                                                            <option value="PENDING" <?= (old('status') == 'PENDING') ? 'selected' : ''; ?>>PENDING</option>
                                                                            <option value="PENDINGS" <?= (old('status') == 'PENDINGS') ? 'selected' : ''; ?>>PENDINGS</option>
                                                                            <option value="QUEUED" <?= (old('status') == 'QUEUED') ? 'selected' : ''; ?>>QUEUED</option>
                                                                            <option value="RESOLVED" <?= (old('status') == 'RESOLVED') ? 'selected' : ''; ?>>RESOLVED</option>
                                                                            <option value="SALAMSIM" <?= (old('status') == 'SALAMSIM') ? 'selected' : ''; ?>>SALAMSIM</option>
                                                                            <option value="SLAHOLD" <?= (old('status') == 'SLAHOLD') ? 'selected' : ''; ?>>SLAHOLD</option>
                                                                            <option value="WAIT" <?= (old('status') == 'WAIT') ? 'selected' : ''; ?>>WAIT</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-sm-12 pl-0">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Status_Date</label>
                                                                        <input type="text" name="status_date" id="status_date" class=" form-control bg-white datetimepicker" value="<?= old('status_date'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Resolved_By</label>
                                                                        <input type="text" name="resolved_by" id="resolved_by" class=" form-control bg-white" value="<?= old('resolved_by'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-sm-12 pl-0">
                                                                    <div class="form-group">
                                                                        <label for="">Resolved_Date</label>
                                                                        <input type="text" name="resolved_date" id="resolved_date" class="form-control bg-white datetimepicker" value="<?= old('resolved_date'); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Workzone</label>
                                                                        <select class="form-control custom-select ?>" id="workzone" name="workzone" placeholder="workzone" value="<?= old('workzone'); ?>">
                                                                            <option value="" hidden>Choose...</option>
                                                                            <?php foreach ($dataSto as $d) : ?>
                                                                                <option value="<?= old('workzone'); ?>" hidden <?= old('workzone') == $d->idsto ? 'selected' : null ?>><?= old('workzone'); ?></option>
                                                                                <option value="<?= $d->idsto ?>"><?= $d->idsto ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Witel</label>
                                                                        <input type="text" name="witel" id="witel" class=" form-control bg-white" value="<?= old('witel'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Regional</label>
                                                                        <input type="text" name="regional" id="regional" class=" form-control bg-white" value="<?= old('regional'); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card card-plain">
                                                    <div class="card-header" role="tab" id="headingTwo">
                                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                            #PROGRESS_BY

                                                            <i class="fas fa-chevron-down text-right"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                                        <div class="card-body">
                                                            <div class="row ">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">Petugas</label>
                                                                        <select class="form-control select2 <?= ($validation->hasError('petugas')) ? 'is-invalid' : ''; ?>" id="petugas" name="petugas[]" placeholder="petugas" value="<?= old('petugas'); ?>" multiple="">
                                                                            <?php foreach ($dataNaker as $d) : ?>
                                                                                <option value="<?= $d->idnaker ?>"><?= $d->nama ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                                            <div class="form-group row">
                                                                <label for="" class="col-12 col-md-3 col-lg-3 col-form-label">File Excel</label>
                                                                <div class="col-12 col-lg-12">
                                                                    <div class="file-drop-area">
                                                                        <span class="btn btn-sm btn-info mr-2">Choose files</span>
                                                                        <span class="file-message">or drag and drop files here</span>
                                                                        <input class="file-input  <?= ($validation->hasError('worklogs')) ? 'is-invalid' : ''; ?>" type="file" name="worklogs" id="worklogs">
                                                                        <div class="invalid-feedback">
                                                                            <?= $validation->getError('worklogs'); ?>
                                                                        </div>
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
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-server"></i> Save</button>
                                            <button type="reset" class="btn btn-secondary">Reset</i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="no-ticket" role="tabpanel" aria-labelledby="no-ticket-tab">
                            <div class="card-body col-md-12 pt-0">
                                <form action="/ccan/assurance" method="post" enctype="multipart/form-data">
                                    <?= csrf_field(); ?>
                                    <div class="form-group row mb-4 justify-content-center">
                                        <div class="col-sm-12 col-md-10">
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
                                                            <div class="row ">
                                                                <div class="col-lg-6 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Incident</label>
                                                                        <input type="text" name="incident" id="incident" class="form-control bg-white <?= ($validation->hasError('incident')) ? 'is-invalid' : ''; ?>" value="<?= old('customer_name', "NO TICKET"); ?>" readonly>
                                                                        <div class="invalid-feedback">
                                                                            <?= $validation->getError('incident'); ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Customer_Name</label>
                                                                        <input type="text" name="customer_name" id="customer_name" class="form-control bg-white" value="<?= old('customer_name'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">Summary</label>
                                                                        <textarea class="form_edit form-control bg-white" style="height: 200%;" name="summary" id="summary" rows="3"><?= old('summary'); ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">RCA</label>
                                                                        <textarea class="form_edit form-control bg-white" style="height: 200%;" name="rca" id="rca" rows="3"><?= old('rca'); ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-3 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Reported_Date</label>
                                                                        <input type="text" name="reported_date" id="reported_date" class="form-control bg-white datetimepicker" value="<?= old('reported_date'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Service_No</label>
                                                                        <input type="text" name="service_no" id="service_no" class="form_edit form-control bg-white" value="<?= old('service_no'); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Service_Type</label>
                                                                        <select class="form-control custom-select" id="service_type" name="service_type" placeholder="service_type" value="<?= old('service_type'); ?>">
                                                                            <option value="" hidden>Choose...</option>
                                                                            <option value="INTERNET" <?= (old('service_type') == 'INTERNET') ? 'selected' : ''; ?>>INTERNET</option>
                                                                            <option value="VOICE" <?= (old('service_type') == 'VOICE') ? 'selected' : ''; ?>>VOICE</option>
                                                                            <option value="IPTV" <?= (old('service_type') == 'IPTV') ? 'selected' : ''; ?>>IPTV</option>
                                                                            <option value="MM_IPVPN" <?= (old('service_type') == 'MM_IPVPN') ? 'selected' : ''; ?>>MM_IPVPN</option>
                                                                            <option value="MM_ASTINET" <?= (old('service_type') == 'MM_ASTINET') ? 'selected' : ''; ?>>MM_ASTINET</option>
                                                                            <option value="MM_METRO_ETHERNET" <?= (old('service_type') == 'MM_METRO_ETHERNET') ? 'selected' : ''; ?>>MM_METRO_ETHERNET</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Customer_Segment</label>
                                                                        <select class="form-control custom-select" id="customer_segment" name="customer_segment" placeholder="customer_segment" value="<?= old('customer_segment'); ?>">
                                                                            <option value="" hidden>Choose...</option>
                                                                            <option value="DBS" <?= (old('customer_segment') == 'DBS') ? 'selected' : ''; ?>>DBS</option>
                                                                            <option value="DCS" <?= (old('customer_segment') == 'DCS') ? 'selected' : ''; ?>>DCS</option>
                                                                            <option value="DES" <?= (old('customer_segment') == 'DES') ? 'selected' : ''; ?>>DES</option>
                                                                            <option value="DGS" <?= (old('customer_segment') == 'DGS') ? 'selected' : ''; ?>>DGS</option>
                                                                            <option value="DWS" <?= (old('customer_segment') == 'DWS') ? 'selected' : ''; ?>>DWS</option>
                                                                            <option value="UNS" <?= (old('customer_segment') == 'UNS') ? 'selected' : ''; ?>>UNSEGMENT</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row ">
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Workzone</label>
                                                                        <select class="form-control custom-select ?>" id="workzone" name="workzone" placeholder="workzone" value="<?= old('workzone'); ?>">
                                                                            <option value="" hidden>Choose...</option>
                                                                            <?php foreach ($dataSto as $d) : ?>
                                                                                <option value="<?= old('workzone'); ?>" hidden <?= old('workzone') == $d->idsto ? 'selected' : null ?>><?= old('workzone'); ?></option>
                                                                                <option value="<?= $d->idsto ?>"><?= $d->idsto ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="" for="">Status</label>
                                                                        <select class="form-control custom-select" id="status" name="status" placeholder="status" value="<?= old('status'); ?>">
                                                                            <option value="" hidden>Choose...</option>
                                                                            <option value="BACKEND" <?= (old('status') == 'BACKEND') ? 'selected' : ''; ?>>BACKEND</option>
                                                                            <option value="CLOSED" <?= (old('status') == 'CLOSED') ? 'selected' : ''; ?>>CLOSED</option>
                                                                            <option value="DRAFT" <?= (old('status') == 'DRAFT') ? 'selected' : ''; ?>>DRAFT</option>
                                                                            <option value="FINALCHECK" <?= (old('status') == 'FINALCHECK') ? 'selected' : ''; ?>>FINALCHECK</option>
                                                                            <option value="HISTEDIT" <?= (old('status') == 'HISTEDIT') ? 'selected' : ''; ?>>HISTEDIT</option>
                                                                            <option value="INPROG" <?= (old('status') == 'INPROG') ? 'selected' : ''; ?>>INPROG</option>
                                                                            <option value="MEDIACARE" <?= (old('status') == 'MEDIACARE') ? 'selected' : ''; ?>>MEDIACARE</option>
                                                                            <option value="NEW" <?= (old('status') == 'NEW') ? 'selected' : ''; ?>>NEW</option>
                                                                            <option value="PENDING" <?= (old('status') == 'PENDING') ? 'selected' : ''; ?>>PENDING</option>
                                                                            <option value="PENDINGS" <?= (old('status') == 'PENDINGS') ? 'selected' : ''; ?>>PENDINGS</option>
                                                                            <option value="QUEUED" <?= (old('status') == 'QUEUED') ? 'selected' : ''; ?>>QUEUED</option>
                                                                            <option value="RESOLVED" <?= (old('status') == 'RESOLVED') ? 'selected' : ''; ?>>RESOLVED</option>
                                                                            <option value="SALAMSIM" <?= (old('status') == 'SALAMSIM') ? 'selected' : ''; ?>>SALAMSIM</option>
                                                                            <option value="SLAHOLD" <?= (old('status') == 'SLAHOLD') ? 'selected' : ''; ?>>SLAHOLD</option>
                                                                            <option value="WAIT" <?= (old('status') == 'WAIT') ? 'selected' : ''; ?>>WAIT</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="">Resolved_Date</label>
                                                                        <input type="text" name="resolved_date" id="resolved_date" class="form-control bg-white datetimepicker" value="<?= old('resolved_date',null); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card card-plain">
                                                    <div class="card-header" role="tab" id="headingTwo">
                                                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                            #PROGRESS_BY

                                                            <i class="fas fa-chevron-down text-right"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                                        <div class="card-body">
                                                            <div class="row ">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">Petugas</label>
                                                                        <select class="form-control select2 <?= ($validation->hasError('petugas')) ? 'is-invalid' : ''; ?>" id="petugas" name="petugas[]" placeholder="petugas" value="<?= old('petugas'); ?>" multiple="">
                                                                            <?php foreach ($dataNaker as $d) : ?>
                                                                                <option value="<?= $d->idnaker ?>"><?= $d->nama ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                                            <div class="form-group row">
                                                                <label for="" class="col-12 col-md-3 col-lg-3 col-form-label">File Excel</label>
                                                                <div class="col-12 col-lg-12">
                                                                    <div class="file-drop-area">
                                                                        <span class="btn btn-sm btn-info mr-2">Choose files</span>
                                                                        <span class="file-message">or drag and drop files here</span>
                                                                        <input class="file-input  <?= ($validation->hasError('worklogs')) ? 'is-invalid' : ''; ?>" type="file" name="worklogs" id="worklogs">
                                                                        <div class="invalid-feedback">
                                                                            <?= $validation->getError('worklogs'); ?>
                                                                        </div>
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
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-server"></i> Save</button>
                                            <button type="reset" class="btn btn-secondary">Reset</i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?= $this->endSection(); ?>

<?= $this->section('select2-js') ?>
<script src="<?= base_url() ?>/template/node_modules/select2/dist/js/select2.full.min.js"></script>
<?= $this->endSection(); ?>
<?= $this->section('timepicker-js') ?>
<script src="<?= base_url() ?>/template/node_modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="<?= base_url() ?>/template/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<?= $this->endSection(); ?>