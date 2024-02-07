<?php
//call main fpdf file
require('./fpdf17/fpdf1.php');

//create new class extending fpdf class
class PDF_MC_Table extends FPDF {

// variable to store widths and aligns of cells, and line height
var $widths;
var $aligns;
var $lineHeight;
var $FillColor;  
var $TextColor;  
var $ColorFlag;  

//Set the array of column widths
function SetWidths($w){
    $this->widths=$w;
}


//Set the array of column alignments
function SetAligns($a){
    $this->aligns=$a;
}

//Set line height
function SetLineHeight($h){
    $this->lineHeight=$h;
}



//Calculate the height of the row
function Row($data)
{
    // number of line
    $nb=0;
	$this->SetFillColor(255, 255, 255);
	$this->SetTextColor(0,0,0);
	$this->SetDrawColor(0,0,0);
    // loop each data to find out greatest line number in a row.
    for($i=0;$i<count($data);$i++){
        // NbLines will calculate how many lines needed to display text wrapped in specified width.
        // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    }
    
    //multiply number of line with line height. This will be the height of current row
    $h=$this->lineHeight * $nb;

    //Issue a page break first if needed
    $this->CheckPageBreak($h);

    //Draw the cells of current row
    for($i=0;$i<count($data);$i++)
    {
        // width of the current col
        $w=$this->widths[$i];
        // alignment of the current col. if unset, make it left.
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],1,'C',$a,true);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}
function Footer()
{
$this->SetY(125);
$this->SetFont('Arial','B','8');
$this->cell(95,5,'www.medicoverhospitals.in',0,0,'L');
$this->Cell(95,5,'(Authorised Signatory)',0,1,'R');
$this->Cell(196,5,'', 'T',0,1);
$this->SetY(132);
$this->SetFont('Arial','','8');
$this->Cell(60,5,'',0,0);
$this->Cell(20,5,'Created By :',0,0);
$this->Cell(60,5,$GLOBALS['createdby'],0,0);
$this->Cell(20,5,'Created On :',0,0);
$this->Cell(40,5,$GLOBALS['createdon'],0,1);
$this->Cell(60,5,'',0,0);
$this->Cell(20,5,'Printed By :',0,0);
$this->Cell(60,5,$GLOBALS['printedby'],0,0);
$this->Cell(20,5,'Printed On :',0,0);
$this->Cell(40,5,$GLOBALS['printon'],0,1);
$this->SetFont('Arial','B','10');
$this->Image("https://www.cognex.com/api/Sitecore/Barcode/Get?data=".$GLOBALS['typefile']."&code=BCL_CODE128&width=250&imageType=PNG&foreColor=%23000000&backColor=%23FFFFFF&rotation=RotateNoneFlipNone", 1, 133, 70, 12, 'PNG');
$this->SetY(-9);
       
        $this->SetFont('Arial', 'I', 8);
       
        $this->Cell(0, 10, 'Page '.$this->PageNo(), 0, 0, 'C');

}
function NbLines($w,$txt)
{
    //calculate the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

}
?>