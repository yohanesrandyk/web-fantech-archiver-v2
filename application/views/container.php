<style>
    .mini-image-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-items: center;
    }

    .mini-image-image-preview {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
    }

    .mini-image-file-input {
        display: none;
    }

    .mini-image-file-label {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        border-color: grey;
        background-color: transparent;
        border: 2px solid rgba(128, 128, 128, 0.5);
        opacity: 0.5;
        border-radius: 10%;
        width: 100px;
        height: 100px;
        margin: 5px;
        order: 1;
    }

    .mini-image-file-label:hover {
        color: white;
        background-color: rgba(128, 128, 128, 0.5);
    }

    .mini-image-file-label::before {
        content: "+";
        font-weight: bold;
        color: rgba(128, 128, 128, 0.5);
    }

    .mini-image-file-label:hover::before {
        color: white;
    }

    .mini-image-remove-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: transparent;
        color: red;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
    }

    .mini-image-remove-icon::before {
        content: "\f057";
        /* ini adalah kode unicode untuk icon fa-times */
        color: red;
        font-family: "Font Awesome 5 Free";
        /* ini adalah nama font untuk icon Font Awesome */
        font-weight: 900;
        /* ini adalah bobot font untuk icon Font Awesome */
        font-size: 16px;
        /* ini adalah ukuran font untuk icon */
        line-height: 20px;
        /* ini adalah tinggi baris untuk icon */
    }

    .mini-image-image-container {
        position: relative;
        margin: 5px;
        border-radius: 10px;
        order: 0;
    }

    .mini-image-image-container:hover {
        opacity: 0.8;
        transition: opacity 0.3s;
        display: block;
    }

    .mini-image-remove-icon:hover {
        color: red;
        border-color: red;
    }

    .mini-image-image-container img {
        object-fit: cover;
        border-radius: 10px;
        width: 100px;
        height: 100px;
    }

    .typed-cursor {
        font-size: 20px;
        opacity: 1;
        -webkit-animation: blink 0.7s infinite;
        -moz-animation: blink 0.7s infinite;
        animation: blink 0.7s infinite;
    }

    #loading_text {
        font-size: 20px
    }

    #loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
    }
</style>
<script>
    function initSelectPicker(e) {
        var target = $(e).closest("div").find("select");
        target.selectpicker("refresh");
        target = $(e).closest("div").find("div").find("button");
        $(target[1]).remove();
    }

    function setCurrency(e) {
        $(document).ready(function() {
            var nilai = $(e).val();
            if (nilai.indexOf(".") > 0) nilai = nilai.substr(0, nilai.indexOf("."));
            nilai = nilai.replace(/,/g, '');
            nilai = formatNumber(nilai);
            if (nilai.indexOf(".") > 0) nilai = nilai.substr(0, nilai.indexOf("."));
            if (nilai == "NaN") nilai = 0;
            $(e).val(nilai);
        });
    }
</script>

<?php $this->view($content) ?>

<?php
function get_array_options($array, $id, $label_array)
{
    $options = array();
    foreach ($array as $row) {
        $label = '';
        foreach ($label_array as $row_x) {
            $label .= $row[$row_x] ?? $row_x;
        }
        $options[] = array($row[$id], $label);
    }
    return $options;
}
?>

<?php
function form_input($label, $name, $value, $type = 'text', $disabled = null, $maxlength = null, $func = null, $required = true, $form = true, $accept = "")
{
    $value = (isset($_SESSION['old'][$name]) ? (!is_array($_SESSION['old'][$name]) ? $_SESSION['old'][$name] : null) : null) ?? $value ?? '';
?>
    <div class="<?= $form ? 'form-group' : '' ?>">
        <?php if (isset($label)) { ?>
            <label class="font-weight-bold"><?= $label .= $required ? ' <span class="text-danger">*</span>' : ''; ?></label>
        <?php } ?>
        <input
            class="form-control <?= $disabled ? 'form-control-solid' : '' ?> <?= preg_replace("/[^a-zA-Z0-9]/", '', $name) ?> 
            <?= $type == 'file' ? 'd-none' : '' ?>"
            <?= $maxlength ? 'maxlength="' . $maxlength . '"' : '' ?>
            type="<?= $type ?>"
            name="<?= $name ?>"
            id="<?= $name ?>"
            value="<?= $value ?>"
            <?= $disabled ? 'readonly' : '' ?>
            onkeyup="<?= $func ?>"
            onchange="<?= $func ?>"
            accept="<?= $accept ?>"
            <?= $required ? 'required' : '' ?> />
        <img src onerror="<?= str_contains($func ?? "", "setCurrency") ? "setCurrency($(this).closest('div').find('input'))"  : $func ?>" class="d-none">
    </div>
<?php
}
?>

