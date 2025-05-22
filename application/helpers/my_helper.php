<?php

require "vendor/autoload.php";

use NcJoes\OfficeConverter\OfficeConverter;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Element\TextRun;
use Mpdf\Mpdf;
use Spatie\PdfToImage\Pdf;
use PHPMailer\PHPMailer\PHPMailer;
use Google\Client;

function strposX($haystack, $needle, $number = 0)
{
    return strpos(
        $haystack,
        $needle,
        $number > 1 ?
            strposX($haystack, $needle, $number - 1) + strlen($needle) : 0
    );
}

function in_arrays($array1, $array2)
{
    if (empty($array1) || empty($array2)) return false;

    foreach ($array1 as $a) {
        foreach ($array2 as $b) {
            if ($a == $b) {
                return true;
            }
        }
    }
    return false;
}

function clean($string)
{
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function this_url()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function number_to_roman($number)
{
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if ($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}

function get_from_url($url, $paramName)
{
    $queryString = parse_url($url, PHP_URL_QUERY);

    if ($queryString) {
        parse_str($queryString, $queryParams);
        if (isset($queryParams[$paramName])) {
            return $queryParams[$paramName];
        }
    }
    return null;
}

function get_value_at_index($stringDelimited, $index)
{
    $values = explode(',', $stringDelimited);
    $values = array_map('trim', $values);
    $values = array_filter($values);
    if ($index >= 0 && $index < count($values)) {
        return $values[$index];
    } else {
        return null;
    }
}

function mkdirs($paths)
{
    $mpath = "";
    foreach (explode('/', $paths)  as $path) {
        $mpath .= $path . '/';
        if (!file_exists($mpath)) {
            mkdir($mpath);
        }
    }
}

function custom_date_format($tanggalInput, $format = 'Y-m-d')
{
    $tanggalInput ? $tanggalInput = $tanggalInput : $tanggalInput = '01011990';
    $tanggal = $tanggalInput;
    if (strlen($tanggalInput) == 19) {
        $tanggal = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalInput);
        $tanggal = $tanggal->format('Y-m-d H:i:s');
    } else if (strlen($tanggalInput) == 8) {
        $tanggal = DateTime::createFromFormat('Ymd', $tanggalInput);
        $tanggal = $tanggal->format('Y-m-d');
    } else if (strlen($tanggalInput) == 6) {
        $tanggal = DateTime::createFromFormat('Ym', $tanggalInput);
        $tanggal = $tanggal->format('Y-m-d');
    }
    return strtoupper(date($format, strtotime($tanggal)));
}

function day_to_ID($day)
{
    $englishDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    $dayIndex = array_search($day, $englishDays);

    if ($dayIndex !== false) {
        return $indonesianDays[$dayIndex];
    } else {
        return 'Invalid day';
    }
}

function calc_w_pdf($col)
{
    $max = 190;
    $size = ($max / 12) * $col;
    return $size;
}

function month_to_ID($month)
{
    $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    $monthIndex = array_search($month, $englishMonths);

    if ($monthIndex !== false) {
        return $indonesianMonths[$monthIndex];
    } else {
        return 'Invalid month';
    }
}

function nvl($raw, $val)
{
    if ($raw == null || $raw == '') {
        return $val;
    }
    return $raw;
}

function decode($val)
{
    foreach ($val as $row) {
        if (strlen($row) > 0) {
            return $row;
        }
    }
}

function get_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function crop_image($imgSrc)
{
    list($width, $height) = getimagesize($imgSrc);

    $myImage = imagecreatefromjpeg($imgSrc);

    if ($width > $height) {
        $y = 0;
        $x = ($width - $height) / 2;
        $smallestSide = $height;
    } else {
        $x = 0;
        $y = ($height - $width) / 2;
        $smallestSide = $width;
    }

    $thumbSize = 100;
    $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
    imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

    header('Content-type: image/jpeg');
    return imagejpeg($thumb);
}


function get_array_options($array, $id, $label_array)
{
    $options = array();
    foreach ($array as $row) {
        $label = '';
        foreach ($label_array as $row_x) {
            $label .= $row[$row_x] ?? $row_x;
        }
        $options[] = array($row[$id], $label);
    }
    return $options;
}

function terbilang($number)
{
    $words = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

    if ($number < 12) {
        return $words[$number];
    } elseif ($number < 20) {
        return terbilang($number - 10) . " belas";
    } elseif ($number < 100) {
        return terbilang(intval($number / 10)) . " puluh " . terbilang($number % 10);
    } elseif ($number < 200) {
        return "seratus " . terbilang($number - 100);
    } elseif ($number < 1000) {
        return terbilang(intval($number / 100)) . " ratus " . terbilang($number % 100);
    } elseif ($number < 2000) {
        return "seribu " . terbilang($number - 1000);
    } elseif ($number < 1000000) {
        return terbilang(intval($number / 1000)) . " ribu " . terbilang($number % 1000);
    } elseif ($number < 1000000000) {
        return terbilang(intval($number / 1000000)) . " juta " . terbilang($number % 1000000);
    } else {
        return "Angka terlalu besar";
    }
}

function rmnedir($folderPath)
{
    if (!is_dir($folderPath)) {
        return false;
    }

    $items = array_diff(scandir($folderPath), ['.', '..']);

    foreach ($items as $item) {
        $path = $folderPath . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            rmnedir($path);
        } else {
            unlink($path);
        }
    }

    return rmdir($folderPath);
}


