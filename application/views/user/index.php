<div class="card card-custom bg-danger">
    <div class="card-header flex-wrap">
        <div class="card-title">
            <h2 class="card-label font-weight-bolder text-white">
                <i class="fas fa-user-tie text-white mr-4"></i>
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
            <a href="<?= base_url() ?>user/form" class="btn btn-white text-danger font-weight-bolder ml-2">
                <i class="la la-plus text-danger"></i>
                Tambah Data
            </a>
        </div>
    </div>
</div>
<div class="card card-custom mt-5">
    <div class="card-body">
        <table class="table table-bordered nowrap table-responsive" id="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>USERNAME</th>
                    <th>FULLNAME</th>
                    <th>DIVISI</th>
                    <th>EMAIL</th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th class="searching"><input type="text" class="form-control" /></th>
                    <th class="searching"><input type="text" class="form-control" /></th>
                    <th class="searching"><input type="text" class="form-control" /></th>
                    <th class="searching"><input type="text" class="form-control" /></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($this->mod_user->get_user() as $row) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['fullname'] ?></td>
                        <td><?= $row['division_name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td>
                            <a href="<?= base_url() ?>user/form?id=<?= $row['id'] ?>" class="btn btn-danger btn-icon btn-sm"><i class="la la-pencil"></i></a>
                            <a href="<?= base_url() ?>user/delete?id=<?= $row['id'] ?>" class="btn btn-danger btn-icon btn-sm"><i class="la la-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
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