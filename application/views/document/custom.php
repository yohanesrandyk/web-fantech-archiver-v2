<?php

use function Clue\StreamFilter\fun;

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
                    <td><span class="label label-<?= get_status_color($row['status_update']) ?> label-inline font-weight-lighter mr-2"><?= $row['name'] ?></span></td>
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