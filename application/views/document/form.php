<?php
$this->view('document/custom');

$isdisabled = true;

$statuscode = $document['status'] ?? null;
$approvecode = $document['approve_code'] ?? 'P';
$rejectcode = $document['reject_code'] ?? 'P';
$isnew = substr($approvecode, 0, 1) == 'P';
$isinform = ($type == 'all' && ($document['type'] ?? 'A') == 'A') || $type != 'all';
$iscanapprove = substr($approvecode, 0, 1) == 'A';
$iscanapprove = $iscanapprove && ($document['approve_code'] != 'A' || ($document['approve_code'] == 'A' && ($document['user_create_id'] ?? '') != $_SESSION['user']['id']));
$iscancomplete = $approvecode == 'A';
$isreview = (($document["from_division_id"] ?? $_SESSION['user']['division_id']) != $_SESSION['user']['division_id']
    && ($document["to_division_id"] ?? $_SESSION['user']['division_id']) != $_SESSION['user']['division_id']) ?? false;

echo "<script>console.log('isreview', '" . $isreview . "');console.log('isinform', '" . $isinform . "');console.log('isnew', '" . $isnew . "');console.log('iscanapprove', '" . $iscanapprove . "');console.log('iscancomplete', '" . $iscancomplete . "');</script>";

if (
    $isinform && $isnew
) {
    $isdisabled = false;
}

?>
<script>
    function calculate_item(e) {
        $(document).ready(function() {
            if ($(e).closest("tr").length > 0) {
                var unit = $(e).closest("tr").find(".unit").val().replace(/,/g, '');
                var price = $(e).closest("tr").find(".price").val().replace(/,/g, '');
                $(e).closest("tr").find(".subtotal").val(unit * price);

                setCurrency($(e).closest("tr").find(".unit"));
                setCurrency($(e).closest("tr").find(".price"));
                setCurrency($(e).closest("tr").find(".subtotal"));
            }

            var transfer = $("#transfer_amount").val().replace(/,/g, '');
            setCurrency($("#transfer_amount"));

            if ($('.unit').length) {
                var total = 0;
                var diff = 0;
                $('.subtotal').each(function() {
                    var total_ = parseInt($(this).val().replace(/,/g, ''));
                    if (Number.isNaN(total_)) total_ = 0;
                    total += total_;
                });
                diff = transfer - total;

                if (diff > 0) {
                    $("#mt_to_division_id").val("<?= $document['from_division_id'] ?? '0' ?>");
                } else {
                    $("#mt_to_division_id").val("2");
                }
                $("#mt_to_division_id").selectpicker('refresh');

                $("#sum-subtotal").text("Rp. " + formatNumber(transfer));
                $("#sum-used").text("Rp. " + formatNumber(total));
                $("#sum-diff").text("Rp. " + formatNumber(diff));
                if (diff == 0) {
                    $("#card-diff").removeClass("text-danger");
                    $("#card-diff").removeClass("bg-light-danger");
                    $("#card-diff").addClass("text-success");
                    $("#card-diff").addClass("bg-light-success");
                } else {
                    $("#card-diff").removeClass("text-success");
                    $("#card-diff").removeClass("bg-light-success");
                    $("#card-diff").addClass("text-danger");
                    $("#card-diff").addClass("bg-light-danger");
                }

                if (transfer < 500000) {
                    $("#text-errmsg").text("Minimal pengajuan 500,000.");
                    $("#btn-save").attr("onclick", "show_errmsg_modal()");
                } else {
                    $("#btn-save").attr("onclick", "save_approve()");
                }
            }
        });
    }

    function set_division(e) {
        var division = <?= json_encode($docstatus) ?>;
        var status = $(e).val();
        for (var i = 0; i < division.length; i++) {
            if (division[i].code == status) {
                $("#to_division_id").val(division[i].to_division_id);
                $("#to_division_id").selectpicker("refresh");
            }
        }
    }
