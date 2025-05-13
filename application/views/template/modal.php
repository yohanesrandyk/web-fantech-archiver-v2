<?php
function show_approve_modal($id)
{
?>
    <div class="modal fade" id="modal-approve" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">APPROVAL</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <?php form_textarea(label: 'Catatan', rows: 3, name: 'note', value: $document['note'] ?? '', required: false) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="btn-fake-submit-<?= $id ?>">Simpan</button>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

<?php
function show_confirm_modal($id)
{
?>
    <button type="submit" id="btn-submit-<?= $id ?>" class="d-none"></button>
    <div class="modal fade" id="modal-submit-<?= $id ?>" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KONFIRMASI</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin melanjutkan proses ini? <span id="modal-message"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="btn-fake-submit-<?= $id ?>">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#btn-fake-submit-<?= $id ?>").click(function() {
            $("#modal-submit-<?= $id ?>").modal("hide");
            setTimeout(function() {
                var errmsg = null;
                var form = $("#btn-submit-<?= $id ?>").closest("form");
                form.find("input[required]").each(function(i, e) {
                    if ($(e).val() == null || $(e).val() === "") {
                        var labelText = $(e).closest('.form-group').find('label').text().trim();
                        errmsg = "Input " + labelText + " masih kosong";
                    }
                });
                form.find("select[required]").each(function(i, e) {
                    if ($(e).val() == null || $(e).val() === "") {
                        var labelText = $(e).closest('.form-group').find('label').text().trim();
                        errmsg = "Input " + labelText + " masih kosong";
                    }
                });
                form.find("textarea[required]").each(function(i, e) {
                    if ($(e).val() == null || $(e).val() == "") {
                        var labelText = $(e).closest('.form-group').find('label').text().trim();
                        errmsg = "Input " + labelText + " masih kosong";
                    }
                });
                if (errmsg == null) {
                    document.getElementById("btn-submit-<?= $id ?>").click();
                } else {
                    alert(errmsg);
                }
            }, 500);
        });

        function show_confirm_modal_<?= $id ?>($message) {
            $("#modal-submit-<?= $id ?>").find("#modal-message").text($message);
            $("#modal-submit-<?= $id ?>").modal("show");
        }
    </script>
<?php
}
?>


<div class="modal fade show active" id="modal-errmsg" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">KESALAHAN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body" id="text-errmsg">
                <?= $_SESSION['errmsg'] ?? "" ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    function show_errmsg_modal() {
        $("#modal-errmsg").modal();
    }
    <?php
    if (isset($_SESSION['errmsg'])) {
    ?>
        $(document).ready(function() {
            show_errmsg_modal();
        });
    <?php
        $_SESSION['errmsg'] = null;
    }
    ?>
</script>