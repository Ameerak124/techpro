<?Php
include('mc-tablenewA4.php');
include("api/pdo-db.php");
$empid = $_GET['a'];
$format = $_GET['f'];
$billtype = $_GET['c'];
$searchterm = base64_decode($_GET['no']);


$pdf = new PDF_MC_Table();
$pdf->AddPage();
$data_att = array("accesskey" => $empid,"searchterm" => $searchterm,"billtype" => $billtype,"datetype" => $format);
$data_string_att = json_encode($data_att);
$ch_att = curl_init('http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/getpackage-bill-data-excl-final.php');                                                                      
curl_setopt($ch_att, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch_att, CURLOPT_POSTFIELDS, $data_string_att);                                                                  
curl_setopt($ch_att, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch_att, CURLOPT_HTTPHEADER, array(                                                                          
	'Content-Type: application/json',                                                                                
	'Content-Length: ' . strlen($data_string_att))                                                                       
);                                                          
$i = 0;
$result_att = curl_exec($ch_att);
$character_att = json_decode($result_att, true);

$hospitalisationchargesfrom = $character_att['hospitalisationchargesfrom'];

$billno = $character_att['details'][$i]['billno'];
$billdate = $character_att['details'][$i]['billdate'];
$patientname = $character_att['details'][$i]['patientname'];
$dateofadmission = $character_att['details'][$i]['dateofadmission'];
$consultant = $character_att['details'][$i]['consultant'];
$department = $character_att['details'][$i]['department'];
$sponsor_name = $character_att['details'][$i]['sponsor_name'];
$admissionno = $character_att['details'][$i]['admissionno'];
$dischargedate = $character_att['details'][$i]['dischargedate'];
$s_w_d_b_o = $character_att['details'][$i]['s_w_d_b_o'];
$patmobile = $character_att['details'][$i]['patmobile'];
$umrno = $character_att['details'][$i]['umrno'];
$agesex = $character_att['details'][$i]['agesex'];
$surgery = $character_att['details'][$i]['surgery'];
$referral = $character_att['details'][$i]['referral'];
$admittedward = $character_att['details'][$i]['admittedward'];
$tpa = $character_att['details'][$i]['tpa'];
$sponsor_category = $character_att['details'][$i]['sponsor_category'];

$printon = $character_att['printedon'];
$createdby = $character_att['preparedby'];
$printedby = $character_att['printedby'];
$createdon = $character_att['preparedon'];
$GLOBALS['printon'] = $printon;
$GLOBALS['createdon'] = $createdon;
$GLOBALS['printedby'] = $printedby;
$GLOBALS['createdby'] = $createdby;
$GLOBALS['typefile'] = $billno;
$viewbranch = $character_att['viewbranch'];
$address = $character_att['address'];
$unitaddress = $character_att['unitaddress'];

$city = $character_att['city'];
$gstno = $character_att['gstno'];
$contactno = $character_att['contactno'];
$pdf->SetFont('Arial','B','12');
$pdf->cell(195,6,$viewbranch,0,1,'C');
$pdf->SetFont('Arial','','9');
$pdf->cell(30,4,'',0,0);
$pdf->MultiCell(140,4,$unitaddress,0,'C');
$pdf->cell(30,1,'',0,1);
$pdf->cell(195,5,$contactno,0,1,'C');

$pdf->cell(195,5,$gstno,0,1,'C');

$pdf->cell(120,5,'',0,1);
$pdf->SetFont('Arial','BU','12');
//$pdf->cell(70);
if ($billtype == 'summary') {
	$pdf->cell(190,10,'Summarized Bill Of Supply  ',0,0,'C');
} elseif($billtype == 'final') {
	$pdf->cell(190,10,'IP Package Detailed Bill',0,1,'C');
	$pdf->cell(190,10,'Bill Of Supply',0,0,'C');
}else{
	$pdf->cell(190,10,'Semi-Summarized Bill Of Supply',0,0,'C');
}



