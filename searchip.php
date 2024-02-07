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
if($accesskey == ''){
$accesskey = strtoupper($data->accesskey);
$ipno = strtoupper($data->ipno);
}
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey) && !empty($ipno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
//$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Create Registration
$saleprice = $pdoread -> prepare("SELECT `admissionno`,`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes` FROM `registration` WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND `status` = 'Visible' ORDER BY `admissionno` DESC");
$saleprice -> bindValue(":search", "%{$ipno}%", PDO::PARAM_STR);
$saleprice -> execute();
if($saleprice -> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	while($result = $saleprice->fetch(PDO::FETCH_ASSOC)){
		$response['searchiplist'][] = $result;
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
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
$pdoread = null;
?>