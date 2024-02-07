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
$patient_category = strtoupper($data->patient_category);
$country = strtoupper($data->country);
$patientname = strtoupper($data->patientname);
$patientage = date_format(date_create($data->patientage),"Y-m-d");
$patientgender = strtoupper($data->patientgender);
$contactno = ($data->contactno);
$alternativeno = ($data->alternativeno);
$emailid = strtolower($data->emailid);
$address = strtoupper(str_ireplace("'","",$data->address));
$city = strtoupper(str_ireplace("'","",$data->city));
$state = strtoupper($data->state);
$amount = (int) ($data->amount);
$paymentmode = strtoupper($data->paymentType);
$referenceid = strtoupper($data->referenceId);
$remarks = str_ireplace("'","",$data->remarks);
$paymentstatus = "CREDIT";
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {

if(!empty($accesskey)&&!empty($patient_category)&&!empty($country)&&!empty($patientname)&&!empty($patientage)&&!empty($patientgender)&&!empty($contactno)&&!empty($address)&&!empty($city)&&!empty($state)&&!empty($amount)&&!empty($paymentmode)){
//Check access 
$check = $con -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//Generate Admission number Start
$generateadmno = $pdoread -> prepare("SELECT IFNULL(MAX(`umrno`),CONCAT('MWC',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000')) AS umrno  FROM `umr_registration`");
$generateadmno -> execute();
if($generateadmno -> rowCount() > 0){
$resultadmno = $generateadmno->fetch(PDO::FETCH_ASSOC);
$admission =  $resultadmno['umrno'];
 $admissionnum = ++$admission;
}else{
$admissionnum = $admissionno;
}
//Generate Admission number End
$check = $pdoread -> prepare("SELECT * FROM `umr_registration` WHERE `patient_name` LIKE :patientname AND `mobile_no` = :contactno");
$check->bindParam(':patientname', $patientname, PDO::PARAM_STR);
$check->bindParam(':contactno', $contactno, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$response['error']= true;
	$response['message']= "Details are already exist";
}else{
// Create Registration
$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `umr_registration`(`sno`, `umrno`, `category`, `country`, `patient_name`, `patient_age`, `patient_gender`, `mobile_no`, `alternative_no`, `email_id`, `address`, `state`, `city`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`) VALUES (NULL,:admissionnum,:patient_category,:country,:patientname,:patientage,:patientgender,:contactno,:alternativeno,:emailid,:address,:state,:city,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Visible')");
$saleprice->bindParam(':admissionnum', $admissionnum, PDO::PARAM_STR);
$saleprice->bindParam(':patient_category', $patient_category, PDO::PARAM_STR);
$saleprice->bindParam(':patientname', $patientname, PDO::PARAM_STR);
$saleprice->bindParam(':patientage', $patientage, PDO::PARAM_STR);
$saleprice->bindParam(':patientgender', $patientgender, PDO::PARAM_STR);
$saleprice->bindParam(':contactno', $contactno, PDO::PARAM_STR);
$saleprice->bindParam(':alternativeno', $alternativeno, PDO::PARAM_STR);
$saleprice->bindParam(':emailid', $emailid, PDO::PARAM_STR);
$saleprice->bindParam(':address', $address, PDO::PARAM_STR);
$saleprice->bindParam(':state', $state, PDO::PARAM_STR);
$saleprice->bindParam(':city', $city, PDO::PARAM_STR);
$saleprice->bindParam(':country', $country, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$saleprice -> execute();
if($saleprice -> rowCount() > 0){
	
//Generate Receipt number Start
$receipt = $pdoread -> prepare("SELECT IFNULL(MAX(`receiptno`),CONCAT('MCR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000000')) AS receiptno FROM `payment_history`");
$receipt -> execute();
if($receipt -> rowCount() > 0){
$receiptres = $receipt->fetch(PDO::FETCH_ASSOC);
$receiptno =  $receiptres['receiptno'];
$receiptno = ++$receiptno;
}
//Generate Receipt number End
// Create Registration
$payment = $pdo4 -> prepare("INSERT IGNORE INTO `payment_history`(`sno`,`bill_type`, `receiptno`, `receiptdate`, `admissionon`, `billno`, `amount`, `paymentmode`, `referenceno`, `credit_debit`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `remarks`, `ipaddress`, `status`) VALUES (NULL,'registration',:receiptno,CURRENT_TIMESTAMP,:admissionnum,:receiptno,:amount,:paymentmode,:referenceid,:paymentstatus,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:remarks,:ipaddress,'Visible')");
$payment->bindParam(':admissionnum', $admissionnum, PDO::PARAM_STR);
$payment->bindParam(':receiptno', $receiptno, PDO::PARAM_STR);
$payment->bindParam(':amount', $amount, PDO::PARAM_STR);
$payment->bindParam(':referenceid', $referenceid, PDO::PARAM_STR);
$payment->bindParam(':paymentmode', $paymentmode, PDO::PARAM_STR);
$payment->bindParam(':paymentstatus', $paymentstatus, PDO::PARAM_STR);
$payment->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$payment->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
$payment->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$payment -> execute();
            http_response_code(200);
			$response['error']= false;
	$response['message']= "Thank you! umrnumber is created";
	$response['umrnumber']=$admissionnum;
}else{
	 http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please contact IT team.";
}
}
}else{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " ;
	$e;
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>