</script>
<form action="<?= base_url() . "document/store/$type" ?>" method="POST" enctype="multipart/form-data">
    <div class="card card-custom bg-danger text-white mb-5">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <h2 class="card-label font-weight-bolder text-white">
                    <i class="fas fa-envelope text-white mr-4"></i>
                    <?= $title ?>
                </h2>
            </div>
            <div class="card-toolbar">
                <?php if (isset($document['id'])) { ?>
                    <a class="btn btn-dark font-weight-bolder ml-2" target="_blank" href="<?= base_url() . "document/cetak/$type/" . $document['id'] ?>">
                        <i class="la la-print"></i>
                        Cetak
                    </a>
                <?php } ?>
                <a class="btn btn-white font-weight-bolder text-danger ml-2" href="<?= base_url() . "document/index/$type" ?>">
                    <i class="la la-arrow-left text-danger"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <?php show_confirm_modal('form') ?>

    <div class="card card-custom mb-5">
        <div class="card-body">
            <ul class="nav nav-tabs" id="" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="form-tab" data-toggle="tab" href="#form">
                        <span class="nav-icon"><i class="la la-file-alt text-dark font-weight-bolder"></i></span>
                        <span class="nav-text font-weight-bolder text-dark">FORM</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history">
                        <span class="nav-icon"><i class="la la-clock text-dark font-weight-bolder"></i></span>
                        <span class="nav-text text-dark font-weight-bolder">HISTORY</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content mt-5" id="">
                <div class="tab-pane fade show active" id="form" role="tabpanel"
                    aria-labelledby="form-tab">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <input type="hidden" name="id" value="<?= $document['id'] ?? '' ?>">

                    <div class="row mb-5">
                        <div class="col-lg-4">
                            <?php form_input(label: 'No. Dokumen', name: 'document_number', value: $document['document_number'] ?? '', disabled: true, required: false) ?>
                        </div>
                        <div class="col-lg-4">
                            <?php form_input(label: 'Tanggal Dibuat', name: 'create_date', type: 'date', value: $document['create_date'] ?? date('Y-m-d'), disabled: true) ?>
                        </div>
                        <div class="col-lg-4">
                            <?php form_input(label: 'Nama Pembuat', name: 'user_create', value: $document['user_create'] ?? '', disabled: !empty($document)) ?>
                        </div>
                    </div>

                    <?php if ($type == "ca") { ?>
                        <div class="row mb-5">
                            <div class="col-lg-4">
                                <?php form_input(label: 'Tujuan', name: 'project', value: $document['project'] ?? '', disabled: $isdisabled)
                                ?>
                            </div>
                            <div class="col-lg-4">
                                <?php form_input(label: 'Tanggal Berangkat', name: 'leave_date', type: 'date', value: $document['leave_date'] ?? date('Y-m-d'), disabled: $isdisabled) ?>
                                <?php form_input(label: 'Tanggal Kembali', name: 'back_date', type: 'date', value: $document['back_date'] ?? date('Y-m-d'), disabled: $isdisabled) ?>
                            </div>
                            <div class="col-lg-4">
                                <?php
                                form_select(
                                    label: 'Metode Transfer',
                                    name: 'transfer_method',
                                    options: array(array('B', 'TRANSFER BANK'), array('T', 'TUNAI')),
                                    value: $document['transfer_method'] ?? '',
                                    disabled: $isdisabled
                                )
                                ?>
                                <?php form_input(label: 'Bank Transfer', name: 'transfer_bank', value: $document['transfer_bank'] ?? '', disabled: $isdisabled)
                                ?>
                                <?php form_input(label: 'No. Rekening', name: 'transfer_account', value: $document['transfer_account'] ?? '', disabled: $isdisabled)
                                ?>
                                <?php form_input(label: 'Jumlah Pengajuan', name: 'transfer_amount', value: $document['transfer_amount'] ?? '', func: "calculate_item(this)", disabled: $isdisabled)
                                ?>
                                <?php form_input(label: 'Tanggal Transfer', name: 'transfer_date', value: $document['transfer_date'] ?? '', required: false, disabled: true)
                                ?>
                            </div>
                        </div>
                    <?php } else if ($type == "pc") { ?>
                        <div class="row mb-5">
                            <div class="col-lg-6">
                                <?php form_input(label: 'Tujuan', name: 'project', value: $document['project'] ?? '', disabled: $isdisabled) ?>
                            </div>
                            <div class="col-lg-6">
                                <?php
                                form_select(
                                    label: 'Metode Transfer',
                                    name: 'transfer_method',
                                    options: array(array('B', 'TRANSFER BANK'), array('T', 'TUNAI')),
                                    value: $document['transfer_method'] ?? '',
                                    disabled: $isdisabled
                                )
                                ?>
                                <?php form_input(label: 'Bank Transfer', name: 'transfer_bank', value: $document['transfer_bank'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'No. Rekening', name: 'transfer_account', value: $document['transfer_account'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Nama Rekening', name: 'transfer_account_name', value: $document['transfer_account_name'] ?? '', disabled: $isdisabled) ?>
                            </div>
                        </div>
                    <?php } else if ($type == "pp") { ?>
                        <div class="row mb-5">
                            <div class="col-lg-6">
                                <?php form_input(label: 'Tujuan', name: 'project', value: $document['project'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Jenis Pembelian', name: 'buy_type', value: $document['buy_type'] ?? '', disabled: $isdisabled) ?>
                                <?php form_textarea(
                                    label: 'Catatan Pembelian',
                                    rows: 3,
                                    name: 'buy_note',
                                    value: $document['buy_note'] ?? '',
                                    required: false,
                                    disabled: $isdisabled
                                ) ?>
                            </div>
                            <div class="col-lg-6">
                                <?php
                                form_select(
                                    label: 'Metode Transfer',
                                    name: 'transfer_method',
                                    options: array(array('B', 'TRANSFER BANK'), array('T', 'TUNAI')),
                                    value: $document['transfer_method'] ?? '',
                                    disabled: $isdisabled
                                )
                                ?>
                                <?php form_input(label: 'Bank Transfer', name: 'transfer_bank', value: $document['transfer_bank'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'No. Rekening', name: 'transfer_account', value: $document['transfer_account'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Nama Rekening', name: 'transfer_account_name', value: $document['transfer_account_name'] ?? '', disabled: $isdisabled) ?>
                            </div>
                        </div>
                    <?php } else if ($type == "all") { ?>
                        <div class="row mb-5">
                            <div class="col-lg-4">
                                <?php
                                $options = [];
                                foreach ($doctypes as $row) {
                                    if ($row['type'] != 'A') continue;
                                    $options[] = array($row['id'], $row['name'] . ' [' . $row['code'] . ']');
                                }
                                form_select(
                                    label: 'Tipe Dokumen',
                                    name: 'doctype_id',
                                    options: $options,
                                    value: $document['doctype_id'] ?? '',
                                    disabled: $isdisabled
                                ) ?>
                            </div>
                            <div class="col-lg-8">
                                <?php form_input(label: 'Perihal', name: 'subject', value: $document['subject'] ?? '', disabled: $isdisabled) ?>
                            </div>
                        </div>
                        <?php form_textarea(label: 'Isi Surat', rows: 3, name: 'content', value: $document['content'] ?? '', disabled: $isdisabled) ?>
                    <?php } ?>

                    <?php
                    $isfinance = $iscanapprove && substr($approvecode, 1, 1) == 'F';
                    if ($isfinance || count($dfile ?? []) > 1) {
                    ?>
                        <button id="add-dfile" type="button" class="btn btn-primary d-none">TAMBAH BUKTI TRANSFER</button>
                        <div class="dfiles">
                            <span class="font-weight-bolder">BUKTI TRANSFER</span>
                            <?php foreach ($dfile as $row) {
                            ?>
                                <div class="dfile <?= (empty($row['file'] ?? '') && count($dfile ?? []) > 1) ? 'd-none' : '' ?>">
                                    <?php form_input(label: null, type: "hidden", name: 'file_f_[]', value: $row['file'] ?? '', required: false, disabled: !($iscanapprove && $isfinance)) ?>
                                    <?php form_input(label: null, type: 'file', name: 'file_f[]', value: '', required: false, accept: ".jpg, .jpeg, .png", disabled: !($iscanapprove && $isfinance)) ?>

                                    <table>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-success <?= !($iscanapprove && $isfinance) ? 'disabled' : '' ?>" <?= !($iscanapprove && $isfinance) ? 'disabled' : '' ?> onclick="$(this).closest('.dfile').find('.file_f').click()"><i class="la la-upload"></i> UPLOAD BUKTI TRANSFER</button>
                                            </td>
                                            <td>
                                                <?php if (isset($row['file']) && !empty($row['file'])) { ?>
                                                    <a class="btn btn-primary" href="<?= base_url() . $row['file'] ?>" target="_blank"><i class="la la-image"></i> VIEW</a>
                                                <?php } else { ?>
                                                    <a class="btn btn-secondary disabled" disabled href="#"><i class="la la-image"></i> NO FILE</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <?php form_textarea(label: 'Catatan Transfer', name: 'note_f[]', value: $row['note'] ?? '', required: false, disabled: !($iscanapprove && $isfinance)) ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($type == "ca" || $type == "pc" || $type == "pp") { ?>
                        <div class="<?= ($iscancomplete || count($item ?? []) > 1 || $type != "ca") ? '' :  'd-none' ?>">
                            <div class="font-weight-bolder mb-5">DETAIL PENGAJUAN</div>
                            <table class="table table-bordered mb-5 table-responsive">
                                <thead>
                                    <tr>
                                        <th nowrap="nowrap">DESKRIPSI<span class="text-white">----------</span></th>
                                        <th nowrap="nowrap">UNIT<span class="text-white">----------</span></th>
                                        <th nowrap="nowrap">@HARGA<span class="text-white">----------</span></th>
                                        <th nowrap="nowrap">SUBTOTAL<span class="text-white">----------</span></th>
                                        <th nowrap="nowrap">BUKTI<span class="text-white">----------</span></th>
                                        <th>
                                            <button type="button" class="btn btn-xs btn-icon btn-success <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?>" <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?> id="add-item"><i class="fas fa-plus"></i></button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="items">
                                    <?php foreach ($item as $row) {
                                    ?>
                                        <tr class="item <?= empty($row['description']) ? 'd-none' : '' ?>">
                                            <td>
                                                <?php form_textarea(label: null, name: 'description[]', value: $row['description'] ?? '', form: false, required: false, disabled: $isdisabled && !$iscancomplete) ?>
                                            </td>
                                            <td>
                                                <?php form_input(label: null, name: 'unit[]', type: 'number', value: $row['unit'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled && !$iscancomplete) ?>
                                            </td>
                                            <td>
                                                <?php form_input(label: null, name: 'price[]', value: $row['price'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled && !$iscancomplete) ?>
                                            </td>
                                            <td>
                                                <?php form_input(label: null, name: 'subtotal[]', value: $row['subtotal'] ?? '', form: false, required: false, disabled: true) ?>
                                            </td>
                                            <td nowrap="nowrap" class="td">
                                                <?php form_input(label: null, type: "hidden", name: 'file_[]', value: $row['file'] ?? '', form: false, required: false, disabled: $isdisabled && !$iscancomplete) ?>
                                                <?php form_input(label: null, type: 'file', name: 'file[]', value: '', form: false, required: false, accept: ".jpg, .jpeg, .png", disabled: $isdisabled && !$iscancomplete) ?>

                                                <table border="0">
                                                    <tr>
                                                        <td>
                                                            <button type="button" class="btn btn-primary <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?>" <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?> onclick="$(this).closest('.td').find('.file').click()"><i class="la la-paperclip"></i> UPLOAD</button>
                                                        </td>
                                                        <td>
                                                            <?php if (isset($row['file']) && !empty($row['file'])) { ?>
                                                                <a class="btn btn-primary" href="<?= base_url() . $row['file'] ?>" target="_blank"><i class="la la-image"></i> SHOW</a>
                                                            <?php } else { ?>
                                                                <a class="btn btn-secondary disabled" disabled href="#"><i class="la la-image"></i> NO FILE</a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-icon btn-xs <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?>" <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?> onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else if ($type == "all") { ?>
                    <?php } ?>

                    <?php if ($type == "ca") { ?>
                        <div class="<?= ($iscancomplete) ? '' :  'd-none' ?>">
                            <div class="row mt-5">
                                <div class="col-lg-4 mb-5">
                                    <div class="card card-custom bg-light-primary text-primary card-stretch">
                                        <div class="card-body">
                                            <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">TOTAL TRANSFER</div>
                                            <div class="font-size-h3 font-weight-bolder mr-2" id="sum-subtotal">
                                                Rp. 0
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-5">
                                    <div class="card card-custom bg-light-warning text-warning card-stretch">
                                        <div class="card-body">
                                            <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">TOTAL PEMAKAIAN</div>
                                            <div class="font-size-h3 font-weight-bolder mr-2" id="sum-used">
                                                Rp. 0
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-5">
                                    <div class="card card-custom bg-light-danger text-danger card-stretch" id="card-diff">
                                        <div class="card-body">
                                            <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">SISA TRANSFER</div>
                                            <div class="font-size-h3 font-weight-bolder mr-2" id="sum-diff">
                                                Rp. 0
                                            </div>
                                            <?php if (count($item) > 1) { ?>
                                                <button class="btn btn-white mt-5" id="btn-transfer" type="button" onclick="$('#modal-transfer').modal('show')">FORM TRANSFER</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <?php
                    table_history($history);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-5 d-none">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <?php form_input(label: 'Tanggal Release', name: 'release_date', type: 'date', value: $document['release_date'] ?? date('Y-m-d')) ?>
                </div>
                <div class="col-lg-3">
                    <?php
                    $options = [];
                    $isinoptions = false;
                    foreach ($docstatus as $row) {
                        if (($document['from_division_id'] ?? '') == $_SESSION['user']['division_id']) {
                            if (
                                substr($row['code'], 0, 1) != 'N'
                                && substr($row['code'], 0, 1) != 'P'
                                && substr($row['code'], 0, 1) != 'R'
                                && $row['approve_code'] != ''
                                && !str_contains($row['name'], '_HEAD')
                            ) {
                                continue;
                            }
                        }
                        $options[] = array($row['code'], $row['name'] . ' [' . $row['code'] . ']');
                        if ($row['code'] == $approvecode) {
                            $isinoptions = true;
                        }
                    }
                    $iscanapprove = $iscanapprove && $isinoptions;
                    $options[] = array($statuscode, '');
                    form_select(
                        label: 'Status',
                        name: 'status',
                        options: $options,
                        value: $document['status'] ?? '-',
                        func: "set_division(this)"
                    ) ?>
                </div>
                <div class="col-lg-3">
                    <?php form_select(
                        label: 'Dari Divisi',
                        name: 'from_division_id',
                        options: get_array_options($divisions, 'id', array('name', ' [', 'code', ']')),
                        value: $document['from_division_id'] ?? $_SESSION['user']['division_id'],
                        disabled: true
                    ) ?>
                </div>
                <div class="col-lg-3">
                    <?php form_select(
                        label: 'Ke Divisi',
                        name: 'to_division_id',
                        options: get_array_options($divisions, 'id', array('name', ' [', 'code', ']')),
                        value: $document['to_division_id'] ?? $_SESSION['user']['division_id'],
                        disabled: true
                    ) ?>
                </div>
            </div>
        </div>
    </div>


    <div class="card card-custom <?= $isinform ? '' : 'd-none' ?>">
        <div class="card-body">
            <div class="<?= !($isdisabled && $iscanapprove) ? 'd-none' : '' ?>">
                <?php form_textarea(
                    label: 'Catatan',
                    rows: 3,
                    name: 'note',
                    value: $document['note'] ?? '',
                    required: false,
                    disabled: $isreview
                ) ?>
            </div>
            <?php if (!$isreview) { ?>
                <div class="text-right">
                    <?php
                    if ($iscanapprove || $isnew  || $iscancomplete) {
                    ?>
                        <button type="button" class="btn btn-dark font-weight-bolder" onclick="save_as_draft()">
                            <span class="svg-icon svg-icon-md">
                                <i class="la la-save"></i>
                            </span>Draft
                        </button>
                    <?php
                    }
                    ?>
                    <?php
                    if ($isnew || $iscancomplete) {
                    ?>
                        <button class="btn btn-success" id="btn-save" type="button" onclick="save_approve()"><i class="la la-upload"></i> Kirim</button>
                    <?php } ?>
                    <?php
                    if ($iscanapprove) {
                    ?>
                        <button class="btn btn-success" type="button" onclick="save_approve()"><i class="la la-check"></i> Approve</button>
                        <button class="btn btn-warning" type="button" onclick="save_revisi()"><i class="la la-reply"></i> Revisi</button>
                        <button class="btn btn-danger" type="button" onclick="save_reject()"><i class="la la-close"></i> Reject</button>
                    <?php
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
</form>

<?php if ($type == 'ca' && count($item) > 1) {
    modal_transfer($this, $document, $transfer, $divisions);
} ?>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(document).ready(function() {
        $('#content').summernote(<?= $isdisabled ? '"disable"' : '' ?>);
    });

    $("#add-item").click(function() {
        $("#items").append("<tr class='item'>" + $(".item").last().html().replace(/disabled/g, '').replace(/readonly/g, '').replace(/form-control-solid/g, '').replace(/d-none/g, '') + "</tr>");
        $(".item").last().find(".subtotal").addClass("form-control-solid").attr("disabled", "disabled").attr("readonly", "readonly");
    });

    function remove_item(e) {
        if ($(".item").length > 1) {
            $(e).closest("tr").remove();
        }
    };

    $("#add-dfile").click(function() {
        $("#dfiles").append("<div class='dfile'>" + $(".dfile").last().html().replace(/disabled/g, '').replace(/readonly/g, '').replace(/form-control-solid/g, '').replace(/d-none/g, '') + "</div>");
    });

    function remove_dfile(e) {
        if ($(".dfile").length > 1) {
            $(e).closest("tr").remove();
        }
    };

    function save_as_draft() {
        <?php if ($isnew) { ?>
            $('#status option').each(function() {
                if ($(this).val().substring(0, 1) == "N")
                    $("#status").val($(this).val()).change();
            });
        <?php } else { ?>
            $("#status").val("<?= $statuscode ?>").change();
        <?php
        } ?>

        show_confirm_modal_form("");
    }

    function save_approve() {
        <?php
        if ($isnew) {
        ?>
            $('#status option').each(function() {
                if ($(this).val().substring(0, 1) == "P")
                    $("#status").val($(this).val()).change();
            });
            <?php
        } else {
            if ($iscancomplete && $type == "ca") {
            ?>
                var sum_diff = $("#sum-diff").html();
                sum_diff = sum_diff.replace(/[^0-9-]/g, '');
                if (sum_diff != 0) {
                    alert("Sisa transfer tidak boleh lebih dari 0, silahkan ajukan transfer ke divisi lain.");
                    return;
                }
            <?php
            } else {
            ?>
                $("#status").val("<?= $approvecode ?>").change();
        <?php
            }
        }
        ?>

        show_confirm_modal_form("Data anda akan disetujui ke tahapan " + $("#status option:selected").text() + ".");
    }

    function save_reject() {
        $("#status").val("R").change();
        show_confirm_modal_form("Data anda akan ditolak ke tahapan " + $("#status option:selected").text() + ".");
    }

    function save_revisi() {
        $("#status").val("<?= $rejectcode ?>").change();
        show_confirm_modal_form("Data anda akan ditolak ke tahapan " + $("#status option:selected").text() + ".");
    }
</script>