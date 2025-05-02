<?php
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: DENY");
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>ArcHiver</title>
    <meta name="description" content="Aplikasi pengelolaan dokumen dan approval yang dirancang untuk menyederhanakan proses kerja Anda. Dengan fitur penyimpanan aman, pengaturan izin akses, dan alur persetujuan yang efisien, Archiver membantu tim Anda bekerja lebih produktif dan terorganisir. Temukan solusi manajemen dokumen yang canggih dan user-friendly bersama Archiver." />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link href="<?php echo base_url("assets"); ?>/metronic/html/demo1/dist/assets/plugins/global/plugins.bundle1ff3.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo base_url("assets"); ?>/metronic/html/demo1/dist/assets/css/style.bundle1ff3.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2.png" />
    <style>
        body {
            -webkit-animation: fadein 2s;
            -moz-animation: fadein 2s;
            -ms-animation: fadein 2s;
            -o-animation: fadein 2s;
            animation: fadein 2s;
            width: 100%;
            overflow: hidden;
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

        div#loading-image {
            background: rgba(255, 255, 255, 1.0) url('https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2048x360.png') no-repeat 50% 50%;
            background-size: contain;
        }

        div#loading {
            background: white;
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

        .typed-cursor {
            font-size: 20px;
            opacity: 1;
            -webkit-animation: blink 0.7s infinite;
            -moz-animation: blink 0.7s infinite;
            animation: blink 0.7s infinite;
        }

        #loading_text {
            font-size: 20px
        }

        #loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .updown {
            animation: updownanim 1s linear infinite;
            position: absolute;
            /* left: 0; */
            /* bottom: 0; */
        }

        @keyframes updownanim {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>

<body style="background-color: white;" onload="window.scrollTo(0, 0);">
    <div class="row">
        <div class="col-lg-7 d-none d-lg-block" style="background-image: url(https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1000&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8b2ZmaWNlJTIwYnVpbGRpbmd8ZW58MHx8MHx8fDA%3D);background-position: center center;background-size: cover;background-repeat: no-repeat;min-height: 900px;">
        </div>
        <div class="col-lg-5">
            <div style="padding:50px;">
                <img style="height:50px;" src="https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2048x360.png" alt="">
                <br><br><br><br>
                <div>
                    <h3 class="font-size-h1">Sign In</h3>
                    <p class="text-muted font-weight-bold">Silahkan memasukkan userid dan password anda!</p>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-8">
                        <?php if ($this->session->flashdata('result_login')) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $this->session->flashdata('result_login') ?>
                            </div>
                        <?php } ?>
                        <form action="<?= base_url() . 'login/do_login' ?>" method="post">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>" />
                            <div class="form-group fv-plugins-icon-container">
                                <input class="form-control form-control-solid h-auto py-5 px-6" type="text" placeholder="User ID" name="username" autocomplete="off">
                                <div class="fv-plugins-message-container"></div>
                            </div>
                            <div class="form-group fv-plugins-icon-container">
                                <input class="form-control form-control-solid h-auto py-5 px-6" type="password" placeholder="Password" name="password" autocomplete="off">
                                <div class="fv-plugins-message-container"></div>
                            </div>
                            <input type="hidden" id="fcm_token" name="fcm_token">
                            <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                                <input type="submit" class="btn btn-danger font-weight-bold px-9 py-4 my-3" value="Sign In">
                            </div>
                            <div></div>
                        </form>
                    </div>
                </div>
                <br><br>
                Powered by yohanesrandy
            </div>
        </div>
    </div>
    <div id="loading" style="display: none;">
        <div id="loading-image" class="w-25 h-100 updown">
        </div>
    </div>
    <?php $this->view('firebase') ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.0/typed.min.js"></script>
    <script>
        $("form").submit(function() {
            $("#loading").show();
        });
    </script>
</body>

</html>