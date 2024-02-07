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
$cp_sno = trim($data->cp_sno);
$ipno = trim($data->ipno);
$usage = trim($data->usage);
//$ipaddress = $_SERVER['REMOTE_ADDR'];
try {

if(!empty($accesskey) && !empty($cp_sno) && !empty($ipno)) {
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$admission_status = $pdoread->prepare("SELECT * FROM `registration` WHERE `admissionstatus` IN ('Admitted','Initiated Discharge') AND `admissionno` = :ipno");
$admission_status->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$admission_status -> execute();
if($admission_status -> rowCount() > 0){
$update = $pdo4->prepare("UPDATE `doctor_mediciation` SET `modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP,`stop_medication` = :usage WHERE `sno` = :cp_sno AND `vstatus` = 'Active' AND `source` = 'IPD' AND `billno` =:ipno");
$update->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$update->bindParam(':cp_sno', $cp_sno, PDO::PARAM_STR);
$update->bindParam(':usage', $usage, PDO::PARAM_STR);
$update->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$update -> execute();
if($update -> rowCount() > 0){
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data updated";
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']= "Sorry! No data updated";
}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']= "Sorry! Changed are not allowed";
}
//
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
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