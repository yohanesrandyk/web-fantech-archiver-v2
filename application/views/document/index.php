<div class="card card-custom bg-danger">
    <div class="card-header flex-wrap">
        <div class="card-title">
            <h2 class="card-label font-weight-bolder text-white">
                <i class="fas fa-envelope text-white mr-4"></i>
                <?= $title ?>
            </h2>
        </div>
        <div class="card-toolbar">
            <div class="dropdown dropdown-inline">
                <button type="button" class="btn btn-white text-danger font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Download
                </button>
                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    <ul class="navi flex-column navi-hover py-2">
                        <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-danger pb-2">Pilih format:</li>
                        <li class="navi-item">
                            <a href="#" class="navi-link" id="export-print">
                                <span class="navi-icon"><i class="la la-print"></i></span>
                                <span class="navi-text">Print</span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link" id="export-copy">
                                <span class="navi-icon"><i class="la la-copy"></i></span>
                                <span class="navi-text">Copy</span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link" id="export-excel">
                                <span class="navi-icon"><i class="la la-file-excel-o"></i></span>
                                <span class="navi-text">Excel</span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link" id="export-csv">
                                <span class="navi-icon"><i class="la la-file-text-o"></i></span>
                                <span class="navi-text">CSV</span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link" id="export-pdf">
                                <span class="navi-icon"><i class="la la-file-pdf-o"></i></span>
                                <span class="navi-text">PDF</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php //if ($_SESSION['user']['role'] == 'MK') { 
            ?>
            <a href="<?= base_url() ?>document/form/<?= $type ?>/0" class="btn btn-white text-danger font-weight-bolder ml-2" id="btn-add">
                <i class="la la-plus text-danger"></i>
                Tambah Data
            </a>
            <?php //} 
            ?>
        </div>
    </div>
</div>
<div class="card card-custom mt-5">
    <div class="card-body">
        <table class="table table-bordered table-responsive nowrap" id="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NO. DOKUMEN</th>
                    <?php if ($type == "ca" || $type == "pc" || $type == "pp") { ?>
                        <th>PROYEK</th>
                        <th>TANGGAL SURAT</th>
                        <th>NOMINAL</th>
                    <?php } else if ($type == "all") { ?>
                        <th>SUBJEK</th>
                        <th>DIVISI</th>
                        <th>TANGGAL SURAT</th>
                    <?php } ?>
                    <th>STATUS</th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th class="searching"><input type="text" class="form-control" /></th>
                    <?php if ($type == "ca" || $type == "pc" || $type == "pp") { ?>
                        <th class="searching"><input type="text" class="form-control" /></th>
                        <th class="searching"><input type="text" class="form-control" /></th>
                        <th class="searching"><input type="text" class="form-control" /></th>
                    <?php } else if ($type == "all") { ?>
                        <th class="searching"><input type="text" class="form-control" /></th>
                        <th class="searching"><input type="text" class="form-control" /></th>
                        <th class="searching"><input type="text" class="form-control" /></th>
                    <?php } ?>
                    <th class="searching"><input type="text" class="form-control" /></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $notdone = [];
                foreach ($documents as $row) {
                    if ($row['status'] != 'A' && substr($row['status'], 0, 1) != 'N' && $type == 'ca') {
                        $notdone[] = $row['document_number'];
                    }
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['document_number'] ?></td>
                        <?php if ($type == "ca" || $type == "pc" || $type == "pp") { ?>
                            <td><?= $row['project'] ?></td>
                            <td><?= custom_date_format($row['create_date'], 'd/m/Y') ?></td>
                            <td>Rp. <?= number_format($row['transfer_amount'] == 0 ? $row['transfer_amount_2'] : $row['transfer_amount']) ?></td>
                        <?php } else if ($type == "all") { ?>
                            <td><?= $row['subject'] ?></td>
                            <td><?= $row['user_create'] ?></td>
                            <td><?= custom_date_format($row['release_date'], 'd/m/Y') ?></td>
                        <?php } ?>
                        <td><span class="label label-<?= substr($row['status'], 0, 1) == 'A' ? 'success' : (substr($row['status'], 0, 1) == 'P' ? 'primary' : 'danger') ?> label-inline font-weight-lighter mr-2"><?= $row['docstatus_name'] ?></span></td>
                        <td>
                            <a href="<?= base_url() ?>document/form/<?= $type ?>/<?= $row['id'] ?>" class="btn btn-danger btn-sm"><i class="la la-file"></i> REVIEW</a>
                            <a href="<?= base_url() ?>document/cetak/<?= $type ?>/<?= $row['id'] ?>" class="btn btn-primary btn-sm"><i class="la la-print"></i> CETAK</a>
                        </td>
                    </tr>
                <?php
                } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        <?php if (count($notdone) > 1) { ?>
            $("#btn-add").attr("href", "#");
            $("#btn-add").click(function(e) {
                e.preventDefault();
                $("#text-errmsg").html("Dokumen <?= $notdone[count($notdone) - 1] ?> belum selesai, harap selesaikan dokumen terlebih dahulu.");
                show_errmsg_modal();
            });
        <?php } ?>
        var table = $("#table").DataTable({
            order: [],
            ordering: true,
            orderCellsTop: true,
            buttons: [{
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });

        $('#export-print').on('click', function(e) {
            e.preventDefault();
            table.button(0).trigger();
        });

        $('#export-copy').on('click', function(e) {
            e.preventDefault();
            table.button(1).trigger();
        });

        $('#export-excel').on('click', function(e) {
            e.preventDefault();
            table.button(2).trigger();
        });

        $('#export-csv').on('click', function(e) {
            e.preventDefault();
            table.button(3).trigger();
        });

        $('#export-pdf').on('click', function(e) {
            e.preventDefault();
            table.button(4).trigger();
        });

        $('.searching input').on('keyup change', function() {
            var i = $(this).parent().index();
            if (table.column(i).search() !== this.value) {
                table
                    .column(i)
                    .search(this.value)
                    .draw();
            }
        });
    });
</script>