$pdf->SetFont('Arial','B','9');
$pdf->cell(120,15,'',0,1);
$pdf->cell(28,5,'Bill No',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$billno,0,0);

$pdf->SetFont('Arial','B','9');
$pdf->cell(60,5,'',0,0);
$pdf->cell(28,5,'Admission No',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$admissionno,0,0);

$pdf->SetFont('Arial','B','9');
$pdf->cell(120,5,'',0,1);
$pdf->cell(28,5,'Bill Date',0,0);
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$billdate,0,0);
$pdf->SetFont('Arial','','9');

$pdf->SetFont('Arial','B','9');
$pdf->cell(60,5,'',0,0);
$pdf->cell(28,5,'Discharge Date',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$dischargedate,0,1);

$addmulti = $pdf -> GetY();
$pdf->SetFont('Arial','B','9');


$pdf->cell(28,5,'Patient Name',0,0);
$pdf->SetFont('Arial','B','9');
$pdf->cell(6,5,':',0,0);

$pdf->MultiCell(60,5,$patientname,0);
$patmulti = $pdf -> GetY();
$pdf -> SetXY(105,$addmulti);

$pdf->SetFont('Arial','','9');
$pdf->cell(9,5,'',0,0);
$pdf->cell(28,5,'Contact',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$patmobile,0,0);

$pdf->SetFont('Arial','B','9');
$pdf->cell(120,5,'',0,1);
$pdf -> SetY($patmulti);
$pdf->cell(28,5,'Date Of Admission',0,0);
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$dateofadmission,0,0);
$pdf->SetFont('Arial','','9');

$pdf->SetFont('Arial','','9');
$pdf->cell(60,5,'',0,0);
$pdf->cell(28,5,'Age / Sex',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$agesex,0,1);

$pdf->SetFont('Arial','B','9');
// $pdf->cell(120,5,'',0,1);
$pdf->cell(28,5,'Consultant',0,0);
$pdf->SetFont('Arial','B','9');
$pdf->cell(6,5,':',0,0);
$connext = $pdf->GetY();
$pdf->MultiCell(55,4,$consultant,0);
$condep = $pdf->GetY();
$pdf->SetXY(114,$connext);