<?php
function form_select($label, $options, $name, $value = null, $func = null, $disabled = null, $required = true, $multiple = false)
{
    $value = (isset($_SESSION['old'][$name]) ? (!is_array($_SESSION['old'][$name]) ? $_SESSION['old'][$name] : null) : null) ?? $value ?? '';
?>
    <div class=" form-group">
        <?php if (isset($label)) { ?>
            <label class="font-weight-bold"><?= $label . ($required ? ' <span class="text-danger">*</span>' : '') ?></label>
        <?php } ?>
        <select
            class="form-control <?= $disabled ? 'form-control-solid' : '' ?>"
            <?= $multiple ? 'multiple="multiple"' : '' ?>
            data-size="5"
            data-live-search="true"
            name="<?= $name ?>"
            id="<?= $name ?>"
            onchange="<?= $func ?>"
            <?= $disabled ? 'disabled' : '' ?>
            <?= $required ? 'required' : '' ?>>
            <option value="">Pilih <?= $label ?></option>
            <?php
            if ($options == null) $options = array();
            foreach ($options as $option) {
                $selected = ($option[0] === $value) ? 'selected' : '';
                echo "<option value='$option[0]' $selected>$option[1]</option>";
            } ?>
        </select>
        <?php
        if ($disabled) {
        ?>
            <input type="hidden" name="<?= $name ?>" value="<?= $value ?>">
        <?php
        }
        ?>
        <?php
        if ($multiple) {
            $uuid = uniqid();
        ?>
            <script>
                function initMultiSelectPicker<?= $uuid ?>(e) {
                    $(e).closest("div").find("select").selectpicker('val', "<?= $value ?? '' ?>".split(","));
                    $(e).closest("div").find("select").selectpicker('refresh');
                };
            </script>
            <img src onerror="initMultiSelectPicker<?= $uuid ?>(this)" class="d-none">
        <?php } else {
        ?>
            <img src onerror="initSelectPicker(this)" class="d-none">
        <?php
        } ?>
    </div>
<?php
}
?>

