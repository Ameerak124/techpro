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
$admissionno = strtoupper($data->admissionno);
$category = strtoupper($data->category);
$subcategory = strtoupper($data->subcategory);
$servicecode = strtoupper($data->servicecode);
$service = str_ireplace("'","",strtoupper($data->service));
$servicestatus = strtoupper($data->servicestatus);
$hsn_sac = strtoupper($data->hsn_sac);
$quantity = (int) ($data->quantity);
$rate = (double) ROUND(($data->rate),2);
$total = ROUND(($quantity*$rate),2);
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey) && !empty($admissionno) && !empty($category) && !empty($subcategory) && !empty($servicecode) && !empty($service) && !empty($servicestatus) && !empty($quantity) && !empty($rate)){	
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){

//Generate Bill number Start
$admissioncheck = $pdoread -> prepare("SELECT `billno` AS billno FROM `billing_history` WHERE `ipno` = :admissionno");
$admissioncheck->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$admissioncheck -> execute();
if($admissioncheck -> rowCount() > 0){
$resultadmno = $admissioncheck->fetch(PDO::FETCH_ASSOC);
$billno = $resultadmno['billno'];
}else{
$generateadmno = $pdoread -> prepare("SELECT IFNULL(MAX(`billno`),CONCAT('MOFB',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000')) AS billno FROM `billing_history` WHERE DATE_FORMAT(`createdon`,'%y%b') = DATE_FORMAT(CURRENT_TIMESTAMP,'%y%b')");
$generateadmno -> execute();
$resultadmno = $generateadmno->fetch(PDO::FETCH_ASSOC);
$billno =  $resultadmno['billno'];
$billno = ++$billno;
$updatebillno = $pdo4 -> prepare("UPDATE `registration` SET `billno` = :billno,`modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP WHERE `admissionno` = :admissionno");
$updatebillno->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$updatebillno->bindParam(':billno', $billno, PDO::PARAM_STR);
$updatebillno->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$updatebillno -> execute();
}
//Generate Bill number End
// Create Registration
$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `billing_history`(`sno`, `ipno`, `billno`, `category`, `subcategory`, `servicecode`, `services`, `hsn_sac`, `quantity`, `rate`, `total`, `credit_debit`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `ipaddress`, `status`) VALUES (NULL,:admissionno,:billno,:category,:subcategory,:servicecode,:service,:hsn_sac,:quantity,:rate,:total,:servicestatus,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:ipaddress,'Hold')");
$saleprice->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$saleprice->bindParam(':billno', $billno, PDO::PARAM_STR);
$saleprice->bindParam(':category', $category, PDO::PARAM_STR);
$saleprice->bindParam(':subcategory', $subcategory, PDO::PARAM_STR);
$saleprice->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$saleprice->bindParam(':service', $service, PDO::PARAM_STR);
$saleprice->bindParam(':hsn_sac', $hsn_sac, PDO::PARAM_STR);
$saleprice->bindParam(':quantity', $quantity, PDO::PARAM_STR);
$saleprice->bindParam(':rate', $rate, PDO::PARAM_STR);
$saleprice->bindParam(':total', $total, PDO::PARAM_STR);
$saleprice->bindParam(':servicestatus', $servicestatus, PDO::PARAM_STR);
$saleprice->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$saleprice -> execute();
$lastid = $con->lastInsertId();
if($saleprice -> rowCount() > 0){
	
	$track = $pdo4 -> prepare("INSERT IGNORE INTO `billing_history_track`(`sno`, `billing_id`, `bill_no`, `requisition_no`, `service_code`, `service_name`, `quantity`, `track_status`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`) VALUES (NULL,:lastid,:billno,'',:servicecode,:service,:quantity,'Bill Generated',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Visible')");
$track->bindParam(':lastid', $lastid, PDO::PARAM_STR);
$track->bindParam(':billno', $billno, PDO::PARAM_STR);
$track->bindParam(':quantity', $quantity, PDO::PARAM_STR);
$track->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$track->bindParam(':service', $service, PDO::PARAM_STR);
$track->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$track -> execute();
http_response_code(200);
		$response['error']= false;
	$response['message']= "Service is added";
	$response['admissionno']= $admissionno;
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please try again";
	$response['admissionno']= $admissionno;
}
//
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
	//
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
$pdo4 = null;
$pdoread = null;
?>