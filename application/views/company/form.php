<form action="<?= base_url() ?>company/store" method="POST" enctype="multipart/form-company">
    <div class="card card-custom bg-danger text-white mb-5">
        <div class="card-header flex-wrap">
            <div class="card-title">
                <h2 class="card-label font-weight-bolder text-white">
                    <i class="fas fa-mail-bulk text-white mr-4"></i>
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
            <input type="hidden" name="id" value="<?= $company['id'] ?? '' ?>">
            <div class="row">
                <div class="col-lg-3">
                    <?php form_input(label: 'Kode Perusahaan', name: 'code', value: $company['code'] ?? '') ?>
                </div>
                <div class="col-lg-8">
                    <?php form_input(label: 'Nama Perusahaan', name: 'name', value: $company['name'] ?? '') ?>
                </div>
            </div>
            <?php form_input(label: 'No. Telpon', name: 'phone', value: $company['phone'] ?? '', required: false) ?>
            <?php form_textarea(label: 'Alamat', rows: 6, name: 'address', value: $company['address'] ?? '', required: false) ?>
        </div>
    </div>
</form>