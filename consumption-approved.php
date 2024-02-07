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
$accesskey= $data->accesskey;
$sno= $data->sno;
$status= $data->status;
$response = array();
try{
if(!empty($accesskey) && !empty($sno)){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
if($status=="Approved"){
$stmt2=$pdo4->prepare("UPDATE `generate_consumption` SET `approved_by`=:userid,`approved_on`=CURRENT_TIMESTAMP,`status`=:status WHERE `sno`=:sno");
}else{
	$stmt2=$pdo4->prepare("UPDATE `generate_consumption` SET `approved_by`=:userid,`approved_on`=CURRENT_TIMESTAMP,`status`=:status WHERE `sno`=:sno");
}
$stmt2->bindParam(':sno', $sno, PDO::PARAM_STR);
$stmt2->bindParam(':status', $status, PDO::PARAM_STR);
$stmt2->bindParam(':userid', $emp['userid'], PDO::PARAM_STR);

$stmt2-> execute();
	if($stmt2 -> rowCount() > 0){
		
		 http_response_code(200);
         $response['error']= false;
		 $response['message']=$status." Successfully";
	   
     }else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Not ".$status;
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
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdo4);
   unset($pdoread);
?>
