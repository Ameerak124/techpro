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
//$billno = strtoupper($data->billno);
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey) && !empty($admissionno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Create Registration
$saleprice = $pdo4 -> prepare("UPDATE `billing_history` SET `status` = 'Visible',`modifiedby` = :userid,`modifiedon` = CURRENT_TIMESTAMP,`ipaddress` = :ipaddress WHERE `ipno` = :admissionno AND `status` = 'Hold'");
$saleprice->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
//$saleprice->bindParam(':billno', $billno, PDO::PARAM_STR);
$saleprice->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$saleprice -> execute();
if($saleprice -> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Indent Raised";
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Hope! It's already approved";
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
} catch(PDOException $e){
 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>