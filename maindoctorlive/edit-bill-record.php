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
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$track = strtoupper($data->track);
$qty = (int) ($data->qty);
/* $ipaddress = $_SERVER['REMOTE_ADDR']; */
try {
if(!empty($accesskey) && !empty($track) && !empty($qty)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Updated details
$saleprice = $pdo4 -> prepare("UPDATE `billing_history` SET `modifiedby`= :userid,`modifiedon`=CURRENT_TIMESTAMP,`quantity`= :qty,`total` = ROUND(:qty*`rate`,2) WHERE `sno` = :track AND `status` = 'Hold'");
$saleprice->bindParam(':track', $track, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
//$saleprice->bindParam(':ipaddress', $ipaddress , PDO::PARAM_STR);
$saleprice->bindParam(':qty', $qty , PDO::PARAM_STR);
$saleprice -> execute();
if($saleprice -> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	     $response['message']= "Details are updated";
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Sorry! you are not allowed to edit details";
}
// 
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied";
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