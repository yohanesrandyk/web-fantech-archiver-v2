<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
    <div class="brand flex-column-auto" id="kt_brand">
        <a href="#" class="brand-logo">
            <img height="25" class="mt-5 ml-2" alt="Logo" src="https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2048x360.png" />
            <!-- <img height="25" class="mt-5 ml-2" alt="Logo" src="https://corsys.co.id/assets/img/logo-2.png" /> -->
        </a>
        <button class="brand-toggle btn btn-sm px-0 mt-5" id="kt_aside_toggle">
            <span class="svg-icon svg-icon svg-icon-xl">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
                        <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
                    </g>
                </svg>
            </span>
        </button>
    </div>
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
            <?php
            $url = uri_string();
            $url = str_replace("/", "_", $url);
            ?>
            <ul class="menu-nav">
                <?php
                $menu = array(
                    array('dashboard', 'fas fa-th-large', 'DASHBOARD', null, array('MK', 'AP', 'AD'), 0, array('dashboard'))
                );
                print_menu($menu, $url, $_SESSION['user']['role']);
                ?>

                <li class="menu-section ">
                    <h4 class="menu-text">DOKUMEN SURAT</h4>
                </li>

                <?php
                $menu = array(
                    // array('document', 'fas fa-envelope', 'DOKUMEN', array(
                    array(
                        'document/index/ca',
                        'fas fa-envelope',
                        'CASH ADVANCE',
                        null,
                        array('MK', 'AP'),
                        count($this->mod_document->get_document("%", 'ca', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%")),
                        array('document/index/ca', 'document/form/ca')
                    ),

                    array(
                        'document/index/pp',
                        'fas fa-envelope',
                        'PAYMENT REQUEST',
                        null,
                        array('MK', 'AP'),
                        count($this->mod_document->get_document("%", 'pp', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%")),
                        array('document/index/pp', 'document/form/pp')
                    ),

                    array(
                        'document/index/pr',
                        'fas fa-envelope',
                        'PURCHASE    REQUEST',
                        null,
                        array('MK', 'AP'),
                        count($this->mod_document->get_document("%", 'pr', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%")),
                        array('document/index/pr', 'document/form/pr')
                    ),

                    array(
                        'document/index/pc',
                        'fas fa-envelope',
                        'REIMBURSEMENT',
                        null,
                        array('MK', 'AP'),
                        count($this->mod_document->get_document("%", 'pc', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%")),
                        array('document/index/pc', 'document/form/pc')
                    ),

                    array(
                        'document/index/all',
                        'fas fa-envelope',
                        'CORRESPONDENCE',
                        null,
                        array('MK', 'AP'),
                        count($this->mod_document->get_document("%", 'all', $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%")),
                        array('document/index/all', 'document/form/all')
                    ),
                    // ), array('MK', 'AP'), 0, array('document')),
                );
                print_menu($menu, $url, $_SESSION['user']['role']);
                ?>
                <li class="menu-section ">
                    <h4 class="menu-text">DATA MASTER</h4>
                </li>
                <?php
                $menu = array(
                    array('company', 'fas fa-building', 'PERUSAHAAN', null, array('AD'), 0, array('company')),
                    array('division', 'fas fa-sitemap', 'DIVISI', null, array('AD'), 0, array('division')),
                    array('doctype', 'fas fa-file-alt', 'TIPE DOKUMEN', null, array('AD'), 0, array('doctype')),
                    array('docstatus', 'fas fa-file-alt', 'STATUS DOKUMEN', null, array('AD'), 0, array('docstatus')),
                    array('user', 'fas fa-user-tie', 'PENGGUNA', null, array('AD'), 0, array('user')),
                    //array('user/form/' . $_SESSION['user']['id'], 'fas fa-user', 'PROFIL', null, array('MK', 'AP', 'AD'), 0, array('user/form/' . $_SESSION['user']['id'])),
                );
                print_menu($menu, $url, $_SESSION['user']['role']);
                ?>
                <br>
                <li class="m-item menu-item" aria-haspopup="true">
                    <a href="<?= base_url('/') ?>login/do_logout" class="menu-link ">
                        <span class="fas fa-sign-out-alt menu-icon"></span>
                        <span class="menu-text font-weight-bold">KELUAR</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>


<?php
function is_active($url, $matches)
{
    foreach ($matches as $match) {
        if (preg_match('/' . str_replace('/', '_', $match . '/') . '/i', $url . "_")) {
            return true;
        }
    }
    return false;
}
function print_menu($menu, $url, $role)
{ ?>
    <?php foreach ($menu as $row) {
    ?>
        <?php
        if (in_arrays($row[4], explode(',', $role))) {
            if (isset($row[3])) { ?>
                <li class="menu-item menu-item-submenu <?= is_active($url, $row[6]) ? 'menu-item-active menu-item-open' : '' ?>" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle ">
                        <span class="<?= $row[1] ?> menu-icon <?= is_active($url, $row[6]) ? 'text-danger' : '' ?>"></span>
                        <span class="menu-text font-weight-bold <?= is_active($url, $row[6]) ? 'text-danger' : '' ?>"><?= $row[2] ?></span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <?php
                            foreach ($row[3] as $row_x) {
                                if (in_arrays($row_x[4], explode(',', $role))) {
                            ?>
                                    <li class="m-item menu-item <?= is_active($url, $row_x[6]) ? 'menu-item-active' : '' ?>" aria-haspopup="true">
                                        <a href="<?= base_url('/') . $row[0] . '/' . $row_x[0] ?>" class="menu-link ">
                                            <span class="<?= $row_x[1] ?> menu-icon <?= is_active($url, $row_x[6]) ? 'text-danger' : '' ?>"></span>
                                            <span class="menu-text font-weight-bold <?= is_active($url, $row_x[6]) ? 'text-danger' : '' ?>"><?= $row_x[2] ?></span>
                                            <?= $row_x[5] > 0 ? '<span class="menu-label"><span class="label label-danger label-inline">' . $row_x[5] . '</span></span>' : '' ?>
                                        </a>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </li>
            <?php } else { ?>
                <li class="m-item menu-item <?= is_active($url, $row[6]) ? 'menu-item-active' : '' ?>" aria-haspopup="true">
                    <a href="<?= base_url('/') . $row[0] ?>" class="menu-link ">
                        <span class="<?= $row[1] ?> menu-icon <?= is_active($url, $row[6]) ? 'text-danger' : '' ?>"></span>
                        <span class="menu-text font-weight-bold <?= is_active($url, $row[6]) ? 'text-danger' : '' ?>"><?= $row[2] ?></span>
                        <?= $row[5] > 0 ? '<span class="menu-label"><span class="label label-danger label-inline">' . $row[5] . '</span></span>' : '' ?>
                    </a>
                </li>
        <?php }
        } ?>
    <?php } ?>
<?php } ?>