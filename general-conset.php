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
$patienttype= $data->patienttype;
$psignature= $data->psignature;
$dsignature= $data->dsignature;
$patientphoto= $data->patientphoto;
$ipno=$data->ipno;
$attender_name=$data->attender_name;
$response = array();
try{
if(!empty($accesskey) && !empty($patienttype) && !empty($psignature) && !empty($dsignature)  && !empty($ipno)){
	if($patienttype=="PA" && !empty($attender_name)){
		 http_response_code(400);
         $response['error']= true;
	    $response['message']="Please Enter Attender Name";
	}else{
	$check = $pdoread -> prepare("SELECT `userid`,`department` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	$stmt1 = $pdo4->prepare("UPDATE `registration` SET `patient_signature_type`=:patienttype, `patient_signature`=:psignature, `patient_signature_on`=CURRENT_TIMESTAMP, `employee_signature`=:dsignature, `employee_signature_on`=CURRENT_TIMESTAMP,attender_name=:attender_name,patient_photo=:patientphoto where `admissionno`=:ipno AND `patient_signature`=''");
	$stmt1->bindParam(':patientphoto', $patientphoto, PDO::PARAM_STR);
	$stmt1->bindParam(':psignature', $psignature, PDO::PARAM_STR);
	$stmt1->bindParam(':dsignature', $dsignature, PDO::PARAM_STR);
	$stmt1->bindParam(':patienttype', $patienttype, PDO::PARAM_STR);
	$stmt1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
	$stmt1->bindParam(':attender_name', $attender_name, PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){
         http_response_code(200);
        $response['error']= false;
	    $response['message']="Sucessfully updated";
      }
	 else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="Already submitted";
     }

}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
	}	
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
$pdo4 = null;
$pdoread = null;
?>