<div id="kt_header" class="header header-fixed" style="background-color: white;">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <div class="mt-5">
                <button class="btn btn-light-danger font-weight-bolder btn-sm"><i class="la la-layer-group"></i> ARCHIVER V.2.0</button>
                <button class="btn btn-light-danger font-weight-bolder btn-sm ml-2"><i class="la la-calendar"></i> <?= date('d/m/Y H:i') ?></button>
                <button class="btn btn-light-danger font-weight-bolder btn-sm"><i class="la la-sitemap"></i> <?= $_SESSION['user']['division_name'] ?? "" ?></button>
            </div>
        </div>
        <div class="topbar">
            <div class="btn-group">
                <div class="topbar-item" data-toggle="dropdown">
                    <div class="btn btn-light-danger btn-sm mr-1">
                        <span class="font-weight-bolder"><i class="la la-home"></i> <?= $_SESSION['company_name'] ?? "" ?> </span>
                    </div>
                </div>
                <div class="dropdown-menu">
                    <?php
                    foreach ($this->mod_company->get_company() as $row) {
                    ?>
                        <a href="<?= base_url() . 'dashboard/crosspt/' . $row['id'] ?>" class="dropdown-item">
                            <span class="font-size-sm"><?= $row['name'] ?></span>
                        </a>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="dropdown">
                <?php $notification = $this->mod_notification->get_notification($_SESSION['user']['division_id']); ?>
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-danger">
                        <span class="la la-bell icon-xl text-<?= count($notification) > 0 ? 'danger' : 'muted' ?>"></span>
                        <span class="pulse-ring"></span>
                    </div>
                </div>
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
                    <div class="d-flex flex-column pt-12 bgi-size-cover rounded-top bg-danger pb-8">
                        <h4 class="d-flex flex-center rounded-top">
                            <span class="text-white">Pemberitahuan</span>
                            <?= count($notification) > 0 ? '<span class="btn btn-text btn-white btn-sm font-weight-bold btn-font-md ml-2">' . count($notification) . ' new</span>' : '' ?>
                        </h4>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active show p-8" id="topbar_notifications_notifications" role="tabpanel">
                            <div class="scroll pr-7 mr-n7" data-scroll="true" data-height="300" data-mobile-height="200">
                                <?php
                                foreach ($notification as $row) {
                                ?>
                                    <div class="d-flex align-items-center mb-8">
                                        <div class="symbol symbol-40 symbol-danger mr-5">
                                            <span class="symbol-label">
                                                <span class="fa fa-bell icon-md text-white"></span>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="#" class="text-dark text-hover-danger mb-1 font-size-lg font-weight-bold"><?= $row['title'] ?></a>
                                            <span class="text-muted font-size-sm">
                                                <?= $row['body'] ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-group">
                <div class="topbar-item" data-toggle="dropdown">
                    <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2 " id="kt_quick_user_toggle">
                        <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                        <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['user']['username'] ?? "Yohanes" ?></span>
                        <span class="symbol symbol-lg-35 symbol-25">
                            <?php
                            $avatar = base_url() . 'assets/images/empty-profile.png';
                            ?>
                            <img src="<?= $avatar ?>" style="width: 100px;object-fit:cover;" />
                        </span>
                    </div>
                </div>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= base_url() . 'login/do_logout' ?>">
                        <i class="la la-sign-out mr-3 text-dark"></i>
                        Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>