<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($_POST['accesskey']);
$searchterm = trim($_POST['searchterm']);
$billtype = trim($_POST['billtype']);
$datetype = trim($_POST['datetype']);
if(empty($accesskey) && empty($searchterm)){
	$accesskey = trim($data->accesskey);
	$searchterm = trim($data->searchterm);
	$billtype = trim($data->billtype);
	$datetype = trim($data->datetype);	
}
$class_obj = new numbertowordconvertsconver();
try {
$pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT *,E.address AS patientaddress,`branch_master`.`viewbranch`,`branch_master`.`address` AS unitaddress,CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS city,CONCAT('GST No -',`branch_master`.`gst_no`) AS gstno,CONCAT('Tel. No: 040 6833 4455') AS contactno,(SELECT `title` FROM `umr_registration` WHERE `umrno` = E.umrno LIMIT 1) AS title , (SELECT `umr_registration`.`mobile_no` AS mobileno FROM `umr_registration` WHERE `umrno` = E.umrno LIMIT 1) AS patmobile FROM (SELECT `billno`,DATE_FORMAT(CURRENT_TIMESTAMP,'%d-%b-%Y %h:%i %p') AS billdate,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,'%d %b %Y %h:%i %p') AS dateofadmission,`consultantname` AS consultant,`department` AS department,CONCAT(`address`,',',`city`,',',`state`) AS address,`admissionno`, IF(`registration`.`admissionstatus` = 'Discharged', DATE_FORMAT(CURRENT_TIMESTAMP,'%d-%b-%Y'),'') AS dischargedate,`s_w_d_b_o` AS s_w_d_b_o,`umrno`,CONCAT(DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0,'Y(s)/',`patientgender`) AS agesex,IF(`procedure_surgery` = 'No Update','',`procedure_surgery`) AS surgery,CONCAT(`admittedward`,' / ',`roomno`) AS admittedward,`admission_category` AS referral,`modifiedby` AS preparedby,DATE_FORMAT(`modifiedon`,'%d-%b-%Y %h:%i %p') AS prepareddt,DATE_FORMAT(CURRENT_TIMESTAMP,'%d-%b-%Y %h:%i %p') AS printedon,(CASE WHEN `sponsor_name` IN ('','No Update') THEN 'CASH' ELSE `sponsor_name` END) AS sponsor_name,`registration`.`cost_center`,(CASE WHEN `registration`.`sponsor_category` IN ('No Update','') THEN 'GENERAL' ELSE `registration`.`sponsor_category` END) AS sponsor_category FROM `registration` WHERE `admissionno` = :searchterm AND `status` = 'Visible') AS E LEFT JOIN `branch_master` ON E.cost_center = `branch_master`.`cost_center`");
$reglist->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
$reglist -> execute();
if($reglist-> rowCount() > 0){
	$regres = $reglist->fetch(PDO::FETCH_ASSOC);
		$response['error']= false;
	$response['message']= "Data found";
	$response['printedby']= $result['userid'];
	$response['printedon']= $regres['printedon'];
	$response['preparedby']= $regres['preparedby'];
	$response['preparedon']= $regres['prepareddt'];
	$response['viewbranch']= $regres['viewbranch'];
	$response['unitaddress']= $regres['unitaddress'];
	$response['address']= $regres['patientaddress'];
	$response['city']= $regres['city'];
	$response['gstno']= $regres['gstno'];
	$response['contactno']= $regres['contactno'];
	$response['sponsor_category']= $regres['sponsor_category'];
	$response['tpa']= $regres['sponsor_name'];
	$response['sponsor_name']= $regres['sponsor_name'];
	
	$response['hospitalisationchargesfrom'] = "Hospitalisation Charges From ". $regres['dateofadmission']." To ".$regres['billdate'];
	$response['details'][0] = $regres;
	$billinghead = $pdoread->prepare("SELECT `billinghead`,SUM(ROUND(`total`,2)) AS subcategorytotal , SUM(ROUND(`billing_history`.`bill_included_package`,2)) AS subinclutotal  , SUM(ROUND(`billing_history`.`bill_excluded_package`,2)) AS subexclutotal FROM `billing_history` WHERE `ipno` = :searchterm AND `status` = 'Visible' AND `billing_history`.`service_type` != 'CASH_PACKAGE' AND bill_excluded_package > 0 AND `credit_debit` IN ('CREDIT','DEBIT') GROUP BY `billinghead` ORDER BY FIELD(`billinghead`,'Ward Charges','Consultation Charges','Professional Charges','Procedure Charges','Surgery Charges','Service Charges','Investigation Charges','Pharmacy Charges','Non-medical Charges')");
	$billinghead->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
	$billinghead -> execute();
	$i = 0;

	/* data start */
	$pacakgeorder = $pdoread -> prepare("SELECT `category` AS category,`subcategory`,SUM(ROUND(`total`,2)) AS subcategorytotal , SUM(ROUND(`total`,2)) AS subcategorytotal , SUM(ROUND(`billing_history`.`bill_included_package`,2)) AS subinclutotal  , SUM(ROUND(`billing_history`.`bill_excluded_package`,2)) AS subexclutotal  FROM `billing_history`  WHERE `ipno` = :searchterm AND bill_excluded_package > 0 AND `status` = 'Visible' AND `credit_debit`  = 'CREDIT' AND `billing_history`.`service_type` = 'CASH_PACKAGE'");
	$pacakgeorder->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
	$pacakgeorder -> execute();
	if($pacakgeorder -> rowCount() > 0){
		$orderlist = $pacakgeorder -> fetch(PDO::FETCH_ASSOC);
		$response['package']['incltotal'] = $orderlist['subinclutotal'];
		$response['package']['excltotal'] = $orderlist['subexclutotal'];
		$response['package']['subtotal'] = $orderlist['subcategorytotal'];
	}
	/* get list start */
	$getpackagelist = $pdoread->prepare("SELECT `servicecode`,`services`,`hsn_sac`,`quantity`,ROUND(`total`,2) AS rate,`discountvalue`,ROUND(`aftertotal`,2) AS total,DATE_FORMAT(`createdon`,'%d-%b-%y') AS createdon , `billing_history`.`bill_included_package` AS inclamount ,IF(`billing_history`.`bill_included_package` > 0 , `billing_history`.`quantity` ,0) AS inclquantity , `billing_history`.`bill_excluded_package` AS exclamount ,  IF(`billing_history`.`bill_excluded_package` > 0 , `billing_history`.`quantity` ,0) AS exclquantity  FROM `billing_history` WHERE `ipno` = :searchterm AND `status` ='Visible' AND `billing_history`.`service_type` = 'CASH_PACKAGE' AND `credit_debit` ='CREDIT'");
	$getpackagelist -> bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
	$getpackagelist -> execute();
	if($getpackagelist -> rowCount() > 0){
		
		while($packagelist = $getpackagelist -> fetch(PDO::FETCH_ASSOC)){
			$response['packagelist'][] = $packagelist;
  		}
	} 
	/* get list end */
	/* data end */
while($billingheadres = $billinghead->fetch(PDO::FETCH_ASSOC)){
	    $response['billing'][$i]['category']['value'] = $billingheadres['subcategorytotal'];
		$response['billing'][$i]['category']['display'] = $billingheadres['billinghead'];
		$response['billing'][$i]['category']['inclamount'] = $billingheadres['subinclutotal'];
		$response['billing'][$i]['category']['exclamount'] = $billingheadres['subexclutotal'];

		$order = $pdoread -> prepare("SELECT `category` AS category,`subcategory`,SUM(ROUND(`total`,2)) AS subcategorytotal , SUM(ROUND(`total`,2)) AS subcategorytotal , SUM(ROUND(`billing_history`.`bill_included_package`,2)) AS subinclutotal  , SUM(ROUND(`billing_history`.`bill_excluded_package`,2)) AS subexclutotal  FROM `billing_history` WHERE `ipno` = :searchterm AND `status` = 'Visible' AND bill_excluded_package > 0 AND `credit_debit` IN ('CREDIT','DEBIT') AND `billing_history`.`service_type` != 'CASH_PACKAGE' AND `billinghead` = :billinghead GROUP BY `subcategory` ORDER BY `category` DESC");
		$order->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
		$order->bindParam(':billinghead', $billingheadres['billinghead'], PDO::PARAM_STR);
		$order -> execute();
		$s = 0;
	
	while($orderres = $order->fetch(PDO::FETCH_ASSOC)) {
		if($billtype == 'final' && $datetype == 'WithoutDate')
		{
			$list = $pdoread->prepare("SELECT `servicecode`,`services`,`hsn_sac`,SUM(`quantity`) AS quantity,ROUND(`rate`,2) AS rate,`discountvalue`,ROUND(`aftertotal`*SUM(`quantity`),2) AS total,DATE_FORMAT(`createdon`,'%d-%b-%y') AS createdon , SUM(`billing_history`.`bill_included_package`) AS inclamount ,IF(`billing_history`.`bill_included_package` > 0 , SUM(`billing_history`.`quantity`) ,0) AS exclquantity , SUM(`billing_history`.`bill_excluded_package`) AS exclamount ,  IF(`billing_history`.`bill_excluded_package` > 0 , SUM(`billing_history`.`quantity`) ,0) AS inclquantity AND bill_excluded_package > 0  FROM `billing_history` WHERE `ipno` = :searchterm AND `status` ='Visible' AND `billing_history`.`service_type` != 'CASH_PACKAGE' AND `credit_debit` ='CREDIT' AND `category` =:category AND `subcategory` =:subcategory AND `billinghead` = :billinghead GROUP BY `servicecode`");
			$list->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
			$list->bindParam(':category', $orderres['category'], PDO::PARAM_STR);
			$list->bindParam(':subcategory', $orderres['subcategory'], PDO::PARAM_STR);
			$list->bindParam(':billinghead', $billingheadres['billinghead'], PDO::PARAM_STR);
		}
		else
		{
			$list = $pdoread->prepare("SELECT `servicecode`,`services`,`hsn_sac`,`quantity`,ROUND(`rate`,2) AS rate,`discountvalue`,ROUND(`aftertotal`,2) AS total,DATE_FORMAT(`createdon`,'%d-%b-%y') AS createdon , `billing_history`.`bill_included_package` AS inclamount ,IF(`billing_history`.`bill_included_package` > 0 , `billing_history`.`quantity` ,0) AS inclquantity , `billing_history`.`bill_excluded_package` AS exclamount ,  IF(`billing_history`.`bill_excluded_package` > 0 , `billing_history`.`quantity` ,0) AS exclquantity   FROM `billing_history` WHERE  `ipno` = :searchterm AND `status` ='Visible' AND bill_excluded_package > 0 AND `billing_history`.`service_type` != 'CASH_PACKAGE' AND `credit_debit` ='CREDIT' AND `category` =:category AND `subcategory` =:subcategory AND `billinghead` = :billinghead");
			$list->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
			$list->bindParam(':category', $orderres['category'], PDO::PARAM_STR);
			$list->bindParam(':subcategory', $orderres['subcategory'], PDO::PARAM_STR);
			$list->bindParam(':billinghead', $billingheadres['billinghead'], PDO::PARAM_STR);
		}
			$list-> execute();
			$grosstotal = 0;
			$total = 0;
			$inclamount	= 0;
			$exclamount = 0;

			while($listres = $list->fetch(PDO::FETCH_ASSOC)){
			
				$response['billing'][$i]['category']['list'][$s]['subcategory']['inclamount'] = $orderres['subinclutotal'];
				$response['billing'][$i]['category']['list'][$s]['subcategory']['exclamount'] = $orderres['subexclutotal'];
				$response['billing'][$i]['category']['list'][$s]['subcategory']['value'] = $orderres['subcategorytotal'];
				$response['billing'][$i]['category']['list'][$s]['subcategory']['display'] = $orderres['subcategory'];
				$response['billing'][$i]['category']['list'][$s]['subcategory']['list'][] = $listres;
				//$response['list'][0]=$listres;
				$grosstotal += $listres['rate']; 
				$total += $listres['total'];
				$inclamount += $listres['inclamount'];
				$exclamount += $listres['exclamount'];
			}
				$response['billing'][$i]['category']['list'][$s]['value'] = number_format((float)ROUND($total,2), 2, '.', '');
				$response['billing'][$i]['category']['list'][$s]['packincl'] = number_format((float)ROUND($inclamount,2), 2, '.', '');
				$response['billing'][$i]['category']['list'][$s]['packexcl'] = number_format((float)ROUND($exclamount,2), 2, '.', '');
				$response['billing'][$i]['category']['list'][$s]['display'] = $orderres['category'];
				$s++;
				$convert_number += number_format((float)ROUND($total,2), 2, '.', '');
				$convert_number_incl += number_format((float)ROUND($inclamount,2), 2, '.', '');
				$convert_number_excl += number_format((float)ROUND($exclamount,2), 2, '.', '');
	
	}
	$i++;
}
	$totals = $pdoread->prepare("SELECT '' AS display, '' AS netvalue UNION ALL SELECT 'Total Bill Amount' AS display, `gross_amount` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' UNION ALL SELECT 'Discount Amount' AS display, `patient_discount` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `patient_discount` != 0 UNION ALL SELECT 'Net Paid Amount' AS display, `advance_amt` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `advance_amt` != 0 UNION ALL SELECT 'Co-Payment / Difference Amount' AS display, `co_payment` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `co_payment` != '0' UNION ALL SELECT 'Total Received Amount' AS display, `patient_payable` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `patient_payable` != '0' UNION ALL SELECT 'Organization Payable Amount' AS display, `organization_payable` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `organization_payable` != '0' UNION ALL SELECT 'AS PER MOU DISCOUNT' AS display, `mou_discount` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `mou_discount` != '0' UNION ALL SELECT 'Organization Due Amt' AS display, `organization_due` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `organization_due` != '0' UNION ALL SELECT 'Balance Amount' AS display, `total_bill` AS netvalue FROM `registration` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `patient_category` = 'GENERAL' AND `total_bill` != 0");
	$totals->bindParam(':admissionno', $searchterm, PDO::PARAM_STR);
	$totals ->execute();
	$it = 0;
	while($totalses = $totals->fetch(PDO::FETCH_ASSOC)){

		$response['total']['list'][$it]['display'] =	$totalses['display'];
		$response['total']['list'][$it]['value'] = 	number_format((int)ROUND($totalses['netvalue'],2), 2, '.', '');
		$it++;
	}
	$response['total']['totalinwords'] = $class_obj->convert_number($totalses['netamount'])." Only";
	$payment = $pdoread->prepare("SELECT `receiptno`,DATE_FORMAT(`receiptdate`,'%d-%b-%y') AS receiptdate,(CASE WHEN `paymentmode` = 'CASH' THEN ROUND(`amount`,2) ELSE '0.00' END) AS cash,(CASE WHEN `paymentmode` = 'CHEQUE' THEN ROUND(`amount`,2) ELSE '0.00' END) AS cheque,(CASE WHEN `paymentmode` = 'CARD' THEN ROUND(`amount`,2) ELSE '0.00' END) AS card,(CASE WHEN `paymentmode` = 'UPI/NETBANKING' THEN ROUND(`amount`,2) ELSE '0.00' END) AS upi,ROUND(`amount`,2) AS recptamt,`remarks` AS remarks FROM `payment_history` WHERE `admissionon` = :admissionno AND `billno` = :billno AND `status` = 'Visible' AND `credit_debit` = 'Credit'");
	$payment->bindParam(':billno', $regres['billno'], PDO::PARAM_STR);
	$payment->bindParam(':admissionno', $searchterm, PDO::PARAM_STR);
	$payment ->execute();
	$paidamt = 0;
	while($paymentres = $payment->fetch(PDO::FETCH_ASSOC)){
		$response['payment']['list'][] = $paymentres;
		$paidamt+= $paymentres['recptamt'];
	}
	$response['payment']['value'] = number_format((float)ROUND($totalses['totoalpaid'],2), 2, '.', '');
}else{
	$response['error']= true;
	$response['message']="No data found";
}
}else{
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>
<?php
class numbertowordconvertsconver {
    function convert_number($number) 
    {
        if (($number < 0) || ($number > 999999999)) 
        {
            throw new Exception("Number is out of range");
        }
        $giga = floor($number / 1000000);
        // Millions (giga)
        $number -= $giga * 1000000;
        $kilo = floor($number / 1000);
        // Thousands (kilo)
        $number -= $kilo * 1000;
        $hecto = floor($number / 100);
        // Hundreds (hecto)
        $number -= $hecto * 100;
        $deca = floor($number / 10);
        // Tens (deca)
        $n = $number % 10;
        // Ones
        $result = "";
        if ($giga) 
        {
            $result .= $this->convert_number($giga) .  "Million";
        }
        if ($kilo) 
        {
            $result .= (empty($result) ? "" : " ") .$this->convert_number($kilo) . " Thousand";
        }
        if ($hecto) 
        {
            $result .= (empty($result) ? "" : " ") .$this->convert_number($hecto) . " Hundred";
        }
        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
        if ($deca || $n) {
            if (!empty($result)) 
            {
                $result .= " and ";
            }
            if ($deca < 2) 
            {
                $result .= $ones[$deca * 10 + $n];
            } else {
                $result .= $tens[$deca];
                if ($n) 
                {
                    $result .= "-" . $ones[$n];
                }
            }
        }
        if (empty($result)) 
        {
            $result = "zero";
        }
        return $result;
    }
}
?>