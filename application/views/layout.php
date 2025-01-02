<?php
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: DENY");

if (!isset($_SESSION['user'])) {
    // $this->session->set_flashdata('errmsg', 'Perhatian! Harap login menggunakan username dan password Anda.');
    redirect('login');
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>ArcHiver v.2.0 <?= '| ' . $title ?? '' ?></title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/plugins/custom/datatables/datatables.bundle1ff3.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/plugins/global/plugins.bundle1ff3.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/css/style.bundle1ff3.css" rel="stylesheet" type="text/css" />

    <link href="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/css/themes/layout/aside/light.css" rel="stylesheet" type="text/css" />

    <link rel="shortcut icon" href="https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2.png" />
    <script>
        var KTAppSettings = {
            "breakpoints": {
                "sm": 576,
                "md": 768,
                "lg": 992,
                "xl": 1200,
                "xxl": 1400
            },
            "colors": {
                "theme": {
                    "base": {
                        "white": "#ffffff",
                        "primary": "#3699FF",
                        "secondary": "#E5EAEE",
                        "success": "#1BC5BD",
                        "info": "#8950FC",
                        "danger": "#FFA800",
                        "danger": "#F64E60",
                        "light": "#E4E6EF",
                        "dark": "#181C32"
                    },
                    "light": {
                        "white": "#ffffff",
                        "primary": "#E1F0FF",
                        "secondary": "#EBEDF3",
                        "success": "#C9F7F5",
                        "info": "#EEE5FF",
                        "danger": "#FFF4DE",
                        "danger": "#FFE2E5",
                        "light": "#F3F6F9",
                        "dark": "#D6D6E0"
                    },
                    "inverse": {
                        "white": "#ffffff",
                        "primary": "#ffffff",
                        "secondary": "#3F4254",
                        "success": "#ffffff",
                        "info": "#ffffff",
                        "danger": "#ffffff",
                        "danger": "#ffffff",
                        "light": "#464E5F",
                        "dark": "#ffffff"
                    }
                },
                "gray": {
                    "gray-100": "#F3F6F9",
                    "gray-200": "#EBEDF3",
                    "gray-300": "#E4E6EF",
                    "gray-400": "#D1D3E0",
                    "gray-500": "#B5B5C3",
                    "gray-600": "#7E8299",
                    "gray-700": "#5E6278",
                    "gray-800": "#3F4254",
                    "gray-900": "#181C32"
                }
            },
            "font-family": "Poppins"
        };
    </script>
    <script src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/plugins/global/plugins.bundle.js"></script>
    <script src="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/js/scripts.bundle1ff3.js"></script>
    <script src="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/js/pages/custom/profile/profile1ff3.js"></script>
    <script src="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/plugins/custom/datatables/datatables.bundle1ff3.js"></script>
    <!-- <script src="<?= base_url("assets"); ?>/metronic/html/demo1/dist/assets/js/pages/crud/ktdatatable/base/html-table1ff3.js?v=7.1.2"></script> -->

    <style>
        body {
            -webkit-animation: fadein 2s;
            -moz-animation: fadein 2s;
            -ms-animation: fadein 2s;
            -o-animation: fadein 2s;
            animation: fadein 2s;
        }

        @keyframes fadein {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @-moz-keyframes fadein {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @-webkit-keyframes fadein {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @-ms-keyframes fadein {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @-o-keyframes fadein {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        ::-webkit-scrollbar {
            width: 11px;
            height: 11px;
        }

        ::-webkit-scrollbar-button {
            width: 0px;
            height: 0px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border: 0px none #ffffff;
            border-radius: 50px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border: 0px none #ffffff;
        }

        ::-webkit-scrollbar-corner {
            background: transparent;
        }

        div#loading {
            background: rgba(255, 255, 255, 1.0) url('<?= base_url() . 'assets/images/red-cube.gif' ?>') no-repeat 50% 50%;
            /* background: white; */
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 999;
        }

        .blur {
            -webkit-filter: blur(5px);
            -moz-filter: blur(5px);
            -o-filter: blur(5px);
            -ms-filter: blur(5px);
            filter: blur(5px);
        }

        @media all and (min-width: 480px) {
            .desktop {
                display: block;
            }

            .mobile {
                display: none;
            }
        }

        @media all and (max-width: 479px) {
            .desktop {
                display: none;
            }

            .mobile {
                display: block;
            }
        }
    </style>
</head>

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed bg-white">
        <a href="index.html">
            <img height="30" alt="Logo" src="https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2048x360.png" />
        </a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
                <span></span>
            </button>
            <button class="btn btn-hover-text-danger p-0 ml-2" id="kt_header_mobile_topbar_toggle">
                <span class="svg-icon svg-icon-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24" />
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
                </span>
            </button>
        </div>
    </div>

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <?php $this->view('sidebar') ?>
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <?php $this->view('header') ?>
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid" style="margin-top: -50px;">
                        <div class="container-fluid">
                            <div class="mobile" style="height:70px;"></div>
                            <?php $this->view('container') ?>
                        </div>
                    </div>
                </div>
                <?php $this->view('footer') ?>
            </div>
        </div>
    </div>

    <div id="loading" style="display: none;"></div>
</body>

</html>