$pdf->SetFont('Arial','','9');
$pdf->cell(28,5,'UMR No',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(55,5,$umrno ,0,1);
$pdf->SetY($condep);
$pdf->SetFont('Arial','','9');
// $pdf->cell(120,5,'',0,1);
$pdf->cell(28,5,'Department',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$department,0,0);

$pdf->SetFont('Arial','','9');
$pdf->cell(60,5,'',0,0);
$pdf->cell(28,5,'Referral',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$referral,0,1);

$pdf->SetFont('Arial','','9');
// $pdf->cell(120,5,'',0,1);
$pdf->cell(28,5,'Admitted Ward',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$admittedward,0,0);


$pdf->SetFont('Arial','','9');
$pdf->cell(60,5,'',0,0);
$pdf->cell(28,5,'Organisation',0,0);
$pdf->SetFont('Arial','','8');
$pdf->cell(6,5,':',0,0);
$pdf->MultiCell(50,5,$sponsor_name,0,1);
$pdf->SetFont('Arial','','9');
$y = $pdf->GetY();
if($sponsor_category == 'INSURANCE'){
	
$pdf->SetY($y);
$pdf->SetX(114);
$pdf->cell(28,5,'TPA',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(10,5,$tpa ,0,1);
}

$pdf->SetY($y);
$pdf->cell(28,5,'Address',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);

$pdf->MultiCell(55,4,$address,0);
$head = $pdf->GetY();





$pdf -> setY($head);
$pdf->SetFont('Arial','','9');
$pdf->cell(28,5,'Procedure Name',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(6,5,':',0,0);
$pdf->cell(100,5,$surgery ,0,1);


$pdf->cell(120,5,'',0,1);


// $pdf->cell(120,5,'',0,1);
$pdf->SetFont('Arial','B','11');
$pdf->cell(195,8,$hospitalisationchargesfrom,1,0,'C',0,1);
$pdf->cell(195,12,'',0,1);
$l1h = $pdf -> GetY();

$pdf -> Line(10,$l1h+2,205,$l1h+2);
$pdf->SetFont('Arial','','12');
$pdf->SetTextColor(74, 26, 255);
;$pdf -> setY($l1h+5);
$pdf -> cell(195,1,'Package Details',0,1);
$pdf -> Line(10,$l1h+8,205,$l1h+8);
$pdf -> Line(10,$l1h+14,205,$l1h+14);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B','7');
$pdf -> setY($l1h+9);

if($format == 'WithoutDate'){
	if($billtype == 'summary' || $billtype == 'semi'){
		//$pdf->cell(10,5,'S.No',0,0,'C');
		$pdf->cell(170,5,'Service Type',0,0,'L');
		$pdf->cell(25,5,'Amount',0,1,'R');
	}
	else{
		
//	$pdf->cell(10,5,'',0,0,'R');
	$pdf->cell(20,5,'Service Code',0,0,'L');
	$pdf->cell(55,5,'Services / Investigation',0,0,'L');
	$pdf->cell(15,5,'Rate',0,0,'R');
	$pdf->cell(15,5,'Dis(%)',0,0,'C');
	$pdf->cell(5,5,'Qty',0,0,'R');
	$pdf->cell(20,5,'Amount',0,1,'R');
}
}
else
{
	if($billtype == 'summary' || $billtype == 'semi'){
		//$pdf->cell(10,5,'S.No',0,0,'C');
		$pdf->cell(170,5,'Service Type',0,0,'L');
		$pdf->cell(25,5,'Amount',0,1,'R');
	}
	else{
	$pdf->cell(7,5,'Date',0,0,'R');
	$pdf->cell(20,5,'Service Code',0,0,'R');
	$pdf->cell(90,5,'Services / Investigation',0,0,'C');
	$pdf->cell(18,5,'Rate',0,0,'R');
	$pdf->cell(18,5,'Dis(%)',0,0,'R');
	$pdf->cell(18,5,'Qty',0,0,'R');
	$pdf->cell(18,5,'Amount',0,1,'R');
	}
}


$packagelist = $character_att['packagelist'];

	foreach($packagelist AS $list){
		if($format == 'WithoutDate'){
			//	$pdf->cell(10,5,"",0,0,'R');
				$pdf->cell(20,5,$list['servicecode'],0,0,'R');
				$pdf->cell(55,5,substr($list['services'],0,30),0,0,'C');
				$pdf->cell(10,5,$list['hsn_sac'],0,0,'C');
				$pdf->cell(15,5,$list['rate'],0,0,'R');
				$pdf->cell(15,5,$list['discount'],0,0,'C');
				$pdf->cell(5,5,$list['quantity'],0,0,'R');
				$pdf->cell(20,5,$list['total'],0,0,'R');
				$pdf->cell(10,5,$list['inclquantity'],0,0,'R');
				$pdf->cell(12,5,$list['inclamount'],0,0,'R');
				
				$pdf->cell(10,5,$list['exclquantity'],0,0,'R');
				$pdf->cell(12,5,$list['exclamount'],0,1,'R');
		
			}else{
				$pdf->cell(7,5,$list['createdon'],0,0,'C');
				$pdf->cell(20,5,$list['servicecode'],0,0,'C');
				$pdf->cell(90,5,substr($list['services'],0,30),0,0,'C');
				$pdf->cell(20,5,$list['rate'],0,0,'R');
				$pdf->cell(18,5,$list['discount'],0,0,'R');
				$pdf->cell(18,5,$list['quantity'],0,0,'R');
				$pdf->cell(18,5,$list['total'],0,1,'R');
			}
		
		$k++;
		if ($pdf->GetY() > 250) {
			$pdf->AddPage();
		} 
	}



$l2h = $pdf -> GetY();
$pdf -> setY($l2h+12);
//$pdf->cell(195,1,'','T',0,1);
$pdf->SetFont('Arial','B','7');
/* $pdf->cell(195,6,'',0,1);
$pdf->cell(195,0.5,'','T',0,1);
$pdf->cell(195,0.5,'',0,1);
$pdf->cell(195,0.5,'','T',0,1); */
$pdf->cell(10,2,'',0,1);

$pdf -> Line(10,$l2h+2,205,$l2h+2);
$pdf->SetFont('Arial','','12');
$pdf->SetTextColor(74, 26, 255);
$pdf -> setY($l2h+5);
$pdf -> cell(195,1,'Package Excludes',0,1);
$pdf -> Line(10,$l2h+8,205,$l2h+8);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial','B','7');
$pdf -> setY($l2h+9);

if($format == 'WithoutDate'){
	if($billtype == 'summary' || $billtype == 'semi'){
		//$pdf->cell(10,5,'S.No',0,0,'C');
		$pdf->cell(170,5,'Service Type',0,0,'L');
		$pdf->cell(25,5,'Amount',0,1,'R');
	}else{
		
//	$pdf->cell(10,5,'',0,0,'R');
	$pdf->cell(20,5,'Service Code',0,0,'L');
	$pdf->cell(55,5,'Services / Investigation',0,0,'L');
	$pdf->cell(10,5,'HSN/SAC',0,0,'C');
	$pdf->cell(5,5,'Inc Qty',0,0,'R');
	$pdf->cell(15,5,'Rate',0,0,'R');
	$pdf->cell(12,5,'In.Amt',0,1,'R');
}
}
else
{
	if($billtype == 'summary' || $billtype == 'semi'){
		//$pdf->cell(10,5,'S.No',0,0,'C');
		$pdf->cell(170,5,'Service Type',0,0,'L');
		$pdf->cell(25,5,'Amount',0,1,'R');
	}
	else{
	$pdf->cell(7,5,'Date',0,0,'C');
	$pdf->cell(20,5,'Service Code',0,0,'C');
	$pdf->cell(100,5,'Services / Investigation',0,0,'C');
	$pdf->cell(20,5,'Exc.Qty',0,0,'R');
	$pdf->cell(20,5,'Rate',0,0,'R');
	$pdf->cell(20,5,'Exc.Amt',0,1,'R');
	}
}
$pdf->cell(195,0,'','T',0,1);
$pdf->cell(10,2,'',0,1);

$j = 0;
//Category Loop
while($character_att['billing'][$j]){

	$billing = $character_att['billing'][$j];
	//Sub-Category
	$billinghead = $billing['category'];
	$billinghead_value = $billinghead['value'];
	$billinghead_item_name = $billinghead['display'];
	$pdf->SetTextColor(74, 26, 255);
	$pdf->SetFont('Arial','B','9');
	$pdf->cell(175,5,$billinghead_item_name,0,0,'L');
	$pdf->SetFont('Arial','','9');
	$pdf->cell(24,5,$billinghead_value,0,1,'L');
	$pdf->SetFont('Arial','','8');
	$i=0;
if($billtype != 'summary'){
	while($billinghead['list'][$i]){
	$category = $billinghead['list'][$i]['subcategory'];
		$category_value = $category['value'];
		$subinclcategory = $category['inclamount'];
		$subexclcategory = $category['exclamount'];
	   $category_item_name = $category['display'];
	$pdf->SetTextColor(0, 0, 0);
	$pdf->cell(5,5,'',0,0,'L');
	$pdf->SetFont('Arial','B','9');
	$pdf->cell(170,5,$category_item_name,0,0,'L');
	$pdf->cell(24,5,$subexclcategory,0,1,'L');
	$pdf->SetFont('Arial','','8');

	$k = 0;
	if($billtype == 'final'){
	while($category['list'][$k]){
		$list = $category['list'][$k];
		if($format == 'WithoutDate'){
		//	$pdf->cell(10,5,"",0,0,'R');
			$pdf->cell(20,5,$list['servicecode'],0,0,'R');
			$pdf->cell(55,5,substr($list['services'],0,30),0,0,'C');
			$pdf->cell(10,5,$list['inclquantity'],0,0,'R');
			$pdf->cell(15,5,$list['rate'],0,0,'R');
			$pdf->cell(5,5,$list['quantity'],0,0,'R');
			$pdf->cell(12,5,$list['inclamount'],0,0,'R');

		}else{
			$pdf->cell(7,5,$list['createdon'],0,0,'R');
			$pdf->cell(20,5,$list['servicecode'],0,0,'R');
			$pdf->cell(100,5,substr($list['services'],0,30),0,0,'C');
			$pdf->cell(20,5,$list['exclquantity'],0,0,'R');
			$pdf->cell(20,5,$list['rate'],0,0,'R');
			$pdf->cell(20,5,$list['exclamount'],0,1,'R');
		}

	$k++;
	if ($pdf->GetY() > 250) {
		$pdf->AddPage();
	} 
	}
}
		$i++;
		if ($pdf->GetY() > 250) {
		$pdf->AddPage();
	} 
	}
}
	$j++;
	if ($pdf->GetY() > 250) {
		$pdf->AddPage();
	} 
}


$pdf->SetTextColor(0, 0, 0);
$pdf->cell(193,5,'','B',0,1);
// $pdf->SetFont('Arial','B','9');
// $pdf->cell(195,8,'',0,1);
// $pdf->cell(10,7,'','T',0,'C');
// $pdf->cell(22,7,'',0,'C');
// $pdf->cell(69,7,'',0,'C');
// $pdf->cell(30,7,'',0,'C');
// $pdf->cell(11,7,'',0,'C');
// $pdf->cell(20,7,'',0,'C');
// $pdf->cell(29,7,'',0,'C');

//Gross Values
$totalinwords = $character_att['total']['totalinwords'];
$l = 0;
while($character_att['total']['list'][$l]){
	
	$pdf->SetFont('Arial','B','9');
	$total = $character_att['total']['list'][$l];
	$total_display  = $total['display'];
	$total_value = $total['value'];
	$pdf->cell(140,5,'',0,0);
	$pdf->cell(25,5,$total_display,0,0,'C');
	$pdf->cell(10,5,':',0,0,'C');
	$pdf->cell(20,5,$total_value,0,1,'R');
	
$l++;
if ($pdf->GetY() > 250) {
	$pdf->AddPage();
} 
}
if ($pdf->GetY() > 250) {
	$pdf->AddPage();
} 
//Receipt/ Payment Details
$pdf->cell(120,5,'',0,1);
// $pdf->cell(120,6,'',0,1);
$pdf->SetFont('Arial','B','9');
$pdf->cell(195,5,'Receipt/ Payment Details',0,1);

//  $pdf->cell(195,0,'',0,1);
$pdf->SetFont('Arial','B','9');
$pdf->cell(30,5,'Recpt. No.','TB',0,'C');
$pdf->cell(18,5,'Recpt. Dt.','TB',0,'C');
$pdf->cell(23,5,'Cash Amt','TB',0,'C');
$pdf->cell(23,5,'Cheque Amt','TB',0,'C');
$pdf->cell(23,5,'Card Amt','TB',0,'C');
$pdf->cell(23,5,'Upi','TB',0,'C');
$pdf->cell(23,5,'Recpt. Amt','TB',0,'C');
$pdf->cell(30,5,'Remarks','TB',1,'C');


$receipt_value = $character_att['payment']['value'];
$m = 0;
while($character_att['payment']['list'][$m]){
	
	$receipt = $character_att['payment']['list'][$m];
	$receiptno  = $receipt['receiptno'];
	$receiptdate = $receipt['receiptdate'];
	$cash = $receipt['cash'];
	$cheque = $receipt['cheque'];
	$card = $receipt['card'];
	$upi = $receipt['upi'];
	$recptamt = $receipt['recptamt'];
	$remarks = $receipt['remarks'];
	
	$rem1 = substr($remarks, 0, 20);
	$rem2 = substr($remarks, 20,40);
	
	$pdf->SetFont('Arial','','9');
	// $pdf->cell(0,3,'',0,1);
	$pdf->cell(30,5,$receiptno,0,'C');
	$pdf->cell(20,5,$receiptdate,0,'C');
	$pdf->cell(28,5,$cash,0,'R');
	$pdf->cell(23,5,$cheque,0,'R');
	$pdf->cell(23,5,$card,0,'R');
	$pdf->cell(23,5,$upi,0,'R');
	$pdf->cell(23,5,$recptamt,0,'R');
	$pdf->cell(30,5,$rem1,0,1,'L');
	
	if($rem2 != ''){
		$pdf->SetFont('Arial','','9');
		$pdf->cell(30,8,'',0,'C');
		$pdf->cell(20,8,'',0,'C');
		$pdf->cell(28,8,'',0,'R');
		$pdf->cell(23,8,'',0,'R');
		$pdf->cell(23,8,'',0,'R');
		$pdf->cell(23,8,'',0,'R');
		$pdf->cell(23,8,'',0,'R');
		$pdf->cell(30,8,$rem2,0,'L');
	}
	
$m++;
if ($pdf->GetY() > 250) {
	$pdf->AddPage();
} 
}

if ($pdf->GetY() > 250) {
	$pdf->AddPage();
} 
$pdf->SetFont('Arial','B','9');
$pdf->cell(140,5,'','TB',0,'L');
$pdf->cell(25,5,'Total','TB',0,'R');
$pdf->cell(10,5,':','TB',0,'C');
$pdf->cell(20,5,$receipt_value,'TB',1,'R');

//Amount in Words
$pdf->SetFont('Arial','B','9');
$pdf->cell(120,10,'',0,1);
$pdf->cell(52,4,'Total Net Amount in Words',0,0);
$pdf->SetFont('Arial','','9');
$pdf->cell(4,4,':',0,0);
$pdf->MultiCell(125,4,'RUPEES '.strtoupper($totalinwords),0);

// $pdf->SetFont('Arial','B','9');
// $pdf->cell(120,8,'',0,1);
// $pdf->cell(40,4,'Gross Amount in Words',0,0);
// $pdf->SetFont('Arial','','9');
// $pdf->cell(16,4,':',0,0);
// $pdf->MultiCell(125,4,'RUPEES '.strtoupper($totalinwords),0);



// $pdf->SetY(250);
// $pdf->SetFont('Arial','B','8');

// $pdf->cell(100,5,'Note : This computer generated prescription no signature required',0,0,'L');
// $pdf->Cell(95,5,'www.medicoverhospitals.in',0,1,'R');
// $pdf->Cell(196,5,'', 'T',0,1);
// $pdf->SetY(260);
// $pdf->SetFont('Arial','','8');
// $pdf->Cell(60,5,'',0,0);
// $pdf->Cell(20,5,'Created By :',0,0);
// $pdf->Cell(60,5,$createdby,0,0);
// $pdf->Cell(20,5,'Created On :',0,0);
// $pdf->Cell(40,5,$createdon,0,1);
// $pdf->Cell(60,5,'',0,0);
// $pdf->Cell(20,5,'Printed By :',0,0);
// $pdf->Cell(60,5,$printedby,0,0);
// $pdf->Cell(20,5,'Printed On :',0,0);
// $pdf->Cell(40,5,$printon,0,1);
// $pdf->SetFont('Arial','B','10');

// $pdf->Image('https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$billno.'&choe=UTF-8&chco=222222&chld=L|0',12,260,20,20,"png");
// $pdf->SetFont('Arial','B','9');



$pdf->Output('Approx-Bill.pdf','I');
