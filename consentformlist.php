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
$ipno= $data->ipno;
$response = array();
try{
if(!empty($accesskey) && !empty($ipno)){
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$stmt2=$pdoread->prepare("SELECT consent_patient_data.`consent_id`, consent_form_master.title,consent_patient_data.transid,consent_patient_data.status FROM `consent_patient_data` inner join consent_form_master on consent_patient_data.consent_id=consent_form_master.consent_id where consent_patient_data.status='ACTIVE' AND consent_patient_data.ipno=:ipno");
	$stmt2->bindParam(':ipno', $ipno, PDO::PARAM_STR);
	$stmt2 -> execute(); 
	if($stmt2 -> rowCount() > 0){
	$result = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
	
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
	    $response['consentformlist']=$result;
	} else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="No Data Found!";
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
} 
catch(Exception $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdoread = null;
?>