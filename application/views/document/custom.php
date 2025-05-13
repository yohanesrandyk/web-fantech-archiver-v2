<?php
function table_history($data)
{
?>
    <table class="table table-bordered table-responsive" id="table-history">
        <thead>
            <tr>
                <th>#</th>
                <th>USER UPDATE</th>
                <th>UPDATE STATUS</th>
                <th>CATATAN</th>
                <th>TANGGAL UPDATE</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($data as $row) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['user_update'] ?></td>
                    <td><span class="label label-<?= substr($row['status_update'], 0, 1) == 'A' ? 'success' : (substr($row['status_update'], 0, 1) == 'P' ? 'primary' : 'danger') ?> label-inline font-weight-lighter mr-2"><?= $row['name'] ?></span></td>
                    <td><?= $row['note'] ?></td>
                    <td><?= $row['update_date'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <script>
        $(document).ready(function() {
            $("#table-history").DataTable();
        });
    </script>
<?php
}
?>

<?php
function modal_transfer($php, $document, $transfer, $divisions)
{
    $isdisabled = false;
    // if (isset($transfer['transfer_amount'])) {
    //     $isdisabled = true;
    // }
?>
    <form action="<?= base_url() . "document/store_transfer" ?>" method="POST" enctype="multipart/form-data">
        <div class="modal fade" id="modal-transfer" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">FORM TRANSFER</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="hidden" name="<?= $php->security->get_csrf_token_name() ?>" value="<?= $php->security->get_csrf_hash() ?>">
                                <input type="hidden" name="mt_id" value="<?= $transfer['id'] ?? '' ?>">
                                <input type="hidden" name="mt_document_id" value="<?= $document['document_id'] ?>">
                                <?php
                                form_select(
                                    label: 'Metode Transfer',
                                    name: 'mt_transfer_method',
                                    options: array(array('B', 'TRANSFER BANK'), array('T', 'TUNAI')),
                                    value: $transfer['transfer_method'] ?? '',
                                    disabled: $isdisabled
                                )
                                ?>
                                <?php form_input(label: 'Bank Transfer', name: 'mt_transfer_bank', value: $transfer['transfer_bank'] ?? '', disabled: $isdisabled)
                                ?>
                            </div>
                            <div class="col-lg-6">
                                <?php form_input(label: 'No. Rekening', name: 'mt_transfer_account', value: $transfer['transfer_account'] ?? '', disabled: $isdisabled)
                                ?>
                                <?php form_input(label: 'Nama Pemilik Rekening', name: 'mt_transfer_account_name', value: $transfer['transfer_account_name'] ?? '', disabled: $isdisabled)
                                ?>
                                <?php form_input(label: 'Jumlah Transfer', name: 'mt_transfer_amount', value: $transfer['transfer_amount'] ?? '', func: "setCurrency(this)", disabled: $isdisabled)
                                ?>
                                <?php form_input(label: 'Tanggal Transfer', name: 'mt_transfer_date', value: $transfer['transfer_date'] ?? '', func: "", disabled: true)
                                ?>
                            </div>
                        </div>
                        <?php form_textarea(label: 'Catatan', rows: 3, name: 'mt_note', value: $transfer['note'] ?? '', required: false, disabled: $isdisabled) ?>
                        <?php form_select(
                            label: 'Penagihan Ke Divisi',
                            name: 'mt_to_division_id',
                            options: get_array_options($divisions, 'id', array('name', ' [', 'code', ']')),
                            value: $transfer['to_division_id'] ?? '',
                            disabled: true
                        ) ?>
                        <?php form_input(label: null, type: "hidden", name: 'mt_file_', value: $transfer['file'] ?? '', required: false) ?>
                        <?php form_input(label: null, type: 'file', name: 'mt_file', value: '', required: false, accept: ".pdf, .jpg, .jpeg, .png") ?>
                        <table>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success <?= $isdisabled ? 'disabled' : '' ?>" onclick="$(this).closest('form').find('.mt_file').click()" <?= $isdisabled ? 'disabled' : '' ?>><i class="la la-upload"></i> UPLOAD BUKTI TRANSFER</button>
                                </td>
                                <td>
                                    <?php if (isset($transfer['file'])) { ?>
                                        <a class="btn btn-primary" href="<?= base_url() . $transfer['file'] ?>" target="_blank"><i class="la la-image"></i> VIEW</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger <?= $isdisabled ? 'disabled' : '' ?>" <?= $isdisabled ? 'disabled' : '' ?>>Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php } ?>