<?php
class Encryption
{
    public function f_acak_pwd($ag_nama_user, $ag_digit, $ag_company)
    {
        $s_huruf = $ag_company . 'QAZWSXEDCRFVTGBYHNUJMIKLOP' . $ag_nama_user . '1,2.3_4+5#6!7*8/90';
        $s_hasil = substr($s_huruf, $ag_digit, 1);
        return $s_hasil;
    }

    public function f_pwd_kombinasi($ag_nama_user, $ag_password)
    {
        $s_hasil = '';
        for ($i = 0; $i < strlen($ag_password); $i++) {
            $ls_posisi_pass = substr($ag_password, $i, 1);
            $ls_posisi_user = substr($ag_nama_user, $i, 1);
            if ($ls_posisi_user == null) {
                $ls_posisi_user = ' ';
            }
            $s_hasil = $s_hasil . $ls_posisi_pass . $ls_posisi_user;
        }
        return $s_hasil;
    }

    public function f_asci_hexa_pwd($ags_word, $ag_key)
    {
        $ls_hexout    = '';
        $bil_hexa     = '';
        $lc_hexdigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $count        = strlen($ags_word);
        for ($i = 0; $i < $count; $i++) {
            $ll_decin = ord(substr($ags_word, $i, 1)) + $ag_key;
            for ($ll_digit = 7; $ll_digit >= 0; $ll_digit--) {
                $ll_quotient = intval($ll_decin / pow(16, $ll_digit));
                $ll_decin    = $ll_decin - $ll_quotient * pow(16, $ll_digit);
                $ls_hexout   = $ls_hexout . $lc_hexdigits[$ll_quotient];
            }
            $bil_hexa = $bil_hexa . substr($ls_hexout, -2);
            //echo $i.'. ';
            //ECHO substr($ls_hexout,-2);
            //ECHO "<br>";
        }
        return $bil_hexa;
    }

    public function f_hexa_asci($ag_hexa, $ag_key)
    {
        $x_counter  = 0;
        $result     = 0;
        $hasil_asci = '';
        $x_lopp     = strlen($ag_hexa) / 2;
        for ($x = 0; $x < $x_lopp; $x++) {
            $length = 1; // len(ag_hexa)
            $ls_hex = substr(strtoupper($ag_hexa), $x_counter, 2);
            for ($i = 0; $i < 2; $i++) {
                $result += (strrpos('123456789ABCDEF', substr($ls_hex, $i, 1)) + 1) * (pow(16, ($length - $i)));
                if (substr($ls_hex, $i, 1) == '0') {
                    $result -= 1;
                }
            }
            $x_counter  = $x_counter + 2;
            $hasil_asci = $hasil_asci . chr(($result - $ag_key));
            $result     = 0;
        }
        return $hasil_asci;
    }

    public function f_encrypt_hexa($ags_word, $ags_key, $gs_company)
    {
        $pwd_acak = '';
        $count    = strlen($ags_word);
        for ($i = 0; $i < $count; $i++) {
            $pwd_acak = $pwd_acak . substr($ags_word, $i, 1) . $this->f_acak_pwd($ags_word, $i, $gs_company);
        }
        //echo "acak2 ".$pwd_acak;
        $ls_return = $this->f_asci_hexa_pwd($pwd_acak, $ags_key);
        return $ls_return;
    }

    public function f_decrypt_hexa($ag_hexa, $ags_key)
    {
        $pwd_asli  = '';
        $x_counter = 0;
        $x_lopp    = strlen($ag_hexa) / 4;
        for ($i = 0; $i < $x_lopp; $i++) {
            $pwd_asli  = $pwd_asli . substr($ag_hexa, $x_counter, 2);
            $x_counter = $x_counter + 4;
        }
        return $pwd_asli;
    }
}
