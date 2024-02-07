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
$accesskey = $data->accesskey;
$sno = $data->sno;
try {
if(!empty($accesskey)&& !empty($sno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empid = $result['empid'];
if($check -> rowCount() > 0){
$stmt= $pdo4->prepare("UPDATE `assessment_templates` SET `status`='Inactive' WHERE `sno` =:sno AND `status` = 'active'");
$stmt->bindParam(':sno', $sno, PDO::PARAM_STR);
//$stmt->bindParam(':empid', $empid, PDO::PARAM_STR);
$stmt -> execute();
if($stmt -> rowCount()>0){
		 http_response_code(200);
         $response['error']= false;
		 $response['message']=" Data Deleted";
	   
     }else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No data deleted";
     }
}else{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}else{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: ".$e ;
	
	}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>