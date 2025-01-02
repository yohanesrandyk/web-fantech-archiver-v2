<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mod_dashboard extends CI_Model
{
    public function get_retslno($view, $glno)
    {
        echo "<script>console.log('get_retslno')</script>";
        return $this->db->query("SELECT * FROM RETSLNO WHERE VIEW_DASHBOARD LIKE '$view' AND GLNO LIKE '$glno' ORDER BY SLNO")->result_array();
    }

    public function get_kdslno($kdslno)
    {
        echo "<script>console.log('get_kdslno')</script>";
        return $this->db->query("SELECT * FROM RETSLNO WHERE KDSLNO = '$kdslno'")->result_array()[0];
    }

    public function get_slno()
    {
        echo "<script>console.log('get_slno')</script>";
        return $this->db->query("SELECT SLNO, GLNO, DESCRIPTION FROM SLCHART WHERE SLNO NOT IN (SELECT SLNO FROM RETSLNO WHERE SLNO IS NOT NULL) GROUP BY SLNO, GLNO, DESCRIPTION ORDER BY SLNO")->result_array();
    }

    public function get_asset($ccy, $kdarea, $rptcode, $slno, $beginbal, $mutdb, $mutcr)
    {
        echo "<script>console.log('get_asset')</script>";
        return $this->db->query("SELECT SLNO, DESCRIPTION, GLNO, RPTCODE, FLAGSL, SLDAKHIR, 
        
        BUDGET01, 
        BUDGET02, 
        BUDGET03, 
        BUDGET04, 
        BUDGET05, 
        BUDGET06, 
        BUDGET07, 
        BUDGET08, 
        BUDGET09, 
        BUDGET10, 
        BUDGET11, 
        BUDGET12, 
        
        CASE BUDGET01 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET01 * 100 END AS BUDGET_PERSEN01,
        CASE BUDGET02 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET02 * 100 END AS BUDGET_PERSEN02,
        CASE BUDGET03 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET03 * 100 END AS BUDGET_PERSEN03,
        CASE BUDGET04 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET04 * 100 END AS BUDGET_PERSEN04,
        CASE BUDGET05 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET05 * 100 END AS BUDGET_PERSEN05,
        CASE BUDGET06 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET06 * 100 END AS BUDGET_PERSEN06,
        CASE BUDGET07 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET07 * 100 END AS BUDGET_PERSEN07,
        CASE BUDGET08 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET08 * 100 END AS BUDGET_PERSEN08,
        CASE BUDGET09 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET09 * 100 END AS BUDGET_PERSEN09,
        CASE BUDGET10 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET10 * 100 END AS BUDGET_PERSEN010,
        CASE BUDGET11 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET11 * 100 END AS BUDGET_PERSEN011,
        CASE BUDGET12 WHEN 0 THEN 0 ELSE SLDAKHIR / BUDGET12 * 100 END AS BUDGET_PERSEN012
        
        FROM (SELECT SLNO, DESCRIPTION, GLNO, RPTCODE, FLAGSL, SUM(SLDAKHIR) AS SLDAKHIR, 
        
        SUM(BLN1) AS BUDGET01, 
        SUM(BLN2) AS BUDGET02, 
        SUM(BLN3) AS BUDGET03, 
        SUM(BLN4) AS BUDGET04, 
        SUM(BLN5) AS BUDGET05, 
        SUM(BLN6) AS BUDGET06, 
        SUM(BLN7) AS BUDGET07, 
        SUM(BLN8) AS BUDGET08, 
        SUM(BLN9) AS BUDGET09, 
        SUM(BLN10) AS BUDGET10, 
        SUM(BLN11) AS BUDGET11, 
        SUM(BLN12) AS BUDGET12 
        
        FROM (SELECT S.KDCAB, S.CCY, S.DESCRIPTION, S.SLNO, S.GLNO, T.RPTCODE, S.FLAGSL, 
        
        CASE S.CCY
             WHEN 'IDR'
             THEN
                CASE S.normalbalance
                   WHEN 'D' THEN S.beginbal + (S.mutdb - S.mutcr)
                   WHEN 'C' THEN S.beginbal + (S.mutcr - S.mutdb)
                END
             ELSE
                CASE S.normalbalance
                   WHEN 'D' THEN S.sldorgawal + (S.mutdborg - S.mutcrorg)
                   WHEN 'C' THEN S.sldorgawal + (S.mutcrorg - S.mutdborg)
                END
          END
             AS SLDAKHIR, 
        
        NVL(B.BLN1, 0) BLN1,
        NVL(B.BLN2, 0) BLN2,
        NVL(B.BLN3, 0) BLN3,
        NVL(B.BLN4, 0) BLN4,
        NVL(B.BLN5, 0) BLN5,
        NVL(B.BLN6, 0) BLN6,
        NVL(B.BLN7, 0) BLN7,
        NVL(B.BLN8, 0) BLN8,
        NVL(B.BLN9, 0) BLN9,
        NVL(B.BLN10, 0) BLN10,
        NVL(B.BLN11, 0) BLN11,
        NVL(B.BLN12, 0) BLN12
        
        FROM SLCHART S INNER JOIN TBLITMRPT T ON T.SLNO = S.SLNO LEFT OUTER JOIN TBLCHART_TB B ON B.SLNO = S.SLNO AND B.CCY = S.CCY AND B.KDCAB = S.KDCAB WHERE S.SLNO = '$slno' AND S.CCY LIKE '$ccy' AND S.KDCAB IN (SELECT KDCAB FROM TBLAREA WHERE KDAREA = '$kdarea') AND T.RPTCODE IN($rptcode)) GROUP BY SLNO, DESCRIPTION, GLNO, RPTCODE, FLAGSL)")->row_array();
    }

    public function get_growth($ccy, $kdarea, $flagsl, $rptcode, $glno)
    {
        echo "<script>console.log('get_growth')</script>";
        $growth = $this->get_growth_months($ccy, $kdarea, $flagsl, $rptcode, $glno);
        $budget = $this->get_budget($ccy, $kdarea, $flagsl, $rptcode, $glno);

        $growth_data_real = [];
        $budget_data = [];
        $growth_labels = [];
        $growth_data = [];
        foreach ($growth->result_array() as $row) {
            $label = $row['PERIODE'];

            array_push($growth_labels, $label);
            array_push($growth_data, round($row['SLDAKHIR'] / 1000000000, 2));
            if (!empty($budget)) {
                array_push($budget_data, round($budget[0]['BLN' . $row['MONTH']] / 1000000000, 2));
            } else {
                array_push($budget_data, 0);
            }


            array_push($growth_data_real, $row['SLDAKHIR']);
        }

        $data = [];
        $data['SLDAKHIR_SBL'] = count($growth_data_real) >= 2 ? $growth_data_real[count($growth_data_real) - 2] : 0;

        $data['chart']['labels'] = json_encode(array_slice($growth_labels, -12));
        $data['chart']['series'][0]['name'] = "Asset";
        $data['chart']['series'][0]['data'] = json_encode(array_slice($growth_data, -12));
        $data['chart']['series'][1]['name'] = "Bugdet";
        $data['chart']['series'][1]['data'] = json_encode(array_slice($budget_data, -12));

        return $data;
    }

    public function get_budget($ccy, $kdarea, $flagsl, $rptcode, $slno)
    {
        echo "<script>console.log('get_budget')</script>";
        return $this->db->query("SELECT SLNO, SUM(BLN1) AS BLN1, SUM(BLN2) AS BLN2, SUM(BLN3) AS BLN3, SUM(BLN4) AS BLN4, SUM(BLN5) AS BLN5, SUM(BLN6) AS BLN6, SUM(BLN7) AS BLN7, SUM(BLN8) AS BLN8, SUM(BLN9) AS BLN9, SUM(BLN10) AS BLN10, SUM(BLN11) AS BLN11, SUM(BLN12) AS BLN12
        FROM TBLCHART_TB C WHERE C.SLNO IN 
        (SELECT T.SLNO FROM TBLITMRPT T WHERE T.RPTCODE IN ($rptcode)) 
        AND C.FLAGSL IN ($flagsl) AND C.CCY LIKE '$ccy'
        AND C.KDCAB IN (SELECT KDCAB FROM TBLAREA WHERE KDAREA = '$kdarea')
        AND C.SLNO = '$slno'
        GROUP BY C.SLNO ORDER BY SLNO ASC")->result_array();
    }

    public function get_growth_months($ccy, $kdarea, $flagsl, $rptcode, $slno)
    {
        echo "<script>console.log('get_growth_months')</script>";
        return $this->db->query("SELECT * FROM (
        SELECT  
        Y.MONTH,
        TO_CHAR(TO_DATE(Y.MONTH, 'MM'), 'FmMon') 
        || ' ' || Y.YEAR
        AS PERIODE,
        SUM(A.SLDAKHIR) AS SLDAKHIR FROM GLRHISSL A 
        INNER JOIN (SELECT X.SLNO, X.KDCAB, X.CCY, MAX(X.DAY) AS DAY, X.MONTH, X.YEAR FROM (
                
                SELECT G.SLNO, G.KDCAB, G.CCY, EXTRACT(DAY FROM G.TGLTRN) AS DAY, EXTRACT(MONTH FROM G.TGLTRN) AS MONTH, EXTRACT(YEAR FROM G.TGLTRN) AS YEAR FROM GLRHISSL G
                WHERE G.CCY LIKE '$ccy'
                AND G.SLNO = '$slno'
                AND G.KDCAB IN (SELECT KDCAB FROM TBLAREA WHERE KDAREA = '$kdarea')
                
                ) X GROUP BY X.SLNO, X.KDCAB, X.CCY, X.MONTH, X.YEAR) Y 
        ON Y.SLNO = A.SLNO
        AND Y.KDCAB = A.KDCAB 
        AND Y.CCY = A.CCY
        AND TO_CHAR(TO_DATE(Y.DAY || '/' || Y.MONTH || '/' || Y.YEAR, 'DD/MM/YYYY'), 'DD/MM/YYYY') = TO_CHAR(A.TGLTRN, 'DD/MM/YYYY') 
        WHERE 
        A.CCY LIKE '$ccy'
        AND A.SLNO = '$slno'
        AND A.KDCAB IN (SELECT KDCAB FROM TBLAREA WHERE KDAREA = '$kdarea')
        AND EXTRACT(YEAR FROM A.TGLTRN) >= (SELECT EXTRACT(YEAR FROM EOD) FROM TBLEOD WHERE PRSID = 'GLR') - 1
        GROUP BY Y.DAY, Y.MONTH, Y.YEAR
        ORDER BY Y.YEAR, Y.MONTH)
        WHERE SLDAKHIR <> 0");
    }

    public function get_growth_months_old($ccy, $kdarea, $flagsl, $rptcode, $glno)
    {
        echo "<script>console.log('get_growth_months_old')</script>";
        return $this->db->query("SELECT * FROM (
        SELECT Z.FLAGSL, 
        
        Y.MONTH,
        CASE Y.MONTH WHEN 12 THEN
        'Jan ' || TO_CHAR(Y.YEAR + 1)
        ELSE
        TO_CHAR(TO_DATE(Y.MONTH + 1, 'MM'), 'FmMon') 
        || ' ' || Y.YEAR
        END AS PERIODE,
        
        SUM(A.SLDAKHIR) AS SLDAKHIR FROM GLRHISSL A 
        INNER JOIN (SELECT X.SLNO, X.KDCAB, X.CCY, MAX(X.DAY) AS DAY, X.MONTH, X.YEAR FROM (SELECT G.SLNO, G.KDCAB, G.CCY, EXTRACT(DAY FROM G.TGLTRN) AS DAY, EXTRACT(MONTH FROM G.TGLTRN) AS MONTH, EXTRACT(YEAR FROM G.TGLTRN) AS YEAR FROM GLRHISSL G) X GROUP BY X.SLNO, X.KDCAB, X.CCY, X.MONTH, X.YEAR) Y 
        ON A.SLNO = Y.SLNO AND A.KDCAB = Y.KDCAB AND A.CCY = Y.CCY AND TO_CHAR(A.TGLTRN, 'DD/MM/YYYY') =  TO_CHAR(TO_DATE(Y.DAY || '/' || Y.MONTH || '/' || Y.YEAR, 'DD/MM/YYYY'), 'DD/MM/YYYY')
        INNER JOIN (SELECT S.SLNO, S.FLAGSL FROM SLCHART S WHERE S.SLNO IN (SELECT T.SLNO FROM TBLITMRPT T WHERE T.RPTCODE IN ($rptcode)) AND S.FLAGSL IN ($flagsl) AND S.GLNO IN ($glno) GROUP BY S.SLNO, S.FLAGSL) Z ON Y.SLNO = Z.SLNO
        WHERE A.CCY LIKE '$ccy'
        AND A.KDCAB IN (SELECT KDCAB FROM TBLAREA WHERE KDAREA = '$kdarea')
        AND EXTRACT(YEAR FROM A.TGLTRN) = (SELECT EXTRACT(YEAR FROM EOD) FROM TBLEOD WHERE PRSID = 'GLR')
        AND EXTRACT(MONTH FROM A.TGLTRN) != (SELECT EXTRACT(MONTH FROM EOD) FROM TBLEOD WHERE PRSID = 'GLR')
        GROUP BY Z.FLAGSL, Y.DAY, Y.MONTH, Y.YEAR
        ORDER BY Y.YEAR, Y.MONTH)
        WHERE SLDAKHIR <> 0");
    }

    public function get_rasiobank($modul, $kdcab, $periode)
    {
        echo "<script>console.log('get_rasiobank')</script>";
        return $this->db->query("SELECT X.KDCAB, X.MODUL, X.PERIODE, TO_CHAR(X.PERIODE, 'DD/MM/YYYY') AS PERIODE_2, X.NILAI
        FROM (
        SELECT T.KDCAB, T.NILAI, T.PERIODE, (CASE
        WHEN T.MODUL = 'KPMM' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'CAR'
        WHEN T.MODUL = 'ROE' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'ROE'
        WHEN T.MODUL = 'LDR' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'LDR'
        WHEN T.MODUL = 'BOPO' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'BOPO'
        WHEN T.MODUL = 'ROA' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'ROA'
        WHEN T.MODUL = 'CRS' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'CRS'
        WHEN T.MODUL = 'NPL' AND T.MODUL_ID = 'RNPLB' AND T.ITMCODE = 'RNPLB' THEN 'NPL BRUTO'
        WHEN T.MODUL = 'NPL' AND T.MODUL_ID = 'RNPLN' AND T.ITMCODE = 'RNPLN' THEN 'NPL NETTO'
        WHEN T.MODUL = 'NPL' AND T.MODUL_ID = 'RNPL' AND T.ITMCODE = 'RNPL' THEN 'NPL'
        WHEN T.MODUL = 'KAP' AND T.MODUL_ID = 'RAP' AND T.ITMCODE = 'RAP' THEN 'KAP'
        WHEN T.MODUL = 'KAP' AND T.MODUL_ID = 'RPPAP' AND T.ITMCODE = 'RPPAP' THEN 'RPPAP'
        WHEN T.MODUL = 'ATMR' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'ATMR'
        WHEN T.MODUL = 'KPMM' AND T.MODUL_ID = 'NKPMM' AND T.ITMCODE = 'NKPMM' THEN 'KPMM NILAI MODAL' 
        ELSE '??? ' || T.MODUL || ' ' || T.MODUL_ID || ' ' || T.ITMCODE || ' ???'
        END) AS MODUL
        FROM TBLTKS T 
        INNER JOIN (SELECT KDCAB, MODUL, MODUL_ID, ITMCODE, MAX(PERIODE) AS PERIODE 
                        FROM TBLTKS 
                        WHERE PERIODE <= TO_DATE('$periode', 'YYYY-MM-DD')
                        GROUP BY KDCAB, MODUL, MODUL_ID, ITMCODE) J
        ON T.KDCAB = J.KDCAB 
        AND T.MODUL = J.MODUL 
        AND T.MODUL_ID = J.MODUL_ID 
        AND T.ITMCODE = J.ITMCODE 
        AND T.PERIODE = J.PERIODE
        WHERE T.NILAI < 200) X
        WHERE 
        LENGTH(X.MODUL) <= 15
        AND REPLACE(X.MODUL, ' ', '_') LIKE '$modul' 
        AND X.KDCAB LIKE '$kdcab'
        ORDER BY NILAI DESC")->result_array();
    }

    public function get_rasiobank_growth($row, $periode)
    {
        echo "<script>console.log('get_rasiobank_growth')</script>";
        $growth = $this->get_rasiobank_growth2($row['MODUL'], $row['KDCAB'], $periode);

        $growth_data = [];
        $growth_labels = [];

        foreach ($growth as $row2) {
            array_push($growth_data, $row2['NILAI']);
            array_push($growth_labels, $row2['PERIODE']);
        }

        if (count($growth_data) < 2) {
            $growth_data = array_merge([0], $growth_data);
            $growth_labels = array_merge([date("M Y", strtotime("-1 months", strtotime($growth_labels[0])))], $growth_labels);
        }

        $data['chart']['labels'] = json_encode($growth_labels);
        $data['chart']['series'][0]['data'] = json_encode($growth_data);
        $data['NILAI_SBL'] = count($growth_data) > 1 ? $growth_data[count($growth_data) - 2] : 0;

        return $data;
    }

    public function get_rasiobank_growth2($modul, $kdcab, $periode)
    {
        echo "<script>console.log('get_rasiobank_growth2')</script>";
        return $this->db->query("SELECT X.KDCAB, X.NILAI, X.MODUL,
            TO_CHAR(TO_DATE(EXTRACT(MONTH FROM X.PERIODE), 'MM'), 'FmMon') 
            || ' ' || EXTRACT(YEAR FROM X.PERIODE)
            AS PERIODE
            FROM (SELECT T.KDCAB, T.NILAI, T.PERIODE, (CASE
            WHEN T.MODUL = 'KPMM' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'CAR'
            WHEN T.MODUL = 'ROE' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'ROE'
            WHEN T.MODUL = 'LDR' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'LDR'
            WHEN T.MODUL = 'BOPO' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'BOPO'
            WHEN T.MODUL = 'ROA' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'ROA'
            WHEN T.MODUL = 'CRS' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'CRS'
            WHEN T.MODUL = 'NPL' AND T.MODUL_ID = 'RNPLB' AND T.ITMCODE = 'RNPLB' THEN 'NPL BRUTO'
            WHEN T.MODUL = 'NPL' AND T.MODUL_ID = 'RNPLN' AND T.ITMCODE = 'RNPLN' THEN 'NPL NETTO'
            WHEN T.MODUL = 'NPL' AND T.MODUL_ID = 'RNPL' AND T.ITMCODE = 'RNPL' THEN 'NPL'
            WHEN T.MODUL = 'KAP' AND T.MODUL_ID = 'RAP' AND T.ITMCODE = 'RAP' THEN 'KAP'
            WHEN T.MODUL = 'KAP' AND T.MODUL_ID = 'RPPAP' AND T.ITMCODE = 'RPPAP' THEN 'RPPAP'
            WHEN T.MODUL = 'ATMR' AND T.MODUL_ID = 'RESULT' AND T.ITMCODE = 'RESULT' THEN 'ATMR'
            WHEN T.MODUL = 'KPMM' AND T.MODUL_ID = 'NKPMM' AND T.ITMCODE = 'NKPMM' THEN 'KPMM NILAI MODAL' 
            ELSE T.MODUL || ' ' || T.MODUL_ID || ' ' || T.ITMCODE
            END) AS MODUL
            FROM TBLTKS T) X 
            WHERE X.MODUL LIKE '$modul' AND X.KDCAB LIKE '$kdcab' AND X.NILAI < 200
            AND X.PERIODE <= TO_DATE('$periode', 'YYYY-MM-DD')
            ")->result_array();
    }

    public function delete_retslno($slno)
    {
        echo "<script>console.log('delete_retslno')</script>";
        $this->db->delete('RETSLNO', array('SLNO' => $slno));
    }

    public function insert_retslno($data)
    {
        echo "<script>console.log('insert_retslno')</script>";
        $this->db->insert_batch('RETSLNO', $data);
    }

    public function update_retslno($slno, $data)
    {
        echo "<script>console.log('update_retslno')</script>";
        $this->db->set($data);
        $this->db->where('SLNO', $slno);
        $this->db->update('RETSLNO');
    }

    public function get_dropping($kdcab, $ccy, $his)
    {
        if ($kdcab == '000') $kdcab = '%';
        echo "<script>console.log('get_dropping')</script>";
        if ($his == 'true') {
            return $this->db->query("SELECT COUNT(X.NOFAS) AS NOA, PRODUK.JNSKRD, 'X' AS KDF01, SUM(X.NLTARIK) AS NILAI  
        FROM (
            SELECT NOFAS, NLTARIK, TGLTRN FROM KRD400HIS
            UNION ALL
            SELECT NOFAS, NLTARIK
            , TGLTRN 
            FROM KRD400
            )  X 
        INNER JOIN KRDFAS ON X.NOFAS = KRDFAS.NOFAS 
        INNER JOIN PRODUK ON KRDFAS.JENIS = PRODUK.KD_PRODUK 
        WHERE X.NLTARIK > 0  AND KRDFAS.KDCAB LIKE '$kdcab' AND KRDFAS.CCY LIKE '$ccy'
        GROUP BY PRODUK.JNSKRD")->result_array();
        } else {
            return $this->db->query("SELECT COUNT(X.NOFAS) AS NOA, PRODUK.JNSKRD, 'X' AS KDF01, SUM(X.NLTARIK) AS NILAI  
        FROM (
            SELECT NOFAS, NLTARIK
            FROM KRD400
            )  X 
        INNER JOIN KRDFAS ON X.NOFAS = KRDFAS.NOFAS 
        INNER JOIN PRODUK ON KRDFAS.JENIS = PRODUK.KD_PRODUK 
        WHERE X.NLTARIK > 0  AND KRDFAS.KDCAB LIKE '$kdcab' AND KRDFAS.CCY LIKE '$ccy'
        GROUP BY PRODUK.JNSKRD")->result_array();
        }
    }

    public function get_dropping_growth($jenis, $kdf01, $kdcab)
    {
        echo "<script>console.log('get_dropping_growth')</script>";
        if ($kdf01 != 'X') {
            $growth = $this->get_dropping_growth3($jenis, $kdf01, $kdcab);
        } else {
            $growth = $this->get_dropping_growth2($jenis, $kdcab);
        }

        $growth_data = [];
        $growth_labels = [];

        foreach ($growth as $row) {
            array_push($growth_data, round($row['NILAI'] / 1000000000, 2));
            array_push($growth_labels, $row['PERIODE']);
        }

        if (count($growth_data) < 2 && count($growth_labels) > 0) {
            $growth_data = array_merge([0], $growth_data);
            $growth_labels = array_merge([date("M Y", strtotime("-1 months", strtotime($growth_labels[0])))], $growth_labels);
        }

        $data['chart']['labels'] = json_encode(array_slice($growth_labels, -12));
        $data['chart']['series'][0]['data'] = json_encode(array_slice($growth_data, -12));
        $data['NILAI'] = count($growth_data) > 0 ? $growth_data[count($growth_data) - 1] : 0;
        $data['NILAI_SBL'] = count($growth_data) > 1 ? $growth_data[count($growth_data) - 2] : 0;

        return $data;
    }

    public function get_dropping_growth2($jenis, $kdcab)
    {
        if ($kdcab == '000') $kdcab = '%';
        echo "<script>console.log('get_dropping_growth2')</script>";
        return $this->db->query("SELECT NOA, NILAI, JNSKRD, TO_CHAR(TO_DATE(BULAN, 'MM'), 'Mon') || ' ' || TAHUN AS PERIODE FROM(
            SELECT COUNT(X.NOFAS) AS NOA, PRODUK.JNSKRD, SUM(X.NLTARIK) AS NILAI, EXTRACT(YEAR FROM X.TGLTRN) AS TAHUN, EXTRACT(MONTH FROM X.TGLTRN) AS BULAN
            FROM (
            SELECT NOFAS, NLTARIK, TGLTRN FROM KRD400HIS
            UNION ALL
            SELECT NOFAS, NLTARIK, TGLTRN FROM KRD400
            )  X
            INNER JOIN KRDFAS ON X.NOFAS = KRDFAS.NOFAS 
            INNER JOIN PRODUK ON KRDFAS.JENIS = PRODUK.KD_PRODUK AND PRODUK.JNSKRD = '$jenis'
            WHERE X.NLTARIK > 0 AND KRDFAS.KDCAB LIKE '$kdcab'
            GROUP BY PRODUK.JNSKRD, EXTRACT(YEAR FROM X.TGLTRN), EXTRACT(MONTH FROM X.TGLTRN)
            )
            ORDER BY JNSKRD, TAHUN, BULAN")->result_array();
    }

    public function get_dropping_growth3($jenis, $kdf01, $kdcab)
    {
        if ($kdcab == '000') $kdcab = '%';
        echo "<script>console.log('get_dropping_growth3')</script>";
        return $this->db->query("SELECT NOA, NILAI, KDF01, TO_CHAR(TO_DATE(BULAN, 'MM'), 'Mon') || ' ' || TAHUN AS PERIODE FROM(
            SELECT COUNT(X.NOFAS) AS NOA, KRDFAS.KDF01, SUM(X.NLTARIK) AS NILAI, EXTRACT(YEAR FROM X.TGLTRN) AS TAHUN, EXTRACT(MONTH FROM X.TGLTRN) AS BULAN
            FROM (
            SELECT NOFAS, NLTARIK, TGLTRN FROM KRD400HIS
            UNION ALL
            SELECT NOFAS, NLTARIK, TGLTRN FROM KRD400
            )  X
            INNER JOIN KRDFAS ON X.NOFAS = KRDFAS.NOFAS 
            INNER JOIN PRODUK 
            ON KRDFAS.JENIS = PRODUK.KD_PRODUK 
            AND PRODUK.JNSKRD = '$jenis'
            AND NVL(KRDFAS.KDF01, ' ')  LIKE '$kdf01'
            WHERE X.NLTARIK > 0 AND KRDFAS.KDCAB LIKE '$kdcab'
            GROUP BY KRDFAS.KDF01, EXTRACT(YEAR FROM X.TGLTRN), EXTRACT(MONTH FROM X.TGLTRN)
            )
            ORDER BY KDF01, TAHUN, BULAN")->result_array();
    }

    // public function get_os_npl($kdcab)
    // {
    //     if ($kdcab == '000') $kdcab = '%';
    //     echo "<script>console.log('get_os_npl')</script>";
    //     return $this->db->query("SELECT KOLEKSKR, COUNT(NOFAS) AS NOA, SUM(SLDAKHIR) AS SLDAKHIR
    //     FROM (
    //     SELECT KRDFAS.NOFAS, DECODE(KRDFAS.KETPRD,'COVID19',MSTCST.KOLEKMANUAL,MSTCST.KOLEKSKR) KOLEKSKR, KRD100.SLDAKHIR 
    //     FROM KRDFAS 
    //     INNER JOIN MSTCST ON KRDFAS.CNO = MSTCST.CNO AND KRDFAS.ALTERNATE = MSTCST.ALTERNATE 
    //     INNER JOIN KRD100 ON KRDFAS.NOFAS = KRD100.NOFAS 
    //     WHERE 
    //     KRD100.STSKRD IN ('LN','OK', 'JT') AND 
    //     KRDFAS.KDCAB LIKE '$kdcab'
    //     )
    //     GROUP BY KOLEKSKR")->result_array();
    // }

    public function get_os_npl($kdcab, $tanggal, $ccy)
    {
        if ($kdcab == '000') $kdcab = '%';
        echo "<script>console.log('get_os_npl')</script>";
        return $this->db->query("SELECT KOLEKSKR, COUNT(NOFAS) AS NOA, SUM(SLDAKHIR) AS SLDAKHIR FROM (
            SELECT 
            KRD100HIS.KOLEKSKR, KRD100HIS.NOFAS, 
            DECODE(PRODUK.ST_KPR,2, CASE WHEN KRD100HIS.SLDAKHIR >= 0 THEN 0
            WHEN KRD100HIS.PLF_DEBITUR = 0 AND KRD100HIS.SLDAKHIR < 0 THEN ABS(KRD100HIS.SLDAKHIR) END, KRD100HIS.SLDAKHIR) SLDAKHIR
            
            FROM KRD100HIS  
            INNER JOIN PRODUK ON KRD100HIS.JENIS = PRODUK.KD_PRODUK
            WHERE 
            KRD100HIS.PLF_DEBITUR > 0 
            AND TRUNC(KRD100HIS.TGLTRN) = TRUNC(TO_DATE('$tanggal', 'YYYY-MM-DD'))
            AND KRD100HIS.KDCAB LIKE '$kdcab'AND KRD100HIS.CCY LIKE '$ccy'
            AND NVL(KRD100HIS.STS_JAMINAN,'OK') IN ('OK','JT','LC','TR','JL','LN') 
            AND KRD100HIS.STSKRD in ('OK','JT','LN','DL') AND (((abs(KRD100HIS.SLDAKHIR)+KRD100HIS.BDDIBYR+KRD100HIS.BCADANG+KRD100HIS.SISAAMOR+KRD100HIS.SISAAMOR_BIAYA+nvl(KRD100HIS.NLDENDA,0))  > 0) or produk.st_kpr <> '3')
        ) GROUP BY KOLEKSKR")->result_array();
    }

    // public function get_os_npl($kdcab)
    // {
    //     if ($kdcab == '000') $kdcab = '%';
    //     echo "<script>console.log('get_os_npl')</script>";
    //     return $this->db->query("SELECT * FROM( SELECT 
    //     -- MSTCST.KOLEKSKR, 
    //     DECODE(KRDFAS.KETPRD,'COVID19',MSTCST.KOLEKMANUAL,MSTCST.KOLEKSKR) KOLEKSKR,
    //     COUNT(KRDFAS.NOFAS) AS NOA, SUM(KRD100HIS.SLDAKHIR) AS SLDAKHIR
    //     FROM KRDFAS INNER JOIN MSTCST ON KRDFAS.CNO = MSTCST.CNO AND KRDFAS.ALTERNATE = MSTCST.ALTERNATE
    //     INNER JOIN KRD100HIS ON KRDFAS.NOFAS = KRD100HIS.NOFAS
    //     INNER JOIN KRDSPR ON KRD100HIS.NOFAS = KRDSPR.NOFAS AND KRD100HIS.NOSPK = KRDSPR.NOSPK
    //     INNER JOIN PRODUK ON KRDFAS.JENIS = PRODUK.KD_PRODUK
    //     INNER JOIN TBLKOLEK ON MSTCST.KOLEKSKR = TBLKOLEK.KOLEKTIBILITAS                      
    //     LEFT OUTER JOIN(SELECT NOFAS,  MAX(NVL(JENIS_AGUNAN,'%')) JENIS_AGUNAN, MAX(NVL(JNSIKATAN,'99')) JNSIKATAN, MAX(JENIS_CASH) JENIS_CASH, SUM(NILAI_PENGIKATAN) NILAI_PENGIKATAN,
    //           NVL(STS_JAMINAN,'OK') STS_JAMINAN, SUM(NILAI_AGUNAN_YG_DIPERHITUNGKAN) NILAI_AGUNAN_YG_DIPERHITUNGKAN, SUM(NILAICEV) NILAICEV,
    //           SUM(NILAI_INDEPENDEN) NILAI_INDEPENDEN, MAX(KETERANGAN_SANDI) KETERANGAN_SANDI, MAX(FLAG_PARIPASU) FLAG_PARIPASU, MAX(JENIS_AGUNAN_LBBPR) JENIS_AGUNAN_LBBPR
    //           FROM VIEW_KRD_PARIPASU 
    //           GROUP BY NOFAS, STS_JAMINAN) VIEW_KRD_PARIPASU ON KRDFAS.NOFAS = VIEW_KRD_PARIPASU.NOFAS 
    //     LEFT OUTER JOIN (SELECT NOFAS, NOSPK, SUM(NVL(KRDBIAYA.SISAAMOR,0)) SISAAMOR FROM KRDBIAYA INNER JOIN TBL_BIAYA ON KRDBIAYA.KDBIAYA = TBL_BIAYA.KD_BIAYA AND KRDBIAYA.TRNCODE = TBL_BIAYA.TCD 
    //         WHERE KRDBIAYA.SISAAMOR > 0 AND KRDBIAYA.AMOR = 'Y' AND TBL_BIAYA.BEBANBIAYA = '0' GROUP BY KRDBIAYA.NOFAS,KRDBIAYA.NOSPK) A ON KRD100HIS.NOFAS = A.NOFAS AND KRD100HIS.NOSPK = A.NOSPK
    //     LEFT OUTER JOIN VIEW_KRD300_SPK ON KRD100HIS.NOFAS = VIEW_KRD300_SPK.NOFAS AND KRD100HIS.NOSPK = VIEW_KRD300_SPK.NOSPK  
    //     LEFT OUTER JOIN (SELECT NOFAS, MAX(NAMA) NAMA FROM KRDASURANSI INNER JOIN TBLASURANSI ON KRDASURANSI.KODE_ASS = TBLASURANSI.KODE_ASS GROUP BY NOFAS) R ON KRDFAS.NOFAS = R.NOFAS
    //     WHERE KRD100HIS.PLF_DEBITUR > 0  
    //     AND KRDFAS.STATUS = '1'               
    //     AND KRDFAS.KDCAB LIKE '$kdcab' 
    //     AND NVL(VIEW_KRD_PARIPASU.STS_JAMINAN,'OK') IN ('OK','JT','LC','TR','JL','LN') 
    //     AND KRDFAS.STSKRD IN ('OK','LN','JT') 
    //     AND (((KRD100HIS.SLDAKHIR+KRD100HIS.SLDBNG+KRD100HIS.BDDIBYR+KRD100HIS.BCADANG+KRD100HIS.BNGJTHARI+NVL(A.SISAAMOR,0)+NVL(VIEW_KRD300_SPK.NLDENDA,0)) > 0) OR PRODUK.ST_KPR <> '3') 
    //     GROUP BY KOLEKSKR)
    //     ORDER BY KOLEKSKR")->result_array();
    // }

    public function get_retslno_cabang($kdslno, $kdcab)
    {
        echo "<script>console.log('get_retslno_cabang')</script>";
        return $this->db->query("SELECT RETSLNO.SLNO, CABANG.KD_CABANG, CABANG.NM_CABANG, RETSLNO.KETERANGAN FROM RETSLNO, CABANG WHERE RETSLNO.KDSLNO = '$kdslno' AND CABANG.KD_CABANG LIKE '$kdcab'")->result_array();
    }

    public function get_segmen($jnskrd, $kdcab, $ccy, $his)
    {
        if ($kdcab == '000') $kdcab = '%';
        echo "<script>console.log('get_segmen')</script>";
        if ($his == 'true') {
            return $this->db->query("SELECT COUNT(X.NOFAS) AS NOA, PRODUK.JNSKRD, NVL(KRDFAS.KDF01, '%') KDF01, SUM(X.NLTARIK) AS NILAI  
        , NVL(SANDI_BI.KETERANGAN, 'LAINNYA') AS SEGMEN FROM (
            SELECT NOFAS, NLTARIK, TGLTRN FROM KRD400HIS
            UNION ALL
            SELECT NOFAS, NLTARIK
            , TGLTRN 
            FROM KRD400
            )  X 
        INNER JOIN KRDFAS ON X.NOFAS = KRDFAS.NOFAS AND KRDFAS.KDCAB LIKE '$kdcab' AND KRDFAS.CCY LIKE '$ccy'
        INNER JOIN PRODUK ON KRDFAS.JENIS = PRODUK.KD_PRODUK 
        LEFT JOIN SANDI_BI ON SANDI_BI.KD_BI = 'F01' AND SANDI_BI.KD_SANDI = KRDFAS.KDF01 
        WHERE X.NLTARIK > 0 
        AND PRODUK.JNSKRD = '$jnskrd'
        GROUP BY PRODUK.JNSKRD, KRDFAS.KDF01, SANDI_BI.KETERANGAN")->result_array();
        } else {
            return $this->db->query("SELECT COUNT(X.NOFAS) AS NOA, PRODUK.JNSKRD, NVL(KRDFAS.KDF01, '%') KDF01, SUM(X.NLTARIK) AS NILAI  
        , NVL(SANDI_BI.KETERANGAN, 'LAINNYA') AS SEGMEN FROM (
            SELECT NOFAS, NLTARIK
            FROM KRD400
            )  X 
        INNER JOIN KRDFAS ON X.NOFAS = KRDFAS.NOFAS AND KRDFAS.KDCAB LIKE '$kdcab' AND KRDFAS.CCY LIKE '$ccy'
        INNER JOIN PRODUK ON KRDFAS.JENIS = PRODUK.KD_PRODUK 
        LEFT JOIN SANDI_BI ON SANDI_BI.KD_BI = 'F01' AND SANDI_BI.KD_SANDI = KRDFAS.KDF01 
        WHERE X.NLTARIK > 0 
        AND PRODUK.JNSKRD = '$jnskrd'
        GROUP BY PRODUK.JNSKRD, KRDFAS.KDF01, SANDI_BI.KETERANGAN")->result_array();
        }
    }

    public function get_rasio_budget($kdcab, $modul)
    {
        echo "<script>console.log('get_rasio_budget')</script>";
        return $this->db->query("SELECT SLNO, SUM(BLN1) AS BLN1, SUM(BLN2) AS BLN2, SUM(BLN3) AS BLN3, SUM(BLN4) AS BLN4, SUM(BLN5) AS BLN5, SUM(BLN6) AS BLN6, SUM(BLN7) AS BLN7, SUM(BLN8) AS BLN8, SUM(BLN9) AS BLN9, SUM(BLN10) AS BLN10, SUM(BLN11) AS BLN11, SUM(BLN12) AS BLN12
        FROM TBLCHART_TB C WHERE C.KDCAB IN (SELECT KDCAB FROM TBLAREA WHERE KDAREA = '$kdcab') AND C.SLNO = '$modul' GROUP BY C.SLNO ORDER BY SLNO ASC")->result_array();
    }
}
