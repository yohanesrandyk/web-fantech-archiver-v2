<form action="<?= base_url() ?>user/store" method="POST" enctype="multipart/form-data">
    <div class="card card-custom bg-danger mb-5">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <h2 class="card-label font-weight-bolder text-white">
                    <i class="fas fa-user-tie text-white mr-4"></i>
                    <?= isset($title) ? $title : 'Tambah Petugas' ?>
                </h2>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-dark font-weight-bolder" onclick="show_confirm_modal_form()">
                    <span class="svg-icon svg-icon-md">
                        <i class="la la-save"></i>
                    </span>Simpan
                </button>
                <a href="<?= base_url() ?>petugas" class="btn btn-white text-danger font-weight-bolder ml-2">
                    <i class="la la-arrow-left text-danger"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <?php show_confirm_modal('form') ?>


    <?php
    if (isset($_GET['id'])) {
        $data = $this->mod_user->find_user_by_id($_GET['id'])[0];
    }
    ?>

    <div class="card card-custom">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <span class="font-weight-bolder">Form Pengguna</span>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
            <?php form_input(label: 'Nama', name: 'fullname', value: $data['fullname'] ?? '') ?>
            <div class="row mb-5">
                <div class="col-lg-6">
                    <?php form_input(label: 'Username', name: 'username', value: $data['username'] ?? '') ?>
                </div>
                <div class="col-lg-6">
                    <?php form_input(label: 'Password', name: 'password', value: $data['password'] ?? '') ?>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-6">
                    <?php form_select(
                        label: 'Divisi',
                        name: 'division_id',
                        options: get_array_options($this->mod_division->get_division(), 'id', array('name', ' [', 'code', ']')),
                        value: $data['division_id'] ?? ''
                    ) ?>
                </div>
                <div class="col-lg-6">
                    <?php form_select(
                        label: 'Role',
                        name: 'role',
                        options: array(array('MK', 'Maker [MK]'), array('AP', 'Approver [AP]'), array('DR', 'Direktur [DR]'), array('AC', 'Akunting [AC]')),
                        value: $data['role'] ?? ''
                    ) ?>
                </div>
            </div>

            <?php form_input(label: 'Email', name: 'email', value: $data['email'] ?? '') ?>
        </div>
    </div>
</form>