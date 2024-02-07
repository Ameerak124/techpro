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
$admissionno = strtoupper($data->searchterm);
$discharge_type = strtoupper($data->discharge_type);
$phar_return = strtoupper($data->phar_return);
$ambulance = strtoupper($data->ambulance);
$notes = strtoupper(str_replace("'","",$data->notes));
$remarks = strtoupper(str_replace("'","",$data->remarks));
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey) && !empty($admissionno) && !empty($discharge_type) && !empty($phar_return) && !empty($ambulance) && !empty($notes)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//Generate Bill number Start
$discharge = $pdo4 -> prepare("UPDATE `registration` SET `admissionstatus` = 'Initiated Discharge',`discharge_type` = :discharge_type,`phar_return` = :phar_return,`ambulance` = :ambulance,`dis_notes` = :notes,`dis_remarks` = :remarks,`dis_initiatedon` = CURRENT_TIMESTAMP,`modifiedby` = :userid,`modifiedon` =CURRENT_TIMESTAMP WHERE `admissionno` = :admissionno AND `status` = 'Visible'");
$discharge->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$discharge->bindParam(':discharge_type', $discharge_type, PDO::PARAM_STR);
$discharge->bindParam(':phar_return', $phar_return, PDO::PARAM_STR);
$discharge->bindParam(':ambulance', $ambulance, PDO::PARAM_STR);
$discharge->bindParam(':notes', $notes, PDO::PARAM_STR);
$discharge->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$discharge->bindParam(':ambulance', $ambulance, PDO::PARAM_STR);
$discharge->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$discharge -> execute();
if($discharge -> rowCount() > 0){
	$track = $pdo4 -> prepare("INSERT INTO `registration_track`(`sno`, `admissionno`, `track_code`, `trackname`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `status`) VALUES (NULL,':admission','2','Initiated Discharge',CURRENT_TIMESTAMP,':userid',CURRENT_TIMESTAMP,':userid','Active')");
	$track->bindParam(':admission', $admissionno, PDO::PARAM_STR);
	$track->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$track -> execute();
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Thank you! Initiated Discharge Process";
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! You are not allowed to do modifications";
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