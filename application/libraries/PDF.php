<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require 'fpdf/fpdf.php';
class PDF extends FPDF
{
    function __construct()
    {
        parent::FPDF();
    }

    // public function Header()
    // {
    //     $this->SetFont('Times', 'B', 12);
    //     $this->Cell(280, 0, 'LAPORAN FIXED ASSET', 0, 0, 'C');
    //     $this->Ln(1);
    // }

    // public function Footer()
    // {
    //     $this->SetY(-12);
    //     $lebar = $this->w;
    //     $this->SetFont('Times', 'I', 8);
    //     $this->line($this->GetX(), $this->GetY(), $this->GetX() + $lebar - 17, $this->GetY());
    //     $this->SetY(-20);
    //     $this->SetX(0);
    //     $this->Ln(1);
    //     $hal = 'Hal : ' . $this->PageNo() . '/{Laporan Fixed Asset}';
    //     $this->Cell($this->GetStringWidth($hal), 10, $hal);
    //     $datestring = "Year: %Y Month: %m Day: %d - %h:%i %a";
    //     $tanggal    = 'Dicetak : ' . date('d-m-Y  h:i-a') . ' ';
    //     $this->Cell($lebar - $this->GetStringWidth($hal) - $this->GetStringWidth($tanggal) - 20);
    //     $this->Cell($this->GetStringWidth($tanggal), 10, $tanggal);
    // }
	function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }
	function MultiCell2($w, $h, $txt, $border=0, $align='J', $fill=false, $maxline=0)
    {
        // Output text with automatic or explicit line breaks, at most $maxline lines
        if(!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw=$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',(string)$txt);
        $nb=strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $b=0;
        if($border)
        {
            if($border==1)
            {
                $border='LTRB';
                $b='LRT';
                $b2='LR';
            }
            else
            {
                $b2='';
                if(is_int(strpos($border,'L')))
                    $b2.='L';
                if(is_int(strpos($border,'R')))
                    $b2.='R';
                $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
            }
        }
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $ns=0;
        $nl=1;
        while($i<$nb)
        {
            // Get next character
            $c=$s[$i];
            if($c=="\n")
            {
                // Explicit line break
                if($this->ws>0)
                {
                    $this->ws=0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $ns=0;
                $nl++;
                if($border && $nl==2)
                    $b=$b2;
                if($maxline && $nl>$maxline)
                    return substr($s,$i);
                continue;
            }
            if($c==' ')
            {
                $sep=$i;
                $ls=$l;
                $ns++;
            }
            $l+=$cw[$c];
            if($l>$wmax)
            {
                // Automatic line break
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                    if($this->ws>0)
                    {
                        $this->ws=0;
                        $this->_out('0 Tw');
                    }
                    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                }
                else
                {
                    if($align=='J')
                    {
                        $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                        $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                    }
                    $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                    $i=$sep+1;
                }
                $sep=-1;
                $j=$i;
                $l=0;
                $ns=0;
                $nl++;
                if($border && $nl==2)
                    $b=$b2;
                if($maxline && $nl>$maxline)
                {
                    if($this->ws>0)
                    {
                        $this->ws=0;
                        $this->_out('0 Tw');
                    }
                    return substr($s,$i);
                }
            }
            else
                $i++;
        }
        // Last chunk
        if($this->ws>0)
        {
            $this->ws=0;
            $this->_out('0 Tw');
        }
        if($border && is_int(strpos($border,'B')))
            $b.='B';
        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
        $this->x=$this->lMargin;
        return '';
    }
}
