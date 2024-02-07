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
try {

if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `umrno`,DATE_FORMAT(`createdon`,'%d-%b-%Y %h:%i %p') AS createdon,`patient_name` AS patientname,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patient_age`)), '%Y')+0 AS Age,`patient_gender`,`mobile_no`,`email_id`,`city` FROM `umr_registration` WHERE `status` = 'Visible' ORDER BY `umrno` DESC");
//$reglist->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$reglist -> execute();
if($reglist -> rowCount() > 0){
	    http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	while($regres = $reglist->fetch(PDO::FETCH_ASSOC)){
		$response['umrlist'][] = $regres;
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