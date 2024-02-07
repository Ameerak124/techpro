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
$searchumr = trim($data->searchumr);
try {
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `umrno`, `category`, `vip_patient`, `country`, `patient_name`, `middle_name`, `last_name`,DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), patient_age)), '%Y') + 0 AS patient_age
, `patient_gender`,`mobile_no`,  `address`, `id_proof_number`, `organization_name`, `organization_code`, `umr_registration`.`remarks`, `umr_registration`.`branch`, `umr_registration`.`status` ,`payment_history`.`receiptno` ,`payment_history`.`billno` ,`payment_history`.`amount`  ,`payment_history`.`paymentmode`    FROM `umr_registration` INNER JOIN `payment_history` ON   `umr_registration`.`umrno` =  `payment_history`.`admissionon`  WHERE `umrno` Like :search AND umr_registration.`branch`=:costcenter AND `umr_registration`.`status`='Visible'  AND   `payment_history`.`bill_type`= 'registration'");
$reglist -> bindValue(":search", "%{$searchumr}%", PDO::PARAM_STR);
$reglist -> bindParam(":costcenter", $result['cost_center'], PDO::PARAM_STR);
$reglist -> execute();
if($reglist -> rowCount() > 0){
	    http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	while($regres = $reglist->fetch(PDO::FETCH_ASSOC)){
		$response['umrsearchlist'][] = $regres;
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
$pdo4 = null;
?>