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
$status= $data->status;
$slno=$data->slno;
$returnno=$data->returnno;
$response = array();
$appr = 'Approved';
try{
if(!empty($accesskey) && !empty($status) && !empty($slno) && !empty($returnno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
$stmt2=$pdo4->prepare("UPDATE `generate_return` SET `status`='Audited' WHERE `sno` = :sno AND `return_id` = :returnno  AND `status` = :appr");
$stmt2 -> bindParam(":sno", $slno,PDO::PARAM_STR);
$stmt2 -> bindParam(":returnno", $returnno,PDO::PARAM_STR);
$stmt2 -> bindParam(":appr", $appr,PDO::PARAM_STR);
$stmt2-> execute();
	if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
		 $response['error']= false;
		 $response['message']="Approved";
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Something Went Wrong!";
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