<?php
function form_textarea($label, $name, $value, $rows = 4, $form = true, $disabled = null, $maxlength = null, $required = true)
{
    $value = (isset($_SESSION['old'][$name]) ? (!is_array($_SESSION['old'][$name]) ? $_SESSION['old'][$name] : null) : null) ?? $value ?? '';
?>
    <div class="<?= $form ? 'form-group' : '' ?>">
        <?php if (isset($label)) { ?>
            <labe class="font-weight-bold"><?= $label .= $required ? ' <span class="text-danger">*</span>' : ''; ?></label>
            <?php } ?>
            <textarea class="form-control <?= $disabled ? 'form-control-solid' : '' ?>" <?= $maxlength ? 'maxlength="' . $maxlength . '"' : '' ?> name="<?= $name ?>" id="<?= $name ?>" rows="<?= $rows ?>" <?= $disabled ? 'readonly' : '' ?> <?= $required ? 'required' : '' ?>><?= $value ?></textarea>
    </div>
<?php
}
?>

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
                    Apakah Anda yakin melanjutkan proses ini?
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
                        // Find the closest form-group and get the label text
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

        function show_confirm_modal_<?= $id ?>() {
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


<script>
    function getSelectValue(e) {
        var value = $(e).find("option:selected").text();
        value = value.substring(value.indexOf("[") + 1, value.indexOf("]"));
        return value;
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.0/typed.min.js"></script>
<script>
    const monthNames = ["", "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };

    function showImage(e, caption) {
        var image = $(e).css("background-image").replace('url(', '').replace(')', '').replace(/\"/gi, "");
        $("#modal-image-content").attr("src", image);
        $("#modal-image").modal('show');
        $(".modal-caption").text(caption);
    }

    function formatNumberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatNumber(number) {
        var nilai = (parseInt(number)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        if (nilai.indexOf(".") > 0) nilai = nilai.substr(0, nilai.indexOf("."));
        return nilai;
    }

    function showMessage(title, message) {
        $("#message-title").text(title);
        $("#message-text").text(message);
        $("#modal-message").modal('show');
    }
</script>
<style>
    .animate__animated {
        animation-duration: 0.5s;
    }
</style>
<script>
    function updateLabel(input) {
        var fileName = input.files[0].name;
        input.nextElementSibling.innerHTML = fileName;
    }

    function add_tab_item(index, item_container, content_container, content) {
        $("#" + item_container).append("<div class='card card-custom mb-3 animate__animated animate__fadeIn' target='" + item_container + "-" + index + "'>" + $("#tab-custom-item").html() + "</div>");
        $("#" + content_container).append("<div class='card-body tab-custom d-none animate__animated animate__fadeIn' id='" + item_container + "-" + index + "'>" + $("#" + content).html() + "</div>");
    }

    function open_form(e) {
        var target = $(e).closest('.card-custom').attr('target');
        console.log(target);
        $(".tab-custom").each(function(i, e) {
            $(e).addClass("d-none animate__animated animate__fadeOut").removeClass("animate__fadeIn");
        });
        $("#" + target).removeClass('d-none').addClass("animate__animated animate__fadeIn").removeClass("animate__fadeOut");
    }

    function delete_item(e) {
        var target = $(e).closest('.card-custom').attr('target');
        $('#' + target).remove();
        $(e).closest('.card-custom').addClass("animate__animated animate__fadeOut").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
            $(this).remove();
        });
    }

    function show_loading() {
        $("#loading").show();
    }
    $("a").click(function() {
        if (this.href != null)
            if (this.href.indexOf("#") == -1 && this.target.indexOf("_blank") == -1) show_loading();
    });
    $("form").submit(function() {
        show_loading();
    });

    function escSpace(text) {
        if (text != null) return text.replace(/ /g, '_');
        return "-";
    }

    function descSpace(text) {
        return text.replace(/_/g, " ");
    }

    $(".m-item").click(function() {
        $("#kt_aside_mobile_toggle").click();
    })

    function replace_url(key, value) {
        show_loading();
        var url = window.location.href;
        var indexKey = url.indexOf(key);
        var newUrl = "";
        if (indexKey > 0) {
            var url2 = url.substr(indexKey, url.length);
            var indexValue = url2.indexOf('&');
            if (indexValue < 1) indexValue = url2.length;
            url2 = url2.substring(indexValue);
            url = url.substring(0, indexKey);
            newUrl = url + key + "=" + value + url2;
        } else {
            newUrl = url + "&" + key + "=" + value;
        }
        window.location.replace(newUrl);
    }

    function remove_url(url, key) {
        show_loading();
        var indexKey = url.indexOf(key);
        var newUrl = "";
        if (indexKey > 0) {
            var url2 = url.substr(indexKey, url.length);
            var indexValue = url2.indexOf('&');
            if (indexValue < 1) indexValue = url2.length;
            url2 = url2.substring(indexValue);
            url = url.substring(0, indexKey);
            newUrl = url + url2;
        } else {
            newUrl = url;
        }
        window.location.replace(newUrl);
    }
    $(document).ready(function() {
        $("#datatable").DataTable();
        $("#datatable2").DataTable();
    });

    function toggleAccordion(e) {
        var target = $(e).closest(".card").find(".collapse");
        console.log(target);
        target.collapse("toggle");
    }
</script>