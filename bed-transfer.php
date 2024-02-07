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
$response = array();
$accesskey = trim($_POST['accesskey']);
$admissionno = strtoupper($_POST['admissionno']);
$servicecode = strtoupper($_POST['servicecode']);
$servicename = strtoupper($_POST['servicename']);
$remarks = str_ireplace("'\'","/",$_POST['remarks']);
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
$pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
if(!empty($accesskey) && !empty($admissionno) && !empty($servicecode) && !empty($servicename) && !empty($remarks)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$SNO = $pdoread -> prepare("SELECT `sno` FROM `bed_transfer` WHERE `admissionno` = :admissionno AND `status` = 'Visible' AND `bed_status` = 'ON_BED'");
$SNO->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$SNO -> execute();
$ref = $SNO->fetch(PDO::FETCH_ASSOC);
// Insert
$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `bed_transfer`(`sno`, `admissionno`, `service_code`, `service_name`, `remarks`, `createdby`, `createdon`,`transferedby`,`transferedon`, `modifiedby`, `modifiedon`, `reference`, `bed_status`, `status`) VALUES (NULL,:admissionno,:servicecode,:servicename,:remarks,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:reference,'ON_BED','Visible')");
$saleprice->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$saleprice->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$saleprice->bindParam(':servicename', $servicename, PDO::PARAM_STR);
$saleprice->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$saleprice->bindParam(':reference', $ref['sno'], PDO::PARAM_STR);
$saleprice -> execute();
$insertedid = $con->lastInsertId();
if($saleprice -> rowCount() > 0){
$update = $pdo4 -> prepare("UPDATE `bed_transfer` SET `transferedby` = :userid,`transferedon` = CURRENT_TIMESTAMP,`modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP,`bed_status` = 'TRANSFERED',`reference` = :reference WHERE `sno` = :sno");
$update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update->bindParam(':sno', $ref['sno'], PDO::PARAM_STR);
$update->bindParam(':reference', $insertedid, PDO::PARAM_STR);
$update -> execute();
http_response_code(200);	
$response['error']= false;
$response['message']= "Bed transfered";
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! Please try again";
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