function count_file($dir, $search)
{
    $files = array_filter(scandir($dir), function ($file) use ($dir, $search) {
        return is_file($dir . DIRECTORY_SEPARATOR . $file)
            && strpos($file, $search) !== false;
    });

    return count($files);
}

function crop_transparent($srcPath, $destPath = null)
{
    $img = imagecreatefrompng($srcPath);
    imagesavealpha($img, true); // Preserve transparency

    $width = imagesx($img);
    $height = imagesy($img);

    // Initialize bounding box
    $top = $height;
    $left = $width;
    $bottom = 0;
    $right = 0;

    // Scan image to find bounds
    for ($y = 0; $y < $height; ++$y) {
        for ($x = 0; $x < $width; ++$x) {
            $rgba = imagecolorat($img, $x, $y);
            $alpha = ($rgba & 0x7F000000) >> 24;

            // Check if pixel is not fully transparent
            if ($alpha < 127) {
                if ($x < $left)   $left = $x;
                if ($x > $right)  $right = $x;
                if ($y < $top)    $top = $y;
                if ($y > $bottom) $bottom = $y;
            }
        }
    }

    // If nothing found, skip
    if ($right < $left || $bottom < $top) {
        return false; // Fully transparent image
    }

    $newWidth = $right - $left + 1;
    $newHeight = $bottom - $top + 1;

    $cropped = imagecreatetruecolor($newWidth, $newHeight);
    imagesavealpha($cropped, true);
    $transparent = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
    imagefill($cropped, 0, 0, $transparent);

    imagecopy($cropped, $img, 0, 0, $left, $top, $newWidth, $newHeight);

    if (!$destPath) {
        $destPath = $srcPath; // overwrite original
    }

    imagepng($cropped, $destPath);
    imagedestroy($img);
    imagedestroy($cropped);

    return true;
}

function send_email($to, $subject, $body)
{
    $mail = new PHPMailer(true);
    try {
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";

        $mail->Host       = "mawarserver.ardetamedia.net";
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->Port       = 587;
        $mail->Username   = "yohanes.randy@corsys.co.id";
        $mail->Password   = "Kurnianto72639!!!";
        $mail->setFrom("yohanes.randy@corsys.co.id");

        $mail->addAddress($to);
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        // $mail->AltBody = $body;

        $mail->send();
    } catch (Exception $e) {
        $_SESSION["errmsg"] = "Email gagal dikirim. Pesan error: $mail->ErrorInfo | " . $e->getMessage();
    }
}

function send_notif($to, $title, $body)
{
    $client = new Client();
    $client->setAuthConfig('yortech-id-firebase-adminsdk-fbsvc-271ff775bd.json');
    $client->addScope('https://www.googleapis.com/auth/cloud-platform');
    $accessToken = $client->fetchAccessTokenWithAssertion();
    $accessToken = $accessToken['access_token'] ?? null;

    $projectId = 'yortech-id';
    $fcmUrl = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    $data = [
        'message' => [
            'token' => $to,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json; UTF-8',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
}

function docx_to_pdf_api($filename)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://v2.convertapi.com/convert/docx/to/pdf');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer secret_pzzTCDI4P74MgTxu'
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'File' => new CURLFile($filename . '.docx'),
        'StoreFile' => 'true'
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        $result = json_decode($response, true);

        // Download the converted PDF
        if (isset($result['Files'][0]['Url'])) {
            $pdfUrl = $result['Files'][0]['Url'];
            $pdfData = file_get_contents($pdfUrl);
            file_put_contents($filename . '.pdf', $pdfData);
        } else {
            $_SESSION['errmsg'] = $result;
        }
    }

    curl_close($ch);

    // return redirect($filename . '.pdf');
}

