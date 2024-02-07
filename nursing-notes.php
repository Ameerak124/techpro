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
$notes = str_ireplace("'","",strtoupper($data->notes));
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey) && !empty($admissionno) && !empty($notes)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){

//Generate Bill number Start
$admissioncheck = $pdoread -> prepare("SELECT `sno` FROM `registration` WHERE `admissionno` = :admissionno AND `admissionstatus` = 'Initiated Discharge' AND `status` = 'Visible'");
$admissioncheck->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$admissioncheck -> execute();
if($admissioncheck -> rowCount() > 0){
$updatebillno = $pdo4 -> prepare("UPDATE `registration` SET `nursing_notes` = :notes,`nurse_createdby` = :userid,`nurser_createdon` = CURRENT_TIMESTAMP WHERE `admissionno` = :admissionno AND `admissionstatus` = 'Initiated Discharge' AND `status` = 'Visible'");
$updatebillno->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$updatebillno->bindParam(':notes', $notes, PDO::PARAM_STR);
$updatebillno->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$updatebillno -> execute();

if($updatebillno -> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Data Saved";
	$response['nursing_notes']= $notes;
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please try again";
}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! You are not allowed to do the changes";
}
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