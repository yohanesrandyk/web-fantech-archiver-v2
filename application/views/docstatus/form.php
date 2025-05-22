<form action="<?= base_url() ?>docstatus/store" method="POST" enctype="multipart/form-docstatus">
    <div class="card card-custom bg-danger text-white mb-5">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <h2 class="card-label font-weight-bolder text-white">
                    <i class="fas fa-file-alt text-white mr-4"></i>
                    <?= $title ?>
                </h2>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-dark font-weight-bolder" onclick="show_confirm_modal_form()">
                    <span class="svg-icon svg-icon-md">
                        <i class="la la-save"></i>
                    </span>Simpan
                </button>
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
                <span class="font-weight-bolder">FORM <?= $title ?></span>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="doctype_ids" value="<?= $row['doctype_ids'] ?? '' ?>">
            <?php
            $options = get_array_options($doctypes, 'code', array('name', ' [', 'code', ']'));
            array_push($options, array('ALL', 'SEMUA DOKUMEN [ALL]'));
            form_select(
                label: 'Tipe Dokumen',
                name: 'doctype_ids_',
                options: $options,
                value: $row['doctype_ids'] ?? '',
                func: 'set_doctype_ids(this)',
                multiple: true,
            ) ?>
            <table class="d-none" id="table-docstatus">
                <?php function item_docstatus($row, $divisions, $docstatus)
                { ?>
                    <tr class="item" draggable="true">
                        <td>
                            <button class="btn btn-danger btn-sm btn-icon" type="button"><i class="la la-arrows"></i></button>
                        </td>
                        <td>
                            <input type="hidden" name="id[]" value="<?= $row['id'] ?? '' ?>">
                            <?php form_input(label: null, name: 'code[]', value: $row['code'] ?? '', required: false) ?>
                        </td>
                        <td>
                            <?php form_input(label: null, name: 'name[]', value: $row['name'] ?? '', required: false) ?>
                        </td>
                        <td>
                            <?php form_select(
                                label: null,
                                name: 'approve_code[]',
                                options: get_array_options($docstatus, 'code', array('name', ' [', 'code', ']')),
                                value: $row['approve_code'] ?? '0',
                                required: false
                            ) ?>
                        </td>
                        <td>
                            <?php form_select(
                                label: null,
                                name: 'to_division_id[]',
                                options: get_array_options($divisions, 'id', array('name', ' [', 'code', ']')),
                                value: $row['to_division_id'] ?? '0',
                                required: false,
                            ) ?>
                        </td>
                        <td>
                            <input type="hidden" name="roles[]" value="<?= $row['roles'] ?? '' ?>">
                            <?php form_select(
                                label: null,
                                name: 'roles_[]',
                                options: array(array('MK', 'MAKER [MK]'), array('AP', 'APPROVER [AP]')),
                                value: $row['roles'] ?? '',
                                required: false,
                                multiple: true,
                                func: 'set_roles(this)',
                            ) ?>
                        </td>
                        <td>
                            <input type="hidden" name="cc_division_ids[]" value="<?= $row['cc_division_ids'] ?? '' ?>">
                            <?php form_select(
                                label: null,
                                name: 'cc_division_ids_[]',
                                options: get_array_options($divisions, 'id', array('name', ' [', 'code', ']')),
                                value: $row['cc_division_ids'] ?? '0',
                                multiple: true,
                                required: false,
                                func: 'set_cc_division_ids(this)',
                            ) ?>
                        </td>
                        <td>
                            <?php form_select(
                                label: null,
                                name: 'reject_code[]',
                                options: get_array_options($docstatus, 'code', array('name', ' [', 'code', ']')),
                                value: $row['reject_code'] ?? '0',
                                required: false
                            ) ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-sm btn-danger" onclick="$(this).closest('tr').remove()"><i class="la la-trash"></i></button>
                        </td>
                    </tr>
                <?php
                }
                item_docstatus([], $divisions, $docstatus);
                ?>
            </table>
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th></th>
                        <th nowrap="nowrap">KODE<span class="text-white">-</span>STATUS</th>
                        <th nowrap="nowrap">STATUS<span class="text-white">-</span>DOKUMEN</th>
                        <th nowrap="nowrap">KODE<span class="text-white">-</span>APPROVE</th>
                        <th nowrap="nowrap">TO<span class="text-white">-</span>DIVISI</th>
                        <th nowrap="nowrap">ROLES<span class="text-white">-----</span></th>
                        <th nowrap="nowrap">CC<span class="text-white">-</span>DIVISI</th>
                        <th nowrap="nowrap">KODE<span class="text-white">-</span>REVISI</th>
                        <th><button type="button" class="btn btn-icon btn-sm btn-success" onclick="$(this).closest('table').find('tbody').append($('#table-docstatus tbody').html());"><i class="la la-plus"></i></button></th>
                    </tr>
                </thead>
                <tbody class="list">
                    <?php
                    foreach ($docstatus as $row) {
                        if ($row['code'] == 'A') continue;
                        if ($row['code'] == 'R') continue;
                        item_docstatus($row, $divisions, $docstatus);
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<style>
    .item.dragging :where(.item, td) {
        opacity: 0;
    }
</style>
<script>
    function set_cc_division_ids(e) {
        var cc_division_ids = $(e).val();
        $(e).closest("tr").find("input[name='cc_division_ids[]']").val(cc_division_ids.join(","));
    }

    function set_roles(e) {
        var roles = $(e).val();
        $(e).closest("tr").find("input[name='roles[]']").val(roles.join(","));
    }

    function set_doctype(e) {
        var doctype_ids = $(e).val();
        $(e).closest("form").find("input[name='doctype_ids']").val(doctype_ids.join(","));
        window.location = "<?= base_url() ?>docstatus/form?type=" + doctype_ids.join(",");
    }
    const tbody = document.querySelector(".list");
    const items = document.querySelectorAll(".item");

    items.forEach(item => {
        item.setAttribute("draggable", "true");
        item.addEventListener("dragstart", event => {
            item.classList.add("dragging");
            event.dataTransfer.setData("text/plain", item.id);
        });
        item.addEventListener("dragend", () => item.classList.remove("dragging"));
    });

    tbody.addEventListener("dragover", event => {
        event.preventDefault();
        const draggingItem = tbody.querySelector(".dragging");
        const afterElement = getDragAfterElement(tbody, event.clientY);
        if (afterElement == null) {
            tbody.appendChild(draggingItem);
        } else {
            tbody.insertBefore(draggingItem, afterElement);
        }
    });

    tbody.addEventListener("drop", event => {
        event.preventDefault();
        const id = event.dataTransfer.getData("text/plain");
        const draggedItem = document.getElementById(id);
        tbody.appendChild(draggedItem);
    });

    function getDragAfterElement(container, y) {
        const elements = [...container.querySelectorAll(".item:not(.dragging)")];
        return elements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return {
                    offset: offset,
                    element: child
                };
            } else {
                return closest;
            }
        }, {
            offset: Number.NEGATIVE_INFINITY
        }).element;
    }
</script>