function edit_word($template, $data, $filename)
{
    $phpWord = new PhpWord();
    $templateProcessor = new TemplateProcessor($template);
    $jctable = new JcTable();
    $tableStyle = new TblWidth();

    $tableStyleArray = [
        'borderSize' => 10,
        'borderColor' => '999999',
        'alignment' => $jctable::CENTER,
        'unit' => $tableStyle::PERCENT,
        'width' => 5000,
    ];

    $trStyle = ['align' => 'center'];

    $thStyle = ['name' => 'Times New Roman', 'size' => 12, 'bold' => true];
    $tdStyle = ['name' => 'Times New Roman', 'size' => 12];

    $isTemp = false;

    foreach ($data as $key => $value) {
        if (str_contains($key, 'table')) {
            $section = $phpWord->addSection();
            $table = $section->addTable($tableStyleArray);

            $tno = 0;
            foreach ($value as $tr) {
                $table->addRow();
                foreach ($tr as $td) {
                    $table->addCell(2000, $trStyle)->addText($td, $tno == 0 ? $thStyle : $tdStyle);
                }
                $tno++;
            }

            $templateProcessor->setComplexBlock($key, $table);
        } else if (str_contains($key, 'html')) {
            $isTemp = true;
            if (!is_dir($filename)) mkdir($filename);

            // $mpdf = new Mpdf();
            $mpdf = new Mpdf([
                //     'format' => [210, 200],
                'margin_left'   => 0,
                'margin_right'  => 0,
                // 'margin_top'    => 0,
                // 'margin_bottom' => 0,
                // 'margin_header' => 0,
                // 'margin_footer' => 0,
            ]);
            $value = '<style>body {font-size: 14pt;}</style>' . $value;
            $mpdf->WriteHTML($value);
            $mpdf->Output($filename . '/temp.pdf', 'F');

            $spdf = new Pdf($filename . '/temp.pdf');
            $spdf->setOutputFormat('png');
            $spdf->saveAllPagesAsImages($filename);

            $section = $phpWord->addSection();
            for ($i = 1; $i <= count_file($filename, '.png'); $i++) {
                crop_transparent($filename . '/' . $i . '.png');
                $section->addText('${' . $key . '_' . $i . '}');
            }

            $templateProcessor->setComplexBlock($key, $section);
        } else if (str_contains($key, 'image')) {
            $isTemp = true;
            if (!is_dir($filename)) mkdir($filename);

            $section = $phpWord->addSection();
            foreach ($value as $i => $element) {
                if ($element != null) {
                    $section->addText('${' . $key . '_' . $i . '}');
                }
            }

            $templateProcessor->setComplexBlock($key, $section);
        } else {
            $templateProcessor->setValue($key, htmlspecialchars($value));
        }
    }

    if ($isTemp) {
        merge_element($template, $templateProcessor, $data, $filename);
    } else {
        $templateProcessor->saveAs($filename . ".docx");
    }

    rmnedir($filename);
}

function merge_element($template, $templateProcessor, $data, $filename)
{
    $tempFile = $filename . '/' . 'temp.docx';
    $templateProcessor->saveAs($tempFile);
    $templateProcessor2 = new TemplateProcessor($tempFile);

    foreach ($data as $key => $value) {
        if (str_contains($key, 'html')) {
            for ($i = 1; $i <= count_file($filename, '.png'); $i++) {
                $element = $filename . '/' . $i . '.png';
                if (file_exists($element)) {
                    list($width, $height) = getimagesize($element);
                    $templateProcessor2->setImageValue($key . '_' . $i, [
                        'path' => $element,
                        'width' => $width / 1.9,
                        'height' => $height / 1.9,
                        'ratio' => false
                    ]);
                }
            }
        } else if (str_contains($key, 'image')) {
            foreach ($value as $i => $element) {
                if ($element != null) {
                    $element = str_replace(base_url(), "", $element);
                    $templateProcessor2->setImageValue($key . '_' . $i, [
                        'path' => $element,
                        'width' => 300,
                        'height' => 300,
                    ]);
                }
            }
        }
    }

    $templateProcessor2->saveAs($filename . ".docx");
}

function show_pdf($filename)
{
    // return redirect($filename . ".pdf");

    echo "<html><head>";
    echo '<link rel="shortcut icon" href="https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2.png" />';
    echo "<title>" . str_replace('_', ' ', strtoupper(substr($filename, strrpos($filename, '/') + 1))) . "</title>";
    echo "</head><body style='margin:0px;padding:0px;overflow:hidden;'>";
    echo '<iframe src="' . base_url() . $filename . ".pdf" . '" style="width:100%;height:100%;"></iframe>';
    // echo '<iframe src="' . base_url() .  "document/show_word?file=" . $filename . ".docx" . '" style="width:100%;height:100%;"></iframe>';
    // echo "<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=" . base_url() . $filename . ".docx" . "' width='100%' height='100%'></iframe>";
    // echo '<iframe src="https://docs.google.com/gview?url=' . base_url() . $filename . ".docx" . '&embedded=true" width="100%" height="100%"></iframe>';
    echo "</body></html>";
}

function get_status_color($status)
{
    if (substr($status, 0, 1) == 'A') return 'success';
    if (substr($status, 0, 1) == 'P') return 'primary';
    if (substr($status, 0, 1) == 'N') return 'warning';
    return 'danger';
}
