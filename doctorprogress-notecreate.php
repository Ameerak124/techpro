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
$ipno = $data->ipno;
$template_name = $data->template_name;
$progressnot = $data->progressnot;
try {
if(!empty($accesskey)&&!empty($ipno)&&!empty($template_name)&&!empty($progressnot)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$stmt = $pdo4 -> prepare("INSERT INTO `doctor_progressnote`( `ipno`, `template_name`, `progressnot`, `created_by`, `created_on`) VALUES (:ipno,:template_name,:progressnot,:userid,CURRENT_TIMESTAMP)");
$stmt->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$stmt->bindParam(':template_name', $template_name, PDO::PARAM_STR);
$stmt->bindParam(':progressnot', $progressnot, PDO::PARAM_STR);
$stmt->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);

$stmt -> execute();
if($stmt -> rowCount()>0){
		 http_response_code(200);
         $response['error']= false;
		 $response['message']="Successfully created";  
     }else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Not created";
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
	$response['message']= "Connection failed: " .$e->getMessage();
	}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>