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
$branch=trim($data->branch);
$accesskey=trim($data->accesskey);
try {
if(!empty($accesskey)&& !empty($branch)){ 
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$grbs_fetch=$pdo4->prepare("UPDATE `user_logins` SET `cost_center`=:branch WHERE `userid`=:userid");
$grbs_fetch->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$grbs_fetch->bindParam(':branch', $branch, PDO::PARAM_STR);
$grbs_fetch->execute();
if($grbs_fetch->rowCount() > 0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Updated";
	
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Already updated";
}
}else {
     http_response_code(400);	
     $response['error'] = true;
	$response['message']= "Access denied!";
}
}else {
	http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed" .$e->getmessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>