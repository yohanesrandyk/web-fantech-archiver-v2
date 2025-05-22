<div class="card card-custom mb-5">
    <div class="card-header flex-wrap">
        <div class="card-title">
            <h2 class="card-label font-weight-bolder text-danger">
                <i class="fa fa-dashboard"></i>
                DASHBOARD
            </h2>
        </div>
        <div class="card-toolbar">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 mb-5">
        <div class="card card-custom bg-primary text-white">
            <div class="card-body">
                <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">TOTAL CASH ADVANCE</div>
                <div class="font-size-h3 font-weight-bolder mr-2">
                    Rp. <?= number_format($document_sum_ca['total']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-5">
        <div class="card card-custom bg-primary text-white">
            <div class="card-body">
                <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">TOTAL PAYMENT REQUEST</div>
                <div class="font-size-h3 font-weight-bolder mr-2">
                    Rp. <?= number_format($document_sum_pc['total']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-5">
        <div class="card card-custom bg-primary text-white">
            <div class="card-body">
                <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">TOTAL REIMBURSEMENT</div>
                <div class="font-size-h3 font-weight-bolder mr-2">
                    Rp. <?= number_format($document_sum_pp['total']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-5">
    <div class="col-lg-12 mb-5">
        <div class="card card-custom h-100">
            <div class="card-header flex-wrap">
                <div class="card-title">
                    <span class="card-label font-weight-bolder">
                        DOKUMEN PENDING
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-5 table-responsive" id="table-pending">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NO. DOKUMEN</th>
                            <th nowrap>TIPE DOKUMEN</th>
                            <th>TANGGAL</th>
                            <th>STATUS</th>
                            <th nowrap></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($document_pending as $row) {
                            $doctype_code = strtolower($row['doctype_code']);
                            if ($doctype_code != 'ca' && $doctype_code != 'pc' && $doctype_code != 'pp' && $doctype_code != 'pr') {
                                $doctype_code = 'all';
                            }
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['document_number'] ?></td>
                                <td><?= $row['doctype_name'] ?></td>
                                <td><?= custom_date_format($row['create_date'], 'd/m/Y') ?></td>
                                <td><span class="label label-<?= get_status_color($row['status']) ?> label-inline font-weight-lighter mr-2"><?= $row['docstatus_name'] ?></span></td>
                                <td nowrap><a href="<?= base_url() ?>document/form/<?= $doctype_code ?>/<?= $row['id'] ?>" class="btn btn-success btn-sm"><i class="la la-file"></i> APPROVE</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-5 d-none">
        <div class="card card-custom h-100">
            <div class="card-header flex-wrap">
                <div class="card-title">
                    <span class="card-label font-weight-bolder">
                        DOKUMEN SUBMIT
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-5 table-responsive" id="table-submit">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>NO. DOKUMEN</th>
                            <th nowrap>TIPE DOKUMEN</th>
                            <th>TANGGAL</th>
                            <th>STATUS</th>
                            <th nowrap></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($document_submit as $row) {
                            $doctype_code = strtolower($row['doctype_code']);
                            if ($doctype_code != 'ca' && $doctype_code != 'pc' && $doctype_code != 'pp' && $doctype_code != 'pr') {
                                $doctype_code = 'all';
                            }
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['document_number'] ?></td>
                                <td><?= $row['doctype_name'] ?></td>
                                <td><?= custom_date_format($row['create_date'], 'd/m/Y') ?></td>
                                <td><span class="label label-<?= get_status_color($row['status']) ?> label-inline font-weight-lighter mr-2"><?= $row['docstatus_name'] ?></span></td>
                                <td nowrap><a href="<?= base_url() ?>document/form/<?= $doctype_code ?>/<?= $row['id'] ?>" class="btn btn-primary btn-sm"><i class="la la-file"></i> REVIEW</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets') ?>/js/chart.js"></script>
<script>
    $(document).ready(function() {
        $("#table-pending").DataTable();
        $("#table-submit").DataTable();
    });
</script>