<?php
function form_input($label, $name, $value, $type = 'text', $disabled = null, $maxlength = null, $func = null, $required = true, $form = true, $accept = "", $max = null, $min = null, $func2 = null)
{
    $value = (isset($_SESSION['old'][$name]) ? (!is_array($_SESSION['old'][$name]) ? $_SESSION['old'][$name] : null) : null) ?? $value ?? '';
?>
    <div class="<?= $form ? 'form-group' : '' ?>">
        <?php if (isset($label)) { ?>
            <label class="font-weight-bold"><?= $label .= $required ? ' <span class="text-danger">*</span>' : ''; ?></label>
        <?php } ?>
        <input
            <?= $type == 'file' ? ' style="display:none;" ' : '' ?>
            class="form-control <?= $disabled ? 'form-control-solid' : '' ?> <?= preg_replace("/[\[\]]/", '', $name) ?> "
            <?= $maxlength ? 'maxlength="' . $maxlength . '"' : '' ?>
            type="<?= $type ?>"
            name="<?= $name ?>"
            max="<?= $max ?>"
            min="<?= $min ?>"
            id="<?= $name ?>"
            value="<?= $value ?>"
            <?= $disabled ? 'readonly' : '' ?>
            onkeyup="<?= $func ?>"
            <?= $func2 ? 'onchange="' . $func2 . '"' : '' ?>
            <?= !$func2 && $func ? 'onchange="' . $func . '"' : '' ?>
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