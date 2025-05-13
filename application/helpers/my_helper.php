<?php
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
