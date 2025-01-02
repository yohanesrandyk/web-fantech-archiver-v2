<form action="<?= base_url() ?>division/store" method="POST" enctype="multipart/form-data">
    <div class="card card-custom bg-danger mb-5">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <h2 class="card-label font-weight-bolder text-white">
                    <i class="fas fa-sitemap text-white mr-4"></i>
                    <?= $title ?>
                </h2>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-dark font-weight-bolder" onclick="show_confirm_modal_form()">
                    <span class="svg-icon svg-icon-md">
                        <i class="la la-save"></i>
                    </span>Simpan
                </button>
                <a href="<?= base_url() ?>division" class="btn btn-white text-danger font-weight-bolder ml-2">
                    <i class="la la-arrow-left text-danger"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <?php show_confirm_modal('form') ?>

    <?php
    if (isset($_GET['id'])) {
        $data = $this->mod_division->find_division_by_id($_GET['id'])[0];
    }
    ?>

    <div class="card card-custom">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <span class="font-weight-bolder">Form Tipe Dokumen</span>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
            <div class="row">
                <div class="col-lg-4">
                    <?php form_input(label: 'Kode Divisi', name: 'code', value: $data['code'] ?? '') ?>
                </div>
                <div class="col-lg-8">
                    <?php form_input(label: 'Nama Divisi', name: 'name', value: $data['name'] ?? '') ?>
                </div>
            </div>
        </div>
    </div>
</form>