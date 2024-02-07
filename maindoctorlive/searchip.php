<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = strtoupper($data->accesskey);
$ipno = strtoupper($data->ipno);
try {

if(!empty($accesskey) && !empty($ipno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
//$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	$validres = $check->fetch(PDO::FETCH_ASSOC);
// Create Registration
$saleprice = $pdoread -> prepare("SELECT `admissionno`,`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,`patientage` as dob,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,CONCAT('Dr. ',`consultantname`) AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`, `consultantcode`,(CASE WHEN `sponsor_name` IN ('','No Update') THEN 'Cash' ELSE `sponsor_name` END) AS organisation_name,CONCAT(DATEDIFF(CURRENT_TIMESTAMP,`admittedon`),' days') AS los, DATE(`admittedon`) AS admitted, CURRENT_DATE AS discharge FROM `registration` WHERE (`admissionno` LIKE :search || `umrno` LIKE :search)  AND `admissionstatus` != 'Discharged' AND `status` = 'Visible' AND `admissionno` !='No Update'  AND `cost_center` =:cost_center GROUP BY `admissionno` ORDER BY `admissionno` DESC");
$saleprice -> bindValue(":search", "%{$ipno}%", PDO::PARAM_STR);
$saleprice -> bindParam(":cost_center", $validres['cost_center'], PDO::PARAM_STR);
$saleprice -> execute();
if($saleprice -> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	while($result = $saleprice->fetch(PDO::FETCH_ASSOC)){
		$response['iplist'][] = $result;
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