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
$accesskey=trim($data->accesskey);
$patient_id=trim($data->patient_id);

try {
     if(!empty($accesskey)&& !empty($patient_id)){    

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$dlist=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`sno` AS id,`service_id`,`service`,'' AS service_group_code,`instruction` FROM (SELECT @a:=0) AS a,`doctor_service_suggestion` WHERE `status`='Active' AND `patient_id`=:patient_id AND `status`='Active'");
$dlist->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
$dlist -> execute();
if($dlist->rowCount()>0){
	$sn=0;
	http_response_code(200);
	$response['error'] = false;
	$response['message']= "Data Found";
	while($res=$dlist->fetch(PDO::FETCH_ASSOC)){
		$response['servicesearchitemlist'][$sn]['sno']=$res['sno'];
		$response['servicesearchitemlist'][$sn]['id']=$res['id'];
		$response['servicesearchitemlist'][$sn]['service_id']=$res['service_id'];
		$response['servicesearchitemlist'][$sn]['service']=$res['service'];
		$response['servicesearchitemlist'][$sn]['instruction']=$res['instruction'];
		$sn++;
	}
}else{
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "No Data Found";	
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
	$response['message']= "Connection failed";
}
echo json_encode($response);
$pdoread = null;
?>