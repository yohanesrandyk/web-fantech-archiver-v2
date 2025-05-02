<?php
$isdisabled = true;

$approvecode = $document['approve_code'] ?? 'P';
$isnew = substr($approvecode, 0, 1) == 'P';
$isinform = ($type == 'all' && ($document['type'] ?? 'A') == 'A') || $type != 'all';
$iscanapprove = substr($approvecode, 0, 1) == 'A';
$iscancomplete = $approvecode == 'A';

if (
    $isnew && $isinform
) {
    $isdisabled = false;
}
?>
<script>
    function calculate_item(e) {
        $(document).ready(function() {
            var unit = $(e).closest("tr").find(".unit").val().replace(/,/g, '');
            var price = $(e).closest("tr").find(".price").val().replace(/,/g, '');
            var transfer = $("#transfer_amount").val().replace(/,/g, '');

            $(e).closest("tr").find(".subtotal").val(unit * price);

            setCurrency($(e).closest("tr").find(".unit"));
            setCurrency($(e).closest("tr").find(".price"));
            setCurrency($(e).closest("tr").find(".subtotal"));
            setCurrency($(e).closest("tr").find(".used"));


            if ($('.used').length) {
                var total = 0;
                var used = 0;
                var diff = 0;
                $('.subtotal').each(function() {
                    var total_ = parseInt($(this).val().replace(/,/g, ''));
                    if (Number.isNaN(total_)) total_ = 0;
                    total += total_;
                });
                $('.used').each(function() {
                    var used_ = parseInt($(this).val().replace(/,/g, ''));
                    if (Number.isNaN(used_)) used_ = 0;
                    used += used_;
                });
                diff = transfer - used;
                $("#sum-subtotal").text("Rp. " + formatNumber(total));
                $("#sum-used").text("Rp. " + formatNumber(used));
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
                if (diff != 0) {
                    $("#text-errmsg").text("Total harga tdak sama dengan jumlah transfer.");
                    $("#btn-save").attr("onclick", "show_errmsg_modal()");
                }
            }
        });
    }

    function set_division(e) {
        var division = <?= json_encode($docstatus) ?>;
        var status = $(e).val();
        // console.log(status);
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
                                <?php form_input(label: 'Nama Proyek', name: 'project', value: $document['project'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Nama Klien', name: 'client', value: $document['client'] ?? '', disabled: $isdisabled) ?>
                                <?php //form_textarea(label: 'Catatan', rows: 3, name: 'note', value: $document['note'] ?? '', required: false) 
                                ?>
                            </div>
                            <div class="col-lg-4">
                                <?php form_input(label: 'Tujuan', name: 'objective', value: $document['objective'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Tanggal Berangkat', name: 'leave_date', type: 'date', value: $document['leave_date'] ?? date('Y-m-d'), disabled: $isdisabled) ?>
                                <?php form_input(label: 'Tanggal Kembali', name: 'back_date', type: 'date', value: $document['back_date'] ?? date('Y-m-d'), disabled: $isdisabled) ?>
                            </div>
                            <div class="col-lg-4">
                                <?php form_input(label: 'Tanggal Transfer', name: 'transfer_date', type: 'date', value: $document['transfer_date'] ?? date('Y-m-d'), disabled: $isdisabled) ?>
                                <?php form_select(
                                    label: 'Metode Transfer',
                                    name: 'transfer_method',
                                    options: array(array('B', 'TRANSFER BANK'), array('T', 'TUNAI'), array('C', 'CEK')),
                                    value: $document['transfer_method'] ?? '',
                                    disabled: $isdisabled
                                ) ?>
                                <?php form_input(label: 'Bank Transfer', name: 'transfer_bank', value: $document['transfer_bank'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'No. Rekening', name: 'transfer_account', value: $document['transfer_account'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Jumlah Transfer', name: 'transfer_amount', value: $document['transfer_amount'] ?? '', func: "setCurrency(this)", disabled: $isdisabled) ?>
                                <?php //form_textarea(label: 'Catatan', rows: 3, name: 'transfer_note', value: $document['transfer_note'] ?? '', required: false) 
                                ?>
                            </div>
                        </div>
                    <?php } else if ($type == "pc") { ?>
                        <div class="row mb-5">
                            <div class="col-lg-6">
                                <?php form_input(label: 'Nama Proyek', name: 'project', value: $document['project'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Nama Customer', name: 'customer', value: $document['customer'] ?? '', disabled: $isdisabled) ?>
                            </div>
                            <div class="col-lg-6">
                                <?php form_input(label: 'Bank Transfer', name: 'bank', value: $document['bank'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'No. Rekening', name: 'account', value: $document['account'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Nama Rekening', name: 'account_name', value: $document['account_name'] ?? '', disabled: $isdisabled) ?>
                                <?php form_textarea(label: 'Catatan', rows: 3, name: 'note', value: $document['note'] ?? '', required: false, disabled: $isdisabled) ?>
                            </div>
                        </div>
                    <?php } else if ($type == "pp") { ?>
                        <div class="row mb-5">
                            <div class="col-lg-6">
                                <?php form_input(label: 'Nama Proyek', name: 'project', value: $document['project'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Nama Customer', name: 'customer', value: $document['customer'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Jenis Pembelian', name: 'buy_type', value: $document['buy_type'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Tanggal Pembelian', name: 'buy_date', type: 'date', value: $document['buy_date'] ?? date('Y-m-d'), disabled: $isdisabled) ?>
                                <?php form_input(label: 'Batas Pembayaran', name: 'payment_max_date', type: 'date', value: $document['payment_max_date'] ?? date('Y-m-d'), required: false, disabled: $isdisabled) ?>
                            </div>
                            <div class="col-lg-6">
                                <?php form_input(label: 'Nama Vendor', name: 'vendor', value: $document['vendor'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Bank Vendor', name: 'vendor_bank', value: $document['vendor_bank'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'No. Rekening Vendor', name: 'vendor_account', value: $document['vendor_account'] ?? '', disabled: $isdisabled) ?>
                                <?php form_input(label: 'Nama Rekening Vendor', name: 'vendor_account_name', value: $document['vendor_account_name'] ?? '', disabled: $isdisabled) ?>
                            </div>
                        </div>
                    <?php } else if ($type == "all") { ?>
                        <div class="row mb-5">
                            <div class="col-lg-4">
                                <?php form_select(
                                    label: 'Tipe Dokumen',
                                    name: 'doctype_id',
                                    options: get_array_options($doctypes, 'id', array('name', ' [', 'code', ']')),
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

                    <?php if (false) { ?>
                        <table class="table table-bordered mb-5">
                            <thead>
                                <tr>
                                    <th>DESKRIPSI</th>
                                    <th>FILE</th>
                                    <th>
                                        <button type="button" class="btn btn-xs btn-icon btn-success <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> id="add-dfile"><i class="fas fa-plus"></i></button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="dfiles">
                                <?php foreach ($dfile as $row) { ?>
                                    <tr class="dfile">
                                        <td>
                                            <?php form_input(label: null, name: 'note_f[]', value: $row['note'] ?? '', form: false, required: false, disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php if (isset($row['file'])) { ?>
                                                <a href="<?= base_url() . $row['file'] ?>" target="_blank"><?= $row['file'] ?></a>
                                            <?php } ?>

                                            <?php form_input(label: null, type: "hidden", name: 'file_f_[]', value: $row['file'] ?? '', form: false, required: false, disabled: $isdisabled) ?>
                                            <?php form_input(label: null, type: 'file', name: 'file_f[]', value: '', form: false, required: false, accept: ".pdf, .jpg, .jpeg, .png", disabled: $isdisabled) ?>

                                            <button type="button" class="btn btn-block btn-primary btn-sm mb-1 <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> onclick="$(this).closest('td').find('.file_f').click()"><i class="la la-paperclip"></i> ATTACH</button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-icon btn-xs <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>

                    <?php if ($type == "ca") { ?>
                        <table class="table table-bordered mb-5 table-responsive">
                            <thead>
                                <tr>
                                    <th nowrap="nowrap">DESKRIPSI<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">UNIT<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">@HARGA<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">SUBTOTAL<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">PEMAKAIAN<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">BUKTI<span class="text-white">----------</span></th>
                                    <th>
                                        <button type="button" class="btn btn-xs btn-icon btn-success <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?>" <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?> id="add-item"><i class="fas fa-plus"></i></button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="items">
                                <?php foreach ($item as $row) { ?>
                                    <tr class="item">
                                        <td>
                                            <?php form_textarea(label: null, name: 'description[]', value: $row['description'] ?? '', form: false, required: false, disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'unit[]', type: 'number', value: $row['unit'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'price[]', value: $row['price'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'subtotal[]', value: $row['subtotal'] ?? '', form: false, required: false, disabled: true) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'used[]', value: $row['used'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled) ?>
                                        </td>
                                        <td nowrap="nowrap">
                                            <?php if (isset($row['file'])) { ?>
                                                <a class="btn btn-block btn-primary btn-sm mb-1" href="<?= base_url() . $row['file'] ?>" target="_blank"><i class="la la-image"></i> VIEW</a>
                                            <?php } ?>

                                            <?php form_input(label: null, type: "hidden", name: 'file_[]', value: $row['file'] ?? '', form: false, required: false, disabled: $isdisabled) ?>
                                            <?php form_input(label: null, type: 'file', name: 'file[]', value: '', form: false, required: false, accept: ".pdf, .jpg, .jpeg, .png", disabled: $isdisabled) ?>

                                            <button type="button" class="btn btn-block btn-primary btn-sm mb-1 <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> onclick="$(this).closest('td').find('.file').click()"><i class="la la-paperclip"></i> ATTACH</button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-icon btn-xs <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else if ($type == "pc") { ?>
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
                                <?php foreach ($item as $row) { ?>
                                    <tr class="item">
                                        <td>
                                            <?php form_input(label: null, name: 'description[]', value: $row['description'] ?? '', form: false, required: false, disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'unit[]', type: 'number', value: $row['unit'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'price[]', value: $row['price'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'subtotal[]', value: $row['subtotal'] ?? '', form: false, required: false, disabled: true) ?>
                                        </td>
                                        <td nowrap="nowrap">
                                            <?php if (isset($row['file'])) { ?>
                                                <a class="btn btn-icon btn-primary btn-xs mb-1" href="<?= base_url() . $row['file'] ?>" target="_blank"><i class="la la-image"></i></a>
                                            <?php } ?>

                                            <?php form_input(label: null, type: "hidden", name: 'file_[]', value: $row['file'] ?? '', form: false, required: false, disabled: $isdisabled) ?>
                                            <?php form_input(label: null, type: 'file', name: 'file[]', value: $row['file'] ?? '', form: false, required: false, accept: ".pdf, .jpg, .jpeg, .png", disabled: $isdisabled) ?>

                                            <button type="button" class="btn btn-icon btn-primary btn-xs mb-1 <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> onclick="$(this).closest('td').find('.file').click()"><i class="la la-paperclip"></i></button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-icon btn-xs <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else if ($type == "pp") { ?>
                        <table class="table table-bordered mb-5 table-responsive">
                            <thead>
                                <tr>
                                    <th nowrap="nowrap">NAMA BARANG<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">UNIT<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">@HARGA<span class="text-white">----------</span></th>
                                    <th nowrap="nowrap">SUBTOTAL<span class="text-white">----------</span></th>
                                    <th>
                                        <button type="button" class="btn btn-xs btn-icon btn-success <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?>" <?= $isdisabled && !$iscancomplete ? 'disabled' : '' ?> id="add-item"><i class="fas fa-plus"></i></button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="items">
                                <?php foreach ($item as $row) { ?>
                                    <tr class="item">
                                        <td>
                                            <?php form_input(label: null, name: 'item[]', value: $row['item'] ?? '', form: false, required: false, disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'unit[]', type: 'number', value: $row['unit'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'price[]', value: $row['price'] ?? '', form: false, required: false, func: "calculate_item(this)", disabled: $isdisabled) ?>
                                        </td>
                                        <td>
                                            <?php form_input(label: null, name: 'subtotal[]', value: $row['subtotal'] ?? '', form: false, required: false, disabled: true) ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-icon btn-xs <?= $isdisabled == true ? 'disabled' : '' ?>" <?= $isdisabled == true ? 'disabled' : '' ?> onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else if ($type == "all") { ?>
                    <?php } ?>

                    <div class="row" id="card-item">
                        <div class="col-lg-4 mb-5">
                            <div class="card card-custom bg-light-primary text-primary">
                                <div class="card-body">
                                    <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">TOTAL HARGA</div>
                                    <div class="font-size-h3 font-weight-bolder mr-2" id="sum-subtotal">
                                        Rp. 0
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-5">
                            <div class="card card-custom bg-light-warning text-warning">
                                <div class="card-body">
                                    <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">TOTAL PEMAKAIAN</div>
                                    <div class="font-size-h3 font-weight-bolder mr-2" id="sum-used">
                                        Rp. 0
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-5">
                            <div class="card card-custom bg-light-danger text-danger" id="card-diff">
                                <div class="card-body">
                                    <div class="card-title font-weight-bolder font-size-h6 mb-4 d-block">SISA TRANSFER</div>
                                    <div class="font-size-h3 font-weight-bolder mr-2" id="sum-diff">
                                        Rp. 0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <?php
                    table_history($history);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom mb-5 d-non">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <?php form_input(label: 'Tanggal Release', name: 'release_date', type: 'date', value: $document['release_date'] ?? date('Y-m-d')) ?>
                </div>
                <div class="col-lg-3">
                    <?php
                    $options = [];
                    foreach ($docstatus as $row) {
                        if ($row['approve_code'] == 'A' && $document['user_create_id'] != $_SESSION['user']['id']) {
                            continue;
                        }
                        $options[] = array($row['code'], $row['name'] . ' [' . $row['code'] . ']');
                    }
                    form_select(
                        label: 'Status',
                        name: 'status',
                        options: $options,
                        value: $document['status'] ?? 'NW',
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


    <div class="card card-custom">
        <div class="card-body">
            <?php form_textarea(
                label: 'Catatan',
                rows: 3,
                name: 'note',
                value: $document['note'] ?? '',
                required: false,
                disabled: $isdisabled && !$iscanapprove
            ) ?>
            <div class="text-right">
                <?php
                if ($isnew) {
                ?>
                    <button type="button" class="btn btn-dark font-weight-bolder" onclick="save_as_draft()">
                        <span class="svg-icon svg-icon-md">
                            <i class="la la-save"></i>
                        </span>Draft
                    </button>
                    <button class="btn btn-success" id="btn-save" type="button" onclick="save_approve()"><i class="la la-upload"></i> Kirim</button>
                <?php } ?>
                <?php
                if ($iscanapprove) {
                ?>
                    <button class="btn btn-success" type="button" onclick="save_approve()"><i class="la la-check"></i> Approve</button>
                    <button class="btn btn-danger" type="button" onclick="save_reject()"><i class="la la-reply"></i> Reject</button>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

</form>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(document).ready(function() {
        if (!$('.used').length) {
            $("#card-item").remove();
        }

        $('#content').summernote(<?= $isdisabled ? '"disable"' : '' ?>);
    });

    $("#add-item").click(function() {
        $("#items").append("<tr class='item'>" + $(".item").last().html().replace(/disabled/g, '').replace(/readonly/g, '') + "</tr>");
    });

    function remove_item(e) {
        if ($(".item").length > 1) {
            $(e).closest("tr").remove();
        }
    };

    $("#add-dfile").click(function() {
        $("#dfiles").append("<tr class='dfile'>" + $(".dfile").last().html().replace(/disabled/g, '').replace(/readonly/g, '') + "</tr>");
    });

    function remove_dfile(e) {
        if ($(".dfile").length > 1) {
            $(e).closest("tr").remove();
        }
    };

    function save_as_draft() {
        $('#status option').each(function() {
            if ($(this).val().substring(0, 1) == "N")
                $("#status").val($(this).val()).change();
        });
        if ($("#status").val() != null) {
            show_confirm_modal_form("Data anda akan masuk ke tahapan " + $("#status option:selected").text() + ".");
        }
    }

    function save_approve() {
        $("#status").val("<?= $approvecode ?>").change();
        if ($("#status").val() != null) {
            show_confirm_modal_form("Data anda akan disetujui ke tahapan " + $("#status option:selected").text() + ".");
        }
    }

    function save_reject() {
        $("#status").val("<?= $approvecode ?>").change();
        show_confirm_modal_form("Data anda akan ditolak ke tahapan " + $("#status option:selected").text() + ".");
    }
</script>