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
$accesskey = $data->accesskey;
$answer = $data->answer;
$sno = $data->qsno;
$consent_id = $data->consent_id;
$response = array();
try{
if(!empty($accesskey) && !empty($answer) && !empty($sno)){
	$stmt = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$stmt->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$stmt -> execute();
	if($stmt->rowCount()>0){
	 $row = $stmt->fetch(PDO::FETCH_ASSOC);
	$empid = $row['userid'];
	$snoo=explode(',',$sno);	
	$ratings=explode(',',$answer);	
    foreach($snoo as $key=>$snoos) {
	$stmt1=$pdo4->prepare("INSERT INTO `consent_answers_report`( consentid,`qsno`, `answer`, `created_by`, `created_on`) VALUES (:consent_id,:sno,:answer,:empid,CURRENT_TIMESTAMP)");
	$stmt1->bindParam(":sno", $snoos, PDO::PARAM_STR);
	$stmt1->bindParam(":empid", $empid, PDO::PARAM_STR);
	$stmt1->bindParam(":answer",$ratings[$key], PDO::PARAM_STR);
	$stmt1->bindParam(":consent_id",$consent_id, PDO::PARAM_STR);
	$stmt1->execute(); 
}
if($stmt1->rowCount()>0){ 

	http_response_code(200);
		$response['error'] = false;
		$response['message']="Thank you";
	}
else{
		http_response_code(503);
		$response['error'] = true;
		$response['message']="Sorry something went wrong";
	}
}else{
		http_response_code(400);
		$response['error'] = true;
		$response['message']="Access denied!";
	}
}else{
	http_response_code(400);
	$response['error'] = true;
	$response['message']="Sorry some details missing";
}
}catch(PDOEXCEPTION $e){
	echo $e;
}
echo json_encode($response); 
unset($pdo4);
unset($pdoread);

?>