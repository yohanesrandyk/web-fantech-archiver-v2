<form action="<?= base_url() ?>user/store" method="POST" enctype="multipart/form-user">
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
                <a href="<?= base_url() ?>user" class="btn btn-white text-danger font-weight-bolder ml-2">
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
                <span class="font-weight-bolder">Form Pengguna</span>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>">
            <?php form_input(label: 'Nama', name: 'fullname', value: $user['fullname'] ?? '') ?>
            <div class="row mb-5">
                <div class="col-lg-6">
                    <?php form_input(label: 'Username', name: 'username', value: $user['username'] ?? '') ?>
                </div>
                <div class="col-lg-6">
                    <?php form_input(label: 'Password', name: 'password', value: $user['password'] ?? '') ?>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-6">
                    <?php form_select(
                        label: 'Divisi',
                        name: 'division_id',
                        options: get_array_options($divisions, 'id', array('name', ' [', 'code', ']')),
                        value: $user['division_id'] ?? ''
                    ) ?>
                </div>
                <div class="col-lg-6">
                    <?php form_select(
                        label: 'Role',
                        name: 'role',
                        options: array(array('MK', 'MAKER [MK]'), array('AP', 'APPROVER [AP]'), array('AD', 'ADMIN [AD]')),
                        value: $user['role'] ?? ''
                    ) ?>
                </div>
            </div>

            <?php form_input(label: 'Email', type:'email', name: 'email', value: $user['email'] ?? '') ?>
        </div>
    </div>
</form>