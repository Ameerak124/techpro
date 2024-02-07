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
$response = array();
try{
if(!empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$stmt=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`billing_history`.`sno` AS track,`billing_history`.`ipno` AS admissionno,`registration`.`umrno`,`billing_history`.`billno` as receiptno,`registration`.`patientname`,`billing_history`.`subcategory`,`billing_history`.`servicecode`,`billing_history`.`services` AS services,`billing_history`.`quantity`,`billing_history`.`rate`,`billing_history`.`total` FROM (SELECT @a:=0) AS a,`billing_history` INNER JOIN `registration` ON `billing_history`.`ipno` = `registration`.`admissionno` WHERE `billing_history`.`status` = 'Visible' AND `billing_history`.`remarks` = '' AND `billing_history`.`category` = 'INVESTIGATIONS' ORDER BY `billing_history`.`ipno` DESC");
$stmt->execute();
if($stmt-> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$response['serviceutilizationlist'][] = $row;
	}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
	echo json_encode($response);
   unset($pdoread);
?>








