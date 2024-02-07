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
$searchterm = trim($data->searchterm);
try {
if(!empty($accesskey) && !empty($searchterm)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `sno` AS track,`category`,`subcategory`,`servicecode`,`services`,`hsn_sac`,`quantity`,`rate`,`total` FROM `billing_history` WHERE `ipno` = :searchterm AND `credit_debit` LIKE 'CREDIT' AND `status` = 'Hold'");
$reglist->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
$reglist -> execute();
if($reglist -> rowCount() > 0){
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";

while($result = $reglist->fetch(PDO::FETCH_ASSOC)){
	$response['finalsummarylist'][] = $result;
	}
	}else{
		http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
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
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>