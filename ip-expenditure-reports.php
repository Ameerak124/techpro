<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$fromdate =date('Y-m-d', strtotime($data->fromdate));
$todate = date('Y-m-d', strtotime($data->todate));
try {
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` ,`username` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
//$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$userid = $result['userid'];
	$username = $result['username'];
	$cost_center = $result['cost_center'];  
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `umrno`,`admissionno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y') AS admittedon,`patientname`,`consultantname`,`department`,(CASE WHEN `referral_name` = 'No Update' THEN '' ELSE `referral_name` END) AS referral_name,`patient_category`,`organization_name`,CONCAT(`admittedward`,' / ',`roomno`) AS admittedward,ROUND((CASE WHEN `billing_history`.`status` = 'Visible' AND `patient_category` = 'GENERAL' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS CASHBILLAMT,ROUND((CASE WHEN `billing_history`.`status` = 'Visible' AND `patient_category` = 'CORPORATE' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS CORPBILLAMT,ROUND((CASE WHEN `billing_history`.`status` = 'Visible' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS APPBILLAMOUNT
	,(SELECT IFNULL(SUM(`total`),0)  FROM `payment_history` WHERE `admissionon` LIKE `registration`.`admissionno` AND `status` LIKE 'Visible') AS TOTADVANCE,ROUND((CASE WHEN `billing_history`.`status` = 'Visible' AND `patient_category` = 'INSURANCE' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS INSURANCEAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Service Charges' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS SERAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Investigation Charges' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS INVAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Consultation Charges' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS CONSAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Ward Charges' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS WARDAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Miscellaneous Charge' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS MISCAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Professional Charges' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS PROFAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Procedure Charges' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS PROCAMT,ROUND((CASE WHEN `billing_history`.`billinghead` = 'Pharmacy Charges' THEN SUM(`billing_history`.`aftertotal`) ELSE 0 END),0) AS PHARMAMT,`registration`.`contactno`,`registration`.`cost_center`,'Rural' AS AREA,ROUND(SUM(`billing_history`.`aftertotal`) - (SELECT IFNULL(SUM(`total`),0)  FROM `payment_history` WHERE `admissionon` LIKE `registration`.`admissionno` AND `status` LIKE 'Visible'),0) AS OUTSTANDINGBALANCE,(CASE WHEN `admissionstatus` = 'Initiated Discharge' THEN 'Y' ELSE 'N' END) AS discharge_flag FROM `registration` LEFT JOIN `billing_history` ON `billing_history`.`ipno` = `registration`.`admissionno` AND `billing_history`.`status` = 'Visible' WHERE DATE(`admittedon`) BETWEEN :fromdate AND :todate AND `registration`.`cost_center` = :cost_center AND `registration`.`admissionstatus` NOT IN ('Discharged','Cancelled') GROUP BY `admissionno`;");
	$reglist -> bindParam(":cost_center" , $result['cost_center'] , PDO::PARAM_STR);
	$reglist -> bindParam(":todate" , $todate , PDO::PARAM_STR);
	$reglist -> bindParam(":fromdate" , $fromdate , PDO::PARAM_STR);

$reglist -> execute();
if($reglist -> rowCount() > 0){
	    http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	$response['referral']= "Referral";
	$response['viewtable_report']= "View Table Report";
	$response['cashbillamt']= "CASHBILLAMT";
	$response['corpbillamt']= "CORPBILLAMT";
	$response['appbillamt']= "APPBILLAMOUNT";
	$response['totadv']= "TOTADVANCE";
	$response['insuranceamt']= "INSURANCEAMT";
	$response['seramt']= "SERAMT";
	$response['invamt']= "INVAMT";
	$response['consamt']= "CONSAMT";
	$response['wardamt']= "WARDAMT";
	$response['miscamt']= "MISCAMT";
	$response['profamt']= "PROFAMT";
	$response['procamt']= "PROCAMT";
	$response['pharmamt']= "PHARMAMT";
	$response['fromdate']= $fromdate;
	$response['todate']=  $todate;
	while($regres = $reglist->fetch(PDO::FETCH_ASSOC)){
		$response['ipexpenditurelist'][] = $regres;
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>