<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$cpsno = trim($data->cpsno);
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey) && !empty($cpsno)){

//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Create Registration
		$checkq = $pdo4->prepare("UPDATE `surgery_advise` SET `modifiedby`=:userid,`modified_on`= CURRENT_TIMESTAMP,`estatus`='I' WHERE `sno` = :cpsno AND `estatus` = 'A'");
		$checkq->bindParam(':cpsno', $cpsno, PDO::PARAM_STR);
		$checkq->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
		$checkq -> execute();
		if($checkq -> rowCount() > 0){
		http_response_code(200);
		$response['error']= false;
	$response['message']= "Data deleted";
	}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data deleted. Please try again";
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