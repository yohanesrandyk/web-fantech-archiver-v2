<?php
$isdisabled = false;
?>
<script>
    function calculate_item(e) {
        $(document).ready(function() {
            var unit = $(e).closest("tr").find(".unit").val().replace(/,/g, '');
            var price = $(e).closest("tr").find(".price").val().replace(/,/g, '');
            $(e).closest("tr").find(".subtotal").val(unit * price);

            setCurrency($(e).closest("tr").find(".unit"));
            setCurrency($(e).closest("tr").find(".price"));
            setCurrency($(e).closest("tr").find(".subtotal"));
            setCurrency($(e).closest("tr").find(".used"));
        });
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
                <button type="button" class="btn btn-dark font-weight-bolder" onclick="show_confirm_modal_form()">
                    <span class="svg-icon svg-icon-md">
                        <i class="la la-save"></i>
                    </span>Simpan
                </button>
                <?php if (isset($data['id'])) { ?>
                    <a class="btn btn-dark font-weight-bolder ml-2" target="_blank" href="<?= base_url() . "document/cetak/$type/" . $data['id'] ?>">
                        <i class="la la-print"></i>
                        Cetak
                    </a>
                <?php } ?>
                <a class="btn btn-white font-weight-bolder text-danger ml-2" href="<?= $_SERVER['HTTP_REFERER'] ?? base_url() ?>">
                    <i class="la la-arrow-left text-danger"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <?php show_confirm_modal('form') ?>

    <div class="card card-custom">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <span class="font-weight-bolder">Form Dokumen</span>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">

            <div class="row mb-5">
                <div class="col-lg-4">
                    <?php form_input(label: 'No. Dokumen', name: 'document_number', value: $data['document_number'] ?? '', disabled: true, required: false) ?>
                </div>
                <div class="col-lg-4">
                    <?php form_input(label: 'Tanggal Dibuat', name: 'create_date', type: 'date', value: $data['create_date'] ?? date('Y-m-d'), disabled: !empty($data)) ?>
                </div>
                <div class="col-lg-4">
                    <?php form_input(label: 'Nama Pembuat', name: 'user_create', value: $data['user_create'] ?? '', disabled: !empty($data)) ?>
                </div>
            </div>

            <?php if ($type == "ca") { ?>
                <div class="row mb-5">
                    <div class="col-lg-4">
                        <?php form_input(label: 'Nama Proyek', name: 'project', value: $data['project'] ?? '') ?>
                        <?php form_input(label: 'Nama Klien', name: 'client', value: $data['client'] ?? '') ?>
                        <?php form_textarea(label: 'Catatan', rows: 3, name: 'note', value: $data['note'] ?? '', required: false) ?>
                    </div>
                    <div class="col-lg-4">
                        <?php form_input(label: 'Tujuan', name: 'objective', value: $data['objective'] ?? '') ?>
                        <?php form_input(label: 'Tanggal Berangkat', name: 'leave_date', type: 'date', value: $data['leave_date'] ?? date('Y-m-d')) ?>
                        <?php form_input(label: 'Tanggal Kembali', name: 'back_date', type: 'date', value: $data['back_date'] ?? date('Y-m-d')) ?>
                    </div>
                    <div class="col-lg-4">
                        <?php form_input(label: 'Tanggal Transfer', name: 'transfer_date', type: 'date', value: $data['transfer_date'] ?? date('Y-m-d')) ?>
                        <?php form_select(
                            label: 'Metode Transfer',
                            name: 'transfer_method',
                            options: array(array('B', 'Transfer Bank'), array('T', 'Tunai'), array('C', 'Cek')),
                            value: $data['transfer_method'] ?? ''
                        ) ?>
                        <?php form_input(label: 'Bank Transfer', name: 'transfer_bank', value: $data['transfer_bank'] ?? '') ?>
                        <?php form_input(label: 'No. Rekening', type: "number", name: 'transfer_account', value: $data['transfer_account'] ?? '') ?>
                        <?php form_input(label: 'Jumlah Transfer', name: 'transfer_amount', value: $data['transfer_amount'] ?? '', func: "setCurrency(this)") ?>
                        <?php form_textarea(label: 'Catatan', rows: 3, name: 'transfer_note', value: $data['transfer_note'] ?? '', required: false) ?>
                    </div>
                </div>

                <table class="table table-bordered mb-5 table-responsive">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>DESKRIPSI</th>
                            <th>UNIT</th>
                            <th>@ HARGA</th>
                            <th>SUBTOTAL</th>
                            <th>PEMAKAIAN</th>
                            <th>BUKTI</th>
                            <th>
                                <button type="button" class="btn btn-xs btn-icon btn-success" id="add-item"><i class="fas fa-plus"></i></button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="items">
                        <?php foreach ($item as $row) { ?>
                            <tr class="item">
                                <td>

                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'description[]', value: $row['description'] ?? '', form: false, required: false) ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'unit[]', value: $row['unit'] ?? '', form: false, required: false, func: "calculate_item(this)") ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'price[]', value: $row['price'] ?? '', form: false, required: false, func: "calculate_item(this)") ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'subtotal[]', value: $row['subtotal'] ?? '', form: false, required: false, disabled: true) ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'used[]', value: $row['used'] ?? '', form: false, required: false, func: "calculate_item(this)") ?>
                                </td>
                                <td nowrap="nowrap">
                                    <?php if (isset($row['file'])) { ?>
                                        <a class="btn btn-icon btn-primary btn-xs mb-1" href="<?= base_url() . $row['file'] ?>" target="_blank"><i class="fas fa-eye"></i></a>
                                    <?php } ?>

                                    <?php form_input(label: null, type: "hidden", name: 'file_[]', value: $row['file'] ?? '', form: false, required: false) ?>
                                    <?php form_input(label: null, type: 'file', name: 'file[]', value: $row['file'] ?? '', form: false, required: false, accept: ".pdf, .jpg, .jpeg, .png") ?>

                                    <button type="button" class="btn btn-icon btn-primary btn-xs mb-1" onclick="$(this).closest('td').find('.file').click()"><i class="fas fa-paperclip"></i></button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-icon btn-xs" onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else if ($type == "pc") { ?>
                <div class="row mb-5">
                    <div class="col-lg-6">
                        <?php form_input(label: 'Nama Proyek', name: 'project', value: $data['project'] ?? '') ?>
                        <?php form_input(label: 'Nama Customer', name: 'customer', value: $data['customer'] ?? '') ?>
                    </div>
                    <div class="col-lg-6">
                        <?php form_input(label: 'Bank Transfer', name: 'bank', value: $data['bank'] ?? '') ?>
                        <?php form_input(label: 'No. Rekening', name: 'account', type: 'number', value: $data['account'] ?? '') ?>
                        <?php form_input(label: 'Nama Rekening', name: 'account_name', value: $data['account_name'] ?? '') ?>
                        <?php form_textarea(label: 'Catatan', rows: 3, name: 'note', value: $data['note'] ?? '', required: false) ?>
                    </div>
                </div>

                <table class="table table-bordered mb-5 table-responsive">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>DESKRIPSI</th>
                            <th>UNIT</th>
                            <th>@ HARGA</th>
                            <th>SUBTOTAL</th>
                            <th>BUKTI</th>
                            <th>
                                <button type="button" class="btn btn-xs btn-icon btn-success" id="add-item"><i class="fas fa-plus"></i></button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="items">
                        <?php foreach ($item as $row) { ?>
                            <tr class="item">
                                <td>

                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'description[]', value: $row['description'] ?? '', form: false, required: false) ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'unit[]', value: $row['unit'] ?? '', form: false, required: false, func: "calculate_item(this)") ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'price[]', value: $row['price'] ?? '', form: false, required: false, func: "calculate_item(this)") ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'subtotal[]', value: $row['subtotal'] ?? '', form: false, required: false, disabled: true) ?>
                                </td>
                                <td nowrap="nowrap">
                                    <?php if (isset($row['file'])) { ?>
                                        <a class="btn btn-icon btn-primary btn-xs mb-1" href="<?= base_url() . $row['file'] ?>" target="_blank"><i class="fas fa-eye"></i></a>
                                    <?php } ?>

                                    <?php form_input(label: null, type: "hidden", name: 'file_[]', value: $row['file'] ?? '', form: false, required: false) ?>
                                    <?php form_input(label: null, type: 'file', name: 'file[]', value: $row['file'] ?? '', form: false, required: false, accept: ".pdf, .jpg, .jpeg, .png") ?>

                                    <button type="button" class="btn btn-icon btn-primary btn-xs mb-1" onclick="$(this).closest('td').find('.file').click()"><i class="fas fa-paperclip"></i></button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-icon btn-xs" onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else if ($type == "pp") { ?>
                <div class="row mb-5">
                    <div class="col-lg-6">
                        <?php form_input(label: 'Nama Proyek', name: 'project', value: $data['project'] ?? '') ?>
                        <?php form_input(label: 'Nama Customer', name: 'customer', value: $data['customer'] ?? '') ?>
                        <?php form_input(label: 'Tanggal Pembelian', name: 'buy_date', type: 'date', value: $data['buy_date'] ?? date('Y-m-d')) ?>
                        <?php form_input(label: 'Batas Pembayaran', name: 'payment_max_date', type: 'date', value: $data['payment_max_date'] ?? date('Y-m-d')) ?>
                    </div>
                    <div class="col-lg-6">
                        <?php form_input(label: 'Nama Vendor', name: 'vendor', value: $data['vendor'] ?? '') ?>
                        <?php form_input(label: 'Jenis Pembelian', name: 'buy_type', value: $data['buy_type'] ?? '') ?>
                        <?php form_input(label: 'Bank Vendor', name: 'vendor_bank', value: $data['vendor_bank'] ?? '') ?>
                        <?php form_input(label: 'No. Rekening Vendor', name: 'vendor_account', type: 'number', value: $data['vendor_account'] ?? '') ?>
                        <?php form_input(label: 'Nama Rekening Vendor', name: 'vendor_account_name', value: $data['vendor_account_name'] ?? '') ?>
                    </div>
                </div>

                <table class="table table-bordered mb-5 table-responsive">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA BARANG</th>
                            <th>UNIT</th>
                            <th>@ HARGA</th>
                            <th>SUBTOTAL</th>
                            <th>
                                <button type="button" class="btn btn-xs btn-icon btn-success" id="add-item"><i class="fas fa-plus"></i></button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="items">
                        <?php foreach ($item as $row) { ?>
                            <tr class="item">
                                <td>

                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'item[]', value: $row['item'] ?? '', form: false, required: false) ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'unit[]', value: $row['unit'] ?? '', form: false, required: false, func: "calculate_item(this)") ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'price[]', value: $row['price'] ?? '', form: false, required: false, func: "calculate_item(this)") ?>
                                </td>
                                <td>
                                    <?php form_input(label: null, name: 'subtotal[]', value: $row['subtotal'] ?? '', form: false, required: false, disabled: true) ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-icon btn-xs" onclick="remove_item(this)"><i class="fas fa-minus"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else if ($type == "all") { ?>
                <div class="row mb-5">
                    <div class="col-lg-4">
                        <?php form_select(
                            label: 'Tipe Dokumen',
                            name: 'document_type_id',
                            options: get_array_options($this->mod_document_type->get_document_type(), 'id', array('name', ' [', 'code', ']')),
                            value: $data['document_type_id'] ?? ''
                        ) ?>
                    </div>
                    <div class="col-lg-8">
                        <?php form_input(label: 'Perihal', name: 'subject', value: $data['subject'] ?? '') ?>
                    </div>
                </div>
                <?php form_textarea(label: 'Isi Surat', rows: 3, name: 'content', value: $data['content'] ?? '') ?>
            <?php } ?>

            <div class="row mb-5">
                <div class="col-lg-3">
                    <?php form_input(label: 'Tanggal Release', name: 'release_date', type: 'date', value: $data['release_date'] ?? date('Y-m-d')) ?>
                </div>
                <div class="col-lg-3">
                    <?php form_select(
                        label: 'Status',
                        name: 'status',
                        options: array(array('P', 'Pending'), array('A', 'Approve'), array('R', 'Reject')),
                        value: $data['status'] ?? 'P'
                    ) ?>
                </div>
                <div class="col-lg-3">
                    <?php form_select(
                        label: 'Dari Divisi',
                        name: 'from_division_id',
                        options: get_array_options($this->mod_division->get_division(), 'id', array('name', ' [', 'code', ']')),
                        value: $data['from_division_id'] ?? ''
                    ) ?>
                </div>
                <div class="col-lg-3">
                    <?php form_select(
                        label: 'Ke Divisi',
                        name: 'to_division_id',
                        options: get_array_options($this->mod_division->get_division(), 'id', array('name', ' [', 'code', ']')),
                        value: $data['to_division_id'] ?? ''
                    ) ?>
                </div>
            </div>

            <?php if ($type != "all") {
                table_history($history);
            } ?>
        </div>
    </div>
</form>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(document).ready(function() {
        // $('#content').summernote();
    });

    $("#add-item").click(function() {
        $("#items").append("<tr class='item'>" + $(".item").html() + "</tr>");
    });

    function remove_item(e) {
        if ($(".item").length > 1) {
            $(e).closest("tr").remove();
        }
    